<?php

namespace App\Http\Controllers\Jobs;

use App\Models\Jobs\Job;
use App\Http\Resources\Jobs\JobResource;
use App\Http\Controllers\Controller;
use App\Models\Dependency\Benefits;
use App\Models\Dependency\Categories;
use App\Models\Dependency\Skills;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class JobController extends Controller
{
    /**
     * Get all open job postings
     *
     * @return \Illuminate\Database\Eloquent\Collection|Job[]
     */
    public function index()
    {
        $jobs = Job::where('status', 'open')->get();
        return JobResource::collection($jobs)->resolve();
    }

    /**
     * Store a newly created job posting in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Job $job)
    {
        // Only allow authenticated users to store a job posting
        if (!$request->user()->can('create', $job)) {
            // Return a 403 error if the user doesn't have permission
            return response()->json([
                'error' => 'You do not have permission to create job, only employers can create jobs'
            ], 403);
        }

        // Validate the input
        $validatedData = $request->validate([
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
        ]);

        // Add the user as the employer
        $validatedData['employer_id'] = $request->user()->id;
        
        DB::beginTransaction();

        // Create a new job listing
        // refresh to return with the default values instead of null
        $job = Job::create($validatedData)->refresh();

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
        DB::commit();
        // Return success response
        return response()->json([
            'message' => 'Job listing created successfully',
            'job' => new JobResource($job->load('skills', 'benefits', 'categories'))
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
        }

        // Update the job listing
        $job->update($validatedData);
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

        // Return success response
        return response()->json([
            'message' => 'Job listing updated successfully',
            'job' => $job->load('skills', 'benefits', 'categories')
        ], 200);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Jobs\Job $job
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, Job $job)
    {
        // Check if the authenticated user has permission to delete the job
        if (!$request->user()->can('delete', $job)) {
            return response()->json([
                'error' => 'You do not have permission to delete this job'
            ], 403);
        }

        // Detach the related data
        $job->skills()->detach();
        $job->benefits()->detach();
        $job->categories()->detach();
        $job->comments()->delete();

        // Delete the job listing
        $job->delete();

        // Return success response
        return response()->json([
            'message' => 'Job listing deleted successfully'
        ], 200);
    }

    /**
     * Handle entities: Check if entities exist, create if not, and return their IDs.
     */
    private function handleEntities(array $ids, string $modelClass, string $key = 'id', string $jobId): array
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
