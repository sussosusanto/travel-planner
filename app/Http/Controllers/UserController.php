<?php

namespace App\Http\Controllers;

use App\Enums\StatusCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Repositories\Contracts\UserRepositoryInterface;

class UserController extends Controller
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function login(Request $request)
    {

        try {
            $requestBody = $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            $user = $this->userRepository->login($requestBody);

            if (!$user) {
                return response()->json([
                    'message' => 'Unauthorized'
                ], StatusCode::UNAUTHORIZED);
            }

            $token = $user->createToken('authToken')->plainTextToken;

            return response()->json([
                'message' => 'Login success',
                'user' => $user,
                'token' => $token
            ], StatusCode::OK);

        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                return response()->json([
                    'message' => 'Validation Error',
                    'errors' => $e->errors()
                ], StatusCode::VALIDATION_ERROR);
            }

            // Add another use case here

            return response()->json([
                'message' => 'Internal Server Error',
                'errors' => $e->getMessage()
            ], StatusCode::INTERNAL_SERVER_ERROR);
        }

    }

    public function register(Request $request)
    {
        try {
            $requestBody = $request->validate([
                'name' => 'required|min:6',
                'email' => 'required|email|unique:users',
                'password' => 'required|confirmed|min:8',
            ]);

            $requestBody['password'] = bcrypt($requestBody['password']);

            $user = $this->userRepository->register($requestBody);

            return response()->json([
                'message' => 'User registered successfully',
                'data' => $user
            ], StatusCode::CREATED);

        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                return response()->json([
                    'message' => 'Validation Error',
                    'errors' => $e->errors()
                ], StatusCode::VALIDATION_ERROR);
            }

            // Add another use case here

            return response()->json([
                'message' => 'Internal Server Error',
                'errors' => $e->getMessage()
            ], StatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();
            return response()->json([
                'message' => 'Logged out successfully'
            ], StatusCode::OK);
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            return response()->json([
                'message' => 'Internal Server Error',
                'errors' => $e->getMessage()
            ], StatusCode::INTERNAL_SERVER_ERROR);
        }
    }
}
