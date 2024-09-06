<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|string|in:employer,candidate',
        ]);

        // Check if user already exists
        if (User::where('email', $request->email)->exists()) {
            return response()->json(['message' => 'User already exists'], 409);
        }

        // Create a new user instance
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        // Generate a new token for the user
        $token = $user->createToken('auth_token')->plainTextToken;

        // Return a response with the access token
        return response()->json(['access_token' => $token, 'token_type' => 'Bearer']);
    }

    /**
     * Handle an incoming login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Find a user by the given email
        $user = User::where('email', $request->email)->first();

        // If the user is not found or the password does not match, return 401
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Check if user exists and have token
        if ($user->tokens()->exists()) {
            $user->tokens()->delete();
        }

        // Generate a new token for the user
        $token = $user->createToken('auth_token')->plainTextToken;

        // Return a response with the access token
        return response()->json(['access_token' => $token, 'token_type' => 'Bearer']);
    }

    /**
     * Revoke the current user's access token.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        // Delete the current access token
        $request->user()->currentAccessToken()->delete();

        // Return a JSON response indicating that the logout was successful
        return response()->json(['message' => 'Logout successful']);
    }

    /**
     * Get the authenticated user.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(Request $request)
    {
        // Return the authenticated user as JSON
        return response()->json($request->user());
    }
}
