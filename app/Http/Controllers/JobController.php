<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function index()
    {
        return Job::all();
    }

    public function store(Request $request)
    {
        $this->authorize('create', Job::class);

        $request->validate([
            'job_title' => 'required|string',
            'description' => 'required|string',
            'location_id' => 'required|integer',
            'deadline' => 'required|date',
            'experience_level' => 'required|string|in:entry_level,intermediate,expert',
            'salary_from' => 'required|integer',
            'salary_to' => 'required|integer|gt:salary_from',
            'work_type' => 'required|string|in:remote,onsite,hybrid',
            'deadline' => 'required|date',
            'location_id' => 'required|integer',
            'employer_id' => 'required|integer',
        ]);

        $request_data = $request->all();
        $job = Job::create([
            'job_title' => $request->job_title,
            'description' => $request->description,
            'location_id' => $request->location_id,
            'deadline' => $request->deadline,
            'employer_id' => $request->user()->id,
        ]);

        return response()->json($job);
    }

    public function show(Job $job)
    {
        return response()->json($job);
    }

    public function update(Request $request, Job $job)
    {
        $this->authorize('update', $job);

        $job->update($request->only(['job_title', 'description', 'location_id', 'deadline']));

        return response()->json($job);
    }

    public function destroy(Job $job)
    {
        $this->authorize('delete', $job);

        $job->delete();

        return response()->json(['message' => 'Job deleted successfully']);
    }
}
