<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ItemReservationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'tenant_id' => 'required|integer',
            'item_id' => 'required|integer|exists:items,id',
            'quantity' => 'required|integer|min:1',
            'start_at' => 'required|date',
            'end_at' => 'nullable|date|after:start_at',
            'idempotency_key' => 'nullable|string|max:255',
            'user_id' => 'nullable|integer|exists:users,id',
            'location_id' => 'nullable|integer',
            'price_amount' => 'nullable|numeric|min:0',
            'meta' => 'nullable|array',
        ];
    }
}
