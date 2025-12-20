<?php

// auth services
namespace App\Http\Services;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;
use App\Http\Repositories\UserRepo;
use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;

class AuthService
{
    public function __construct()
    {
        //
    }

    public function attemptRegistration(array $userData): array|JsonResponse   
    {
        $userData['password'] = Hash::make($userData['password']);

        $repo = new UserRepo(new User());
        try {
            $existingUser = $repo->findUserByEmail($userData['email']);

            if (!empty($existingUser) && $existingUser->email === $userData['email']) {
                return response()->json(['message' => 'Email already registered'], 409);
            }

            if (!empty($existingUser) && $existingUser->name === $userData['name']) {
                return response()->json(['message' => 'Username already taken'], 409);
            }  

            $user = $repo->createUser($userData);

            if (! $user instanceof User) {
                return response()->json([ 'success' => false, 'message' => 'Unable to create user' ], 500);
            }

            $token = $user->createToken('api_token')->plainTextToken;

            return [ 'success' => true, 'user' => $user, 'token' => $token ];

        } catch (\Exception $e) {
            return response()->json([ 'success' => false, 'message' => $e->getMessage() ], 500);
        }
    }

    public function attemptLogin(array $credentials): array|JsonResponse
    {
        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if (! $user instanceof User) {
                return response()->json([ 'message' => 'Authenticated user not found' ], 500);
            }

            $token = $user->createToken('api_token')->plainTextToken;

            return [ 'success' => true, 'user' => $user, 'token' => $token ];
        }

        return response()->json([ 'message' => 'Invalid credentials' ], 401);
    }

    public function attemptLogout()
    {
        $user = Auth::user();

        if (! $user instanceof User) {
            return response()->json(['message' => 'Authenticated user not found'], 401);
        }

        /** @var PersonalAccessToken|null $token */
        $token = $user->currentAccessToken();
        if ($token) {
            $token->delete();
        }

        return response()->json(['message' => 'Logged out successfully'], 200);
    }
}