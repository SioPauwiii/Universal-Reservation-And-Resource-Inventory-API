<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Traits\ApiValidationResponse;
use App\Http\Requests\Traits\ItemDetailsValidation;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator as ValidationC;
use Illuminate\Http\Exceptions\HttpResponseException;

class ItemEditRequest extends FormRequest
{
    use ApiValidationResponse, ItemDetailsValidation;

    public function rules(): array
    {
        // determine the current item id to ignore for unique checks
        $id = null;
        $routeItem = $this->route('item');

        if ($routeItem) {
            if (is_numeric($routeItem)) {
                $id = $routeItem;
            } elseif (is_object($routeItem) && method_exists($routeItem, 'getKey')) {
                $id = $routeItem->getKey();
            }
        } elseif ($this->route('id')) {
            $id = $this->route('id');
        } elseif ($this->input('id')) {
            $id = $this->input('id');
        }

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('items', 'name')->ignore($id),
            ],
            'sku' => [
                'required',
                'string',
                'max:100',
                Rule::unique('items', 'sku')->ignore($id),
            ],
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