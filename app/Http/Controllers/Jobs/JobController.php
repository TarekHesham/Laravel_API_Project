<?php

namespace App\Http\Controllers\Jobs;

use App\Models\Jobs\Job;
use App\Models\Jobs\JobBenefit;
use App\Models\Jobs\JobCategory;
use App\Models\Jobs\JobSkill;
use App\Http\Resources\Jobs\JobResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JobController extends Controller
{
    /**
     * Get all open job postings
     *
     * @return \Illuminate\Database\Eloquent\Collection|Job[]
     */
    public function index()
    {
        return Job::where('status', 'open')->get();
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

        $request_data = $request->all();

        // Validate the input
        $job_validator = Validator::make($request_data, [
            'job_title' => 'required|string',
            'description' => 'required|string',
            'deadline' => 'required|date',
            'experience_level' => 'required|string|in:entry_level,intermediate,expert',
            'salary_from' => 'required|integer',
            'salary_to' => 'required|integer|gt:salary_from',
            'work_type' => 'required|string|in:remote,onsite,hybrid',
            'location_id' => 'required|integer',
            'skills' => 'array|exists:skills,id',
            'benefits' => 'array|exists:benefits,id',
            'categories' => 'array|exists:categories,id',
        ]);

        // Return a 422 error if validation fails
        if ($job_validator->fails()) {
            return response()->json([
                "message" => "Errors with your request",
                "errors" => $job_validator->errors()
            ], 422);
        }

        // Add the authenticated user's id to the request data
        $request_data['employer_id'] = $request->user()->id;

        // Store the new job posting
        $job = Job::create($request_data);

        if (isset($request_data['skills'])) {
            foreach ($request_data['skills'] as $skill) {
                JobSkill::create([
                    'job_listing_id' => $job->id,
                    'skill_id' => $skill
                ]);
            }
        }

        if (isset($request_data['benefits'])) {
            foreach ($request_data['benefits'] as $benefit) {
                JobBenefit::create([
                    'job_listing_id' => $job->id,
                    'benefit_id' => $benefit
                ]);
            }
        }

        if (isset($request_data['categories'])) {
            foreach ($request_data['categories'] as $category) {
                JobCategory::create([
                    'job_listing_id' => $job->id,
                    'category_id' => $category
                ]);
            }
        }

        // Return a 201 response with the new job posting
        return response()->json([
            'message' => 'Job created successfully',
            'job' => $job
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

        // Validate the input
        $validatedData = $request->validate([
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
        ]);

        // Update the job posting without the related data
        $job->update($validatedData);

        // Sync the relationships
        if (isset($validatedData['skills'])) {
            $job->skills()->sync($validatedData['skills']);
        }

        if (isset($validatedData['benefits'])) {
            $job->benefits()->sync($validatedData['benefits']);
        }

        if (isset($validatedData['categories'])) {
            $job->categories()->sync($validatedData['categories']);
        }

        // Return a 200 response with the updated job posting
        return response()->json([
            'message' => 'Job updated successfully',
            'job' => $job->load('skills', 'benefits', 'categories')
        ], 200);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Jobs\Job $job
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Job $job)
    {
        $this->authorize('delete', $job);

        $job->delete();

        return response()->json(['message' => 'Job deleted successfully']);
    }
}
