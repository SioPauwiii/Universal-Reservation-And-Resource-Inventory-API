<?php

// user auth
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\LoginRequest;
use App\Http\Services\AuthService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\RegisterRequest;
use App\Http\Services\TenantService;
use App\Http\Requests\TenantRequest;

class AuthController extends Controller
{
    protected $authService, $tenantService;

    public function __construct()
    {
        $this->authService = new AuthService();
        $this->tenantService = new TenantService();
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $payload = $request->validated();
        $result = $this->authService->attemptRegistration($payload);

        if ($result instanceof JsonResponse) {
            return $result;
        }

        return response()->json([
            'user' => $result['user'],
            'token' => $result['token'],
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $payload = $request->validated();
        $result = $this->authService->attemptLogin($payload);

        if ($result instanceof JsonResponse) {
            return $result;
        }

        return response()->json([
            'user' => $result['user'],
            'token' => $result['token'],
        ], 200);
    }

    public function logout()
    {
        $this->authService->attemptLogout();
        return response()->json(['message' => 'Successfully logged out'], 200);
    }

    public function RegisterTenant(TenantRequest $request)
    {
        $payload = $request->rules();
        $tenant = $this->tenantService->registerTenant($payload);

        return response()->json([
            'tenant' => $tenant,
        ], 201);
    }
}