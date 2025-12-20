<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Foundation\Http\UrlRequest;
use App\Http\Requests\Traits\ApiValidationResponse;
use Illuminate\Validation\Rule;

class ItemFetchRequest extends FormRequest
{
    use ApiValidationResponse;

    public function rules(): array
    {
        return [
            'id' => [
                Rule::requiredIf(fn() => !$this->filled('name') && !$this->filled('sku')),
                'integer',
                'exists:items,id'
            ],
            'sku' => [
                Rule::requiredIf(fn() => !$this->filled('id') && !$this->filled('name')),
                'string',
                'max:100',
                'exists:items,sku'
            ],
            'name' => [
                Rule::requiredIf(fn() => !$this->filled('id') && !$this->filled('sku')),
                'string',
                'max:255',
            ],
            'per_page' => 'sometimes|integer|min:1|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'id.integer' => 'ID must be an integer.',
            'id.exists' => 'Item with that ID does not exist.',
            'sku.exists' => 'Item with that SKU does not exist.',
        ];
    }

    // public function attributes(): array
    // {
    //     return [
    //         'identifier' => 'Identifier Type',
    //         'value' => 'Identifier Value',
    //     ];
    // }
}