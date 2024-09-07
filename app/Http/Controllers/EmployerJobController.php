<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Users\EmployerJobResource;
use App\Models\Jobs\Job;

class EmployerJobController extends Controller
{

    public function index()
    {
        if (Auth::guest()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        } else if (Auth::user()->role !== 'employer') {
            return response()->json(['message' => 'Unauthorized, not an employer'], 401);
        }
        return EmployerJobResource::collection(Auth::user()->jobs)->resolve();
    }

    public function cancelJob($jobId)
    {
        // جلب المستخدم (employer) المسجل
        $employer = Auth::user();

        // جلب الوظيفة من خلال علاقة jobs في جدول employer_jobs
        $job = $employer->jobs()->where('job_listing_id', $jobId)->firstOrFail();

        // تحديث حالة الوظيفة في جدول job_listings إلى 'closed'
        $job->update(['status' => 'closed']);

        // تحديث حالة الوظيفة في جدول employer_jobs إلى 'canceled'
        $job->pivot->update(['status' => 'canceled']);

        return response()->json([
            'message' => 'Job has been successfully canceled',
            'job' => new EmployerJobResource($job)
        ]);
    }
}
