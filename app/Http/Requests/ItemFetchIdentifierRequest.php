<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Foundation\Http\UrlRequest;
use App\Http\Requests\Traits\ApiValidationResponse;
use Illuminate\Validation\Rule;

class ItemFetchIdentifierRequest extends FormRequest
{
    use ApiValidationResponse;

    public function rules(): array
    {
        return [
            'identifier' => [
                'required',
                'string',
                Rule::in(['id', 'name', 'sku']),
            ],
            'value' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'identifier.required' => 'Please provide the identifier type (id, name, or sku).',
            'identifier.in' => 'Identifier must be one of the following: id, name, sku.',
            'value.required' => 'Please provide the value for the identifier.',
        ];
    }

    public function attributes(): array
    {
        return [
            'identifier' => 'Identifier Type',
            'value' => 'Identifier Value',
        ];
    }
}