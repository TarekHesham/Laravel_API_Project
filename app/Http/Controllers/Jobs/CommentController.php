<?php

namespace App\Http\Controllers\Jobs;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Models\Users\Comment;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->user()->cannot('viewAny', Comment::class)) {
            return response()->json(['message' => 'Unauthorized to view all comments'], 401);
        }
        return Comment::all();
    }

    // Display the specified resource.
    public function show(Request $request, Comment $comment)
    {
        if ($request->user()->cannot('view', $comment)) {
            return response()->json(['message' => 'Unauthorized to view this comment'], 401);
        }
        return $comment;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request_data = $request->all();

        // Validate the input
        $comment_validator = Validator::make($request_data, [
            'content' => 'required|string',
            'job_id' => 'required|integer'
        ]);

        // Return a 422 error if validation fails
        if ($comment_validator->fails()) {
            return response()->json([
                "message" => "Errors with your request",
                "errors" => $comment_validator->errors()
            ], 422);
        }

        $request_data['user_id'] = $request->user()->id;

        Comment::create($request_data);
        return response()->json(['message' => 'Comment created successfully'], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Comment $comment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Comment $comment)
    {
        if (!$request->user()->can('delete', $comment)) {
            // Return a 403 error if the user doesn't have permission
            return response()->json([
                'message' => 'You do not have permission to delete this comment'
            ], 403);
        }

        $comment->delete();

        return response()->json(['message' => 'Comment deleted successfully']);
    }
}
