<?php

namespace App\Http\Controllers;

use App\Models\Users\Application;
use App\Http\Resources\ApplicationResource;
use App\Models\Users\CVApplication;
use App\Models\Users\FormApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;


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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!$request->user()->can('create', Application::class)) {
            // Return a 403 error if the user doesn't have permission
            return response()->json([
                'error' => 'You do not have permission to apply for a job, only candidates can apply for jobs'
            ], 403);
        }

        // Validate the input
        $validatedData = $request->validate([
            'type' => 'required|string|in:cv,form',
            'job_id' => 'required|exists:job_listings,id',
            'cv' => ['mimes:doc,pdf,docx', Rule::requiredIf(function () use ($request) {
                return $request->type == 'cv';
            })],
            'name' => ['string', Rule::requiredIf(function () use ($request) {
                return $request->type == 'form';
            })],
            'email' => ['email', Rule::requiredIf(function () use ($request) {
                return $request->type == 'form';
            })],
            'phone_number' => ['string', Rule::requiredIf(function () use ($request) {
                return $request->type == 'form';
            })],
        ]);


        // add transction
        DB::beginTransaction();
        try {
            // Add the user as the candidate
            $validatedData['candidate_id'] = $request->user()->id;
            $validatedData['status'] = 'pending';
            $application = Application::create($validatedData)->refresh();
            
            if ($application->type == 'cv' && isset($validatedData['cv'])) {
                $cv_path = $validatedData['cv']->store("", 'job_cv');
                
                CVApplication::create([
                    'application_id'=>$application->id, 
                    'cv'=> $cv_path
                ]);
            }
            if ($application->type == 'form') {
                FormApplication::create([
                    'application_id'=>$application->id, 
                    'name'=>$validatedData['name'],
                    'email'=>$validatedData['email'],
                    'phone_number'=>$validatedData['phone_number']
                ]);
            }

            DB::commit();
        } catch (Expception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
        return response()->json(['message'=>'application was submitted successfully']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Application $application)
    {
        if (!Auth::user()->can('view', $application)) {
            // Return a 403 error if the user doesn't have permission
            return response()->json([
                'error' => 'You do not have permission to view this application'
            ], 403);
        }
        return new ApplicationResource($application);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Application $application)
    {
        if (!Auth::user()->can('delete', $application)) {
            // Return a 403 error if the user doesn't have permission
            return response()->json([
                'error' => 'You do not have permission to view this application'
            ], 403);
        }
        $application->delete();
        return response()->json(['message' => 'application deleted successfully']);
    }
}
