<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Traits\ApiValidationResponse;
use App\Http\Requests\Traits\ItemDetailsValidation;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator as ValidationC;
use Illuminate\Http\Exceptions\HttpResponseException;

class ItemCreateRequest extends FormRequest
{
    use ApiValidationResponse, ItemDetailsValidation;

    public function rules(): array
    {
        return [
            'name' => 'required|string|unique:items,name|max:255',
            'sku' => 'required|string|unique:items,sku|max:100',
            'type' => ['required','string', Rule::in([
                'physical','consumable','spaces','equipment','vehicle',
                'appointment','event','session','rental','digital','personnel','ad-hoc'
            ])],
            'details' => 'nullable|array',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Please provide the item name.',
            'name.unique' => 'An item with this name already exists.',
            'sku.required' => 'Please provide the SKU.',
            'sku.unique' => 'An item with this SKU already exists.',
            'type.required' => 'Please provide the item type.',
            'type.in' => 'The selected item type is invalid.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Item name',
            'sku' => 'SKU',
            'type' => 'Item type',
            'details' => 'Item details',
        ];
    }

    /**
     * Run additional validation for details based on the selected type.
     */
    protected function withValidator(ValidationC $validator)
    {
        $validator->after(function ($validator) {
            $type = $this->input('type');

            if (empty($type)) {
                return;
            }

            $detailsValidator = $this->validateDetailsPerType($type, $this->all());

            if ($detailsValidator->fails()) {
                foreach ($detailsValidator->errors()->messages() as $field => $messages) {
                    foreach ($messages as $message) {
                        $validator->errors()->add($field, $message);
                    }
                }
            }
        });
    }
}