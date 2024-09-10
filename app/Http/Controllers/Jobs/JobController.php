<?php

namespace App\Http\Controllers\Jobs;

use App\Models\Jobs\Job;
use App\Models\Users\EmployerJob;
use App\Models\Jobs\JobImage;
use App\Http\Resources\Jobs\JobResource;
use App\Models\Dependency\Benefits;
use App\Models\Dependency\Categories;
use App\Models\Dependency\Skills;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\Storage;

class JobController extends Controller
{
    /**
     * Get all open job postings
     *
     * @return \Illuminate\Database\Eloquent\Collection|Job[]
     */
    public function index(Request $request)
    {
        $jobs = [];

        if ($request->user()->isAdmin()) {
            $jobs = Job::select('id', 'job_title', 'description', 'number_of_applications', 'status', 'work_type', 'experience_level', 'location_id', 'employer_id')
                ->with([
                    'location:id,name',
                    'employer:id,name'
                ])
                ->get();
            return response()->json($jobs);
        } else if ($request->user()->isEmployer()) {
            $jobs = Job::select('id', 'job_title', 'description', 'number_of_applications', 'location_id', 'work_type', 'experience_level')
                ->where('status', 'open')->orWhere('employer_id', $request->user()->id)
                ->with('location:id,name')
                ->get();
        } else if ($request->user()->isCandidate()) {
            $jobs = Job::select('id', 'job_title', 'description', 'number_of_applications', 'location_id', 'work_type', 'experience_level')
                ->where('status', 'open')
                ->with('location:id,name')
                ->get();
        }

        if ($jobs->isEmpty()) {
            return response()->json([
                'message' => 'No open jobs found'
            ], 404);
        }

        return response()->json($jobs);
    }


    /**
     * Store a newly created job posting in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Job $job): JsonResponse
    {
        // Only allow authenticated users to store a job posting
        if (!$request->user()->can('create', $job)) {
            // Return a 403 error if the user doesn't have permission
            return response()->json([
                'error' => 'You do not have permission to create job, only employers can create jobs'
            ], 403);
        }
        $validatedData = $request->all();
        // Validate the input
        $validator = Validator::make($validatedData, [
            'job_title' => 'string|required',
            'description' => 'string|required',
            'deadline' => 'date|required',
            'experience_level' => 'string|required',
            'salary_from' => 'integer|required',
            'salary_to' => 'integer|gt:salary_from|required',
            'work_type' => 'string|in:remote,onsite,hybrid|required',
            'location_id' => 'integer|exists:locations,id|required',
            'skills' => 'array|exists:skills,id',
            'benefits' => 'array|exists:benefits,id',
            'categories' => 'array|exists:categories,id',
            'images.*' => 'image|mimes:jpeg,jpg,png,gif|max:5000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        };

        // Add the user as the employer
        $validatedData['employer_id'] = $request->user()->id;

        DB::beginTransaction();

        try {
            // Create a new job listing
            // refresh to return with the default values instead of null
            $job = Job::create($validatedData)->refresh();
            $employer_job = EmployerJob::create([
                'employer_id' => $request->user()->id,
                'job_listing_id' => $job->id,
                'created_at' => now()
            ]);

            if ($request->hasFile('images')) {
                $images = $request->file('images');

                foreach ($images as $image) {
                    // Get the image path
                    $image_path = $image->store("", 'job_images');

                    // Save the image path in the database
                    JobImage::create([
                        'image' => $image_path,
                        'job_listing_id' => $job->id,
                    ]);
                }
            }

            // Handle skills
            if (isset($validatedData['skills'])) {
                $skillIds = $this->handleEntities($validatedData['skills'], Skills::class, 'job_skills', $job->id);
                $job->skills()->sync($skillIds);
            }

            // Handle benefits
            if (isset($validatedData['benefits'])) {
                $benefitIds = $this->handleEntities($validatedData['benefits'], Benefits::class, 'job_benefits', $job->id);
                $job->benefits()->sync($benefitIds);
            }

            // Handle categories
            if (isset($validatedData['categories'])) {
                $categoryIds = $this->handleEntities($validatedData['categories'], Categories::class, 'job_category', $job->id);
                $job->categories()->sync($categoryIds);
            }
        } catch (Exception $errors) {
            DB::rollBack();
            return response()->json([
                'message' => $errors->getMessage()
            ], 500);
        }

        DB::commit();

        // Return success response
        return response()->json([
            'message' => 'Job listing created successfully',
            'job' => new JobResource($job->load('skills', 'benefits', 'categories', 'images')),
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Jobs\Job $job
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, Job $job): JsonResponse
    {
        // Check if the authenticated user has permission to view the job
        if (!$request->user()->can('view', $job)) {
            // Return a 403 error if the user doesn't have permission
            return response()->json([
                'error' => 'You do not have permission to view this job'
            ], 403);
        }

        if (!$job) {
            return response()->json([
                'message' => 'Job not found'
            ], 404);
        }

        // Return the job posting as JSON
        return response()->json(new JobResource($job));
    }

    /**
     * Update the specified job posting in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Jobs\Job $job
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Job $job): JsonResponse
    {
        // Check if the authenticated user has permission to update the job
        if (!$request->user()->can('update', $job)) {
            return response()->json([
                'error' => 'You do not have permission to update this job'
            ], 403);
        }

        $validatedData = $request->all();

        // Create a validator instance
        $validator = Validator::make($validatedData, [
            'job_title' => 'string',
            'description' => 'string',
            'deadline' => 'date',
            'experience_level' => 'string|in:entry_level,intermediate,expert',
            'salary_from' => 'integer',
            'salary_to' => 'integer|gt:salary_from',
            'work_type' => 'string|in:remote,onsite,hybrid',
            'location_id' => 'integer',
            'skills' => 'array|exists:skills,id',
            'benefits' => 'array|exists:benefits,id',
            'categories' => 'array|exists:categories,id',
            'images.*' => 'image|mimes:jpeg,jpg,png,gif|max:5000',
        ], [
            'job_title.string' => 'The job title must be a string.',
            'description.string' => 'The description must be a string.',
            'deadline.date' => 'The deadline must be a valid date.',
            'experience_level.in' => 'The experience level must be one of the following: entry_level, intermediate, expert.',
            'salary_from.integer' => 'The salary from must be an integer.',
            'salary_to.integer' => 'The salary to must be an integer.',
            'salary_to.gt' => 'The salary to must be greater than salary from.',
            'work_type.in' => 'The work type must be one of the following: remote, onsite, hybrid.',
            'location_id.integer' => 'The location id must be an integer.',
            'skills.array' => 'The skills must be an array.',
            'skills.exists' => 'One or more skills do not exist.',
            'benefits.array' => 'The benefits must be an array.',
            'benefits.exists' => 'One or more benefits do not exist.',
            'categories.array' => 'The categories must be an array.',
            'categories.exists' => 'One or more categories do not exist.',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        };

        // Start a new database transaction
        DB::beginTransaction();

        try {
            // Update the job listing
            $job->update($validatedData);

            if ($request->hasFile('images')) {
                $new_images = $request->file('images');
                $stored_images = $job->images()->pluck('image')->toArray();
                $new_image_paths = [];

                foreach ($new_images as $image) {
                    $image_path = $image->store("", 'job_images');
                    $new_image_paths[] = $image_path;

                    if (!in_array($image_path, $stored_images)) {
                        JobImage::create([
                            'image' => $image_path,
                            'job_listing_id' => $job->id,
                        ]);
                    }
                }

                foreach ($stored_images as $stored_image) {
                    if (!in_array($stored_image, $new_image_paths)) {
                        JobImage::where('image', $stored_image)->where('job_listing_id', $job->id)->delete();
                        Storage::disk('job_images')->delete($stored_image);
                    }
                }
            }

            // Handle skills
            if (isset($validatedData['skills'])) {
                $skillIds = $this->handleEntities($validatedData['skills'], Skills::class, 'job_skills', $job->id);
                $job->skills()->sync($skillIds);
            }

            // Handle benefits
            if (isset($validatedData['benefits'])) {
                $benefitIds = $this->handleEntities($validatedData['benefits'], Benefits::class, 'job_benefits', $job->id);
                $job->benefits()->sync($benefitIds);
            }

            // Handle categories
            if (isset($validatedData['categories'])) {
                $categoryIds = $this->handleEntities($validatedData['categories'], Categories::class, 'job_category', $job->id);
                $job->categories()->sync($categoryIds);
            }
        } catch (Exception $errors) {
            DB::rollBack();
            return response()->json([
                'message' => $errors->getMessage()
            ], 500);
        }

        // Commit the transaction
        DB::commit();

        // Return success response
        return response()->json([
            'message' => 'Job listing updated successfully',
            'job' => new JobResource($job->load('skills', 'benefits', 'categories', 'images')),
        ], 200);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Jobs\Job $job
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, Job $job): JsonResponse
    {
        // Check if the authenticated user has permission to delete the job
        if (!$request->user()->can('delete', $job)) {
            return response()->json([
                'error' => 'You do not have permission to delete this job'
            ], 403);
        }

        // Start a new database transaction
        DB::beginTransaction();

        try {
            // Delete the job images
            if ($job->images) {
                foreach ($job->images as $image) {
                    Storage::disk('job_images')->delete($image);
                }
            }
            // Detach the related database tables
            $job->skills()->detach();
            $job->benefits()->detach();
            $job->categories()->detach();
            $job->comments()->delete();

            // Delete the job listing
            $job->delete();
        } catch (Exception $errors) {
            DB::rollBack();
            return response()->json([
                'message' => $errors->getMessage()
            ], 500);
        }

        // Commit the transaction
        DB::commit();

        // Return success response
        return response()->json([
            'message' => 'Job listing deleted successfully'
        ], 200);
    }

    public function showBySlug($slug)
    {
        $job = Job::where('slug', $slug)->firstOrFail();

        return response()->json(new JobResource($job->load('skills', 'benefits', 'categories', 'images')), 200);
    }

    public function acceptReject(Request $request, Job $job): JsonResponse
    {
        if (!$request->user()->can('acceptReject', $job)) {
            // Return a 403 error if the user doesn't have permission
            return response()->json([
                'error' => 'You do not have permission to accept or reject this job'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:accepted,rejected'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Start a new database transaction
        DB::beginTransaction();

        try {
            // Update the job status in 'jobs_listing' table
            $job->update([
                'status' => $request->status == 'accepted' ? 'open' : 'closed'
            ]);

            // Update the status in 'employer_jobs' table
            EmployerJob::where('job_listing_id', $job->id)
                ->update([
                    'status' => $request->status == 'accepted' ? 'accepted' : 'rejected',
                    'updated_at' => now(),
                ]);

            // Commit the transaction
            DB::commit();

            return response()->json([
                'message' => "Job {$request->status} successfully.",
                'data' => new JobResource($job)
            ], 200);
        } catch (Exception $e) {
            // Rollback the transaction if something goes wrong
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to update job status.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle entities: Check if entities exist, create if not, and return their IDs.
     */
    private function handleEntities(array $ids, string $modelClass, string $key, string $jobId): array
    {
        $model = new $modelClass;
        $existingIds = $model::whereIn("id", $ids)->pluck("id")->toArray();
        $newIds = array_diff($ids, $existingIds);

        foreach ($newIds as $id) {
            $model::create([$key => $id, 'job_listing_id' => $jobId]);
        }

        return $ids;
    }
}
