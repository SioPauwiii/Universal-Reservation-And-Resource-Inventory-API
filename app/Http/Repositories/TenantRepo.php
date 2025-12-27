<?php

namespace App\Http\Repositories;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Collection;

class TenantRepo
{
    public function createTenant(array $data): Tenant
    {
        return Tenant::create($data);
    }

    public function findTenantByDomain(string $domain): ?Tenant
    {
        return Tenant::where('domain', $domain)->first();
    }

    public function findTenantById(int $id): ?Tenant
    {
        return Tenant::where('id', $id)->first();
    }

    public function findTenantByOwnerEmail(string $email): ?Tenant
    {
        return Tenant::where('owner_email', $email)->first();
    }

    public function findTenantByName(string $name): ?Tenant
    {
        return Tenant::where('name', $name)->first();
    }

    public function findByBusinessEmail(string $email): ?Tenant
    {
        return Tenant::where('business_email', $email)->first();
    }

    public function listTenants(array $filters = [])
    {
        $query = Tenant::query();

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['plan'])) {
            $query->where('plan', $filters['plan']);
        }

        return $query->get();
    }

    public function updateTenant(Tenant $tenant, array $data): Tenant
    {
        $tenant->update($data);
        return $tenant;
    }

    public function archiveTenant(Tenant $tenant): bool
    {
        return $tenant->setAttribute('status', 'inactive')->save();
    }

    public function deleteTenant(Tenant $tenant): bool
    {
        return $tenant->delete();
    }
}