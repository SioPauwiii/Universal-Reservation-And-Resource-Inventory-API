<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Traits\ApiValidationResponse;

class ItemRequest extends FormRequest
{
    use ApiValidationResponse;

    public function rules(): array
    {
        return [
            'name' => 'required|string|unique:items,name|max:255',
            'sku' => 'required|string|unique:items,sku|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Please provide the item name.',
            'name.unique' => 'An item with this name already exists.',
            'sku.required' => 'Please provide the SKU.',
            'sku.unique' => 'An item with this SKU already exists.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Item name',
            'sku' => 'SKU',
        ];
    }
}