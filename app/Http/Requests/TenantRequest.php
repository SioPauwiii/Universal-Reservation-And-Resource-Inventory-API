<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TenantRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:tenants,name',
            'domain' => 'required|string|max:255|unique:tenants,domain',
            'owner_email' => 'required|email|max:255|unique:tenants,owner_email',
            'owner_name' => 'nullable|string|max:255',
            'contact_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'business_email' => 'required|email|max:255|unique:tenants,business_email',
            'business_description' => 'nullable|string',
            'status' => ['nullable', Rule::in(['active', 'inactive', 'suspended'])],
            'plan' => ['nullable', Rule::in(['free', 'basic', 'premium'])],
            'settings' => 'nullable|array',
        ];
    }
}