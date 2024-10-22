<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApplicationResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Users\EmployerJobResource;
use App\Models\Jobs\Job;
use App\Models\Users\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Nette\Schema\Expect;

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
        return response()->json(EmployerJobResource::collection(Auth::user()->jobs), 200);
    }


    public function applicationsOnJob($slug, Request $request) {
        // get the job
        $job = $request->user()->jobs()->where('slug', $slug)->firstOrFail();
        return response()->json(["applications" => ApplicationResource::collection($job->applications)], 200);
    }
    
    public function acceptApplication(Application $application, Request $request) {
        // Check employer can accept application
        if (!$request->user()->can('accept', $application)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Check if the application has already been accepted
        if ($application->status === 'accepted') {
            return response()->json(['message' => 'Application has already been accepted'], 400);
        }

        // Start a new database transaction
        DB::beginTransaction();
        try {
            // Update the status of the application
            $application->update(['status' => 'accepted']);
            DB::commit();
            return response()->json(['message' => 'Application accepted'], 200);
        } catch (Expect $e) {
            // Rollback the database transaction
            DB::rollBack();
            return response()->json(['message' => 'Something went wrong'], 500);
        }
    }

    public function rejectApplication(Application $application, Request $request) {
        // Check employer can accept application
        if (!$request->user()->can('accept', $application)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Check if the application has already been rejected
        if ($application->status === 'rejected') {
            return response()->json(['message' => 'Application has already been rejected'], 400);
        }

        // Start a new database transaction
        DB::beginTransaction();
        try {
            // Update the status of the application
            $application->update(['status' => 'rejected']);
            DB::commit();
            return response()->json(['message' => 'Application rejected'], 200);
        } catch (Expect $e) {
            // Rollback the database transaction
            DB::rollBack();
            return response()->json(['message' => 'Something went wrong'], 500);
        }
    }

    /**
     * Cancel a job of the current employer
     *
     * @param int $jobId The id of the job to cancel
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancelJob($jobId, Request $request)
    {
        // Get the job
        $job = $request->user()->jobs()->where('job_listing_id', $jobId)->firstOrFail();

        // Check if the user can cancel the job
        if (!$request->user()->can('cancel', $job)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        
        // Check if the current employer is the owner of the job
        if ($job->employer_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Check if the job has already been canceled
        if ($job->pivot->status === 'cancelled') {
            return response()->json(['message' => 'Job has already been canceled'], 400);
        }

        // Start a new database transaction
        DB::beginTransaction();

        // Update the status of the job
        $job->update(['status' => 'closed']);
        $job->pivot->update(['status' => 'cancelled']);

        // end the database transaction
        DB::commit();
        
        // Return the canceled job
        return response()->json([
            'message' => 'Job has been successfully canceled',
            'job' => new EmployerJobResource($job)
        ]);
    }
}
