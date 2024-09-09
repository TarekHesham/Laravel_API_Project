<?php

namespace App\Http\Controllers\Jobs;

use App\Http\Controllers\Controller;
use App\Models\Jobs\Job;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = Job::query();

        // Filter jobs based on user role
        switch ($request->user()->role) {
            case 'candidate':
                $query->where('status', 'open');
                break;
            case 'employer':
                $query->where('status', 'open')->orWhere('employer_id', $request->user()->id);
                break;
        }

        if ($request->filled('query')) {
            $query->where(function ($q) use ($request) {
                $q->where('job_title', 'LIKE', '%' . $request->query('query') . '%')
                    ->orWhere('description', 'LIKE', '%' . $request->query('query') . '%');
            });
        }

        if ($request->filled('location')) {
            $query->whereHas('location', function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->query('location') . '%');
            });
        }

        if ($request->filled('category')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->query('job_category') . '%');
            });
        }

        if ($request->filled('experience_level')) {
            $query->where('experience_level', $request->query('experience_level'));
        }

        if ($request->filled('salary_from')) {
            $query->where('salary_from', '>=', $request->query('salary_from'));
        }

        if ($request->filled('salary_to')) {
            $query->where('salary_to', '<=', $request->query('salary_to'));
        }

        if ($request->filled('work_type')) {
            $query->where('work_type', '=', $request->query('work_type'));
        }

        if ($request->filled('created_at')) {
            $timeFrame = $request->query('created_at');

            switch ($timeFrame) {
                case 'day':
                    $query->where('created_at', '>=', Carbon::now()->subDay());
                    break;
                case 'week':
                    $query->where('created_at', '>=', Carbon::now()->subWeek());
                    break;
                case 'month':
                    $query->where('created_at', '>=', Carbon::now()->subMonth());
                    break;
                case 'year':
                    $query->where('created_at', '>=', Carbon::now()->subYear());
                    break;
            }
        }

        $jobs = $query->get();

        return response()->json($jobs);
    }
}
