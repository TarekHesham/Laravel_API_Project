<?php

namespace App\Http\Controllers\Jobs;

use App\Http\Controllers\Controller;
use App\Models\Dependency\Categories;
use App\Models\Dependency\Location;
use App\Models\Dependency\Skills;
use App\Models\Jobs\Job;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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

    public function autocomplete(Request $request)
    {
        // Validator
        $validator = Validator::make($request->all(), [
            'query' => 'required|string',
            'searchtype' => 'required|string|in:skill,skills,location,locations,category,categories'
        ]);

        // Validate
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Search
        $searchType = $request['searchtype'];
        switch ($searchType) {
            case 'skill':
            case 'skills':
                $query = Skills::query();
                $query->where('name', 'LIKE', '%' . $request->query('query') . '%');
                break;
            case 'location':
            case 'locations':
                $query = Location::query();
                $query->where('name', 'LIKE', '%' . $request->query('query') . '%');
                break;
            case 'category':
            case 'categories':
                $query = Categories::query();
                $query->where('name', 'LIKE', '%' . $request->query('query') . '%');
                break;
            default:
                return response()->json([
                    'message' => 'Invalid search type'
                ], 422);
                break;
        }

        // Get results
        $results = $query->limit(5)->select('id', 'name')->get();
        return response()->json($results, 200);
    }
}
