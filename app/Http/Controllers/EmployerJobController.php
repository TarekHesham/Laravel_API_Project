<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Users\EmployerJobResource;
use App\Models\Jobs\Job;

class EmployerJobController extends Controller
{
    /**
     * Get all jobs of the current employer
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        // Check if the user is authenticated
        if (Auth::guest()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Check if the user is an employer
        if (Auth::user()->role !== 'employer') {
            return response()->json(['message' => 'Unauthorized, not an employer'], 401);
        }

        // Check if the user has any jobs
        if (Auth::user()->jobs->count() === 0) {
            return response()->json(['message' => 'You have no jobs'], 404);
        }

        // Return the jobs of the current employer
        return EmployerJobResource::collection(Auth::user()->jobs)->resolve();
    }

    /**
     * Cancel a job of the current employer
     *
     * @param int $jobId The id of the job to cancel
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancelJob($jobId)
    {
        // Check if the user can cancel the job
        if (!Auth::user()->can('cancel', Job::class)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        // Get the current employer
        $employer = Auth::user();

        // Get the job
        $job = $employer->jobs()->where('job_listing_id', $jobId)->firstOrFail();

        // Check if the current employer is the owner of the job
        if ($job->employer_id !== $employer->id) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Check if the job has already been canceled
        if ($job->pivot->status === 'canceled') {
            return response()->json(['message' => 'Job has already been canceled'], 400);
        }

        // Update the status of the job
        $job->update(['status' => 'closed']);
        $job->pivot->update(['status' => 'canceled']);

        // Return the canceled job
        return response()->json([
            'message' => 'Job has been successfully canceled',
            'job' => new EmployerJobResource($job)
        ]);
    }
}
