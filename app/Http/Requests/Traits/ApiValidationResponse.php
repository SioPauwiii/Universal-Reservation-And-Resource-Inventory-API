<?php

namespace App\Http\Requests\Traits;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

trait ApiValidationResponse
{
    protected function failedValidation(Validator $validator): void
    {
        $errors = $validator->errors()->toArray();
        $formatted = collect($errors)->map(fn($msgs) => $msgs[0])->toArray();

        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $formatted,
        ], 422));
    }
}
