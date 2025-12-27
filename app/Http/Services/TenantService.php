<?php

namespace App\Http\Services;

use App\Http\Repositories\TenantRepo;
use App\Models\Tenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\TenantRequest;
use Laravel\Sanctum\PersonalAccessToken;

class TenantService
{
    protected $tenantRepo;

    public function __construct()
    {
        $this->tenantRepo = new TenantRepo();
    }

    public function registerTenant(array $data): array|JsonResponse
    {
        $rules = (new TenantRequest())->rules();

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            if (isset($data['domain']) && $this->tenantRepo->findTenantByDomain($data['domain'])) {
                return response()->json(['message' => 'Domain already registered'], 409);
            }

            if (! empty($data['owner_email']) && $this->tenantRepo->findTenantByOwnerEmail($data['owner_email'])) {
                return response()->json(['message' => 'Owner email already registered'], 409);
            }

            if (! empty($data['name']) && $this->tenantRepo->findTenantByName($data['name'])) {
                return response()->json(['message' => 'Tenant name already taken'], 409);
            }

            if (! empty($data['business_email']) && $this->tenantRepo->findByBusinessEmail($data['business_email'])) {
                return response()->json(['message' => 'Business email already registered'], 409);
            }

            $tenant = $this->tenantRepo->createTenant($data);

            if (! $tenant instanceof Tenant) {
                return response()->json(['success' => false, 'message' => 'Unable to create tenant'], 500);
            }

            $token = null;
            if (method_exists($tenant, 'createToken')) {
                $token = $tenant->createToken('api_token')->plainTextToken;
            }

            return ['success' => true, 'tenant' => $tenant, 'token' => $token];
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function attemptLoginTenant(array $credentials): array|JsonResponse
    {
        if (Auth::attempt($credentials)) {
            $tenant = Auth::user();

            // locate tenant using available credential fields
            $tenant = null;
            if (isset($credentials['domain'])) {
                $tenant = $this->tenantRepo->findTenantByDomain($credentials['domain']);
            } elseif (isset($credentials['owner_email'])) {
                $tenant = $this->tenantRepo->findTenantByOwnerEmail($credentials['owner_email']);
            } elseif (isset($credentials['business_email'])) {
                $tenant = $this->tenantRepo->findByBusinessEmail($credentials['business_email']);
            }

            if (! $tenant instanceof Tenant) {
                return response()->json(['message' => 'Tenant not found for provided credentials'], 404);
            }

            $token = $tenant->createToken('api_token')->plainTextToken;

            return ['success' => true, 'tenant' => $tenant, 'token' => $token];
        }

        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    public function attemptLogoutTenant()
    {
        $tenant = Auth::user();

        if (! $tenant instanceof Tenant) {
            return response()->json(['message' => 'Authenticated tenant not found'], 401);
        }

        /** @var PersonalAccessToken|null $token */
        $token = $tenant->currentAccessToken();

        if ($token) {
            $token->delete();
        }

        return response()->json(['success' => true, 'message' => 'Logged out successfully'], 200);
    }
}