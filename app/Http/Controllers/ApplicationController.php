<?php

namespace App\Http\Controllers;

use App\Models\Users\Application;
use App\Models\User;
use App\Http\Resources\ApplicationResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->user()->cannot('viewAny', Application::class)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        return ApplicationResource::collection(Application::all());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Application $application)
    {
        if (!$request->user()->can('create', $application)) {
            // Return a 403 error if the user doesn't have permission
            return response()->json([
                'error' => 'You do not have permission to apply for a job, only candidates can apply for jobs'
            ], 403);
        }

        // Validate the input
        $validatedData = $request->validate([
            'type' => 'required|string',
            'job_id' => 'required|exists:job_listings,id',
        ]);

        // Add the user as the candidate
        $validatedData['candidate_id'] = $request->user()->id;

        dd($validatedData);
    }

    /**
     * Display the specified resource.
     */
    public function show(Application $application)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Application $application)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Application $application)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Application $application)
    {
        //
    }
}
