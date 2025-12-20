<?php

namespace App\Http\Requests\Traits;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

trait ItemDetailsValidation
{
    protected function validateDetailsPerType(string $type, array $payload)
    {
        $rulesMap = [
            'physical' => [
                'details.weight' => 'required|numeric|min:0',
                'details.dimensions.length' => 'nullable|numeric|min:0',
                'details.dimensions.width' => 'nullable|numeric|min:0',
                'details.dimensions.height' => 'nullable|numeric|min:0',
                'details.material' => 'sometimes|string|max:255',
                'details.manufacturer' => 'sometimes|string|max:255',
                'details.model' => 'sometimes|string|max:255',
                'details.serial_number' => 'sometimes|string|max:255',
                'details.warranty_until' => 'nullable|date',
                'details.storage_location' => 'nullable|string|max:255',
                'details.sku' => 'nullable|string|max:255',
            ],

            'consumable' => [
                'details.quantity_on_hand' => 'required|integer|min:0',
                'details.unit' => 'required|string|max:50',
                'details.expiry_date' => 'nullable|date',
                'details.reorder_point' => 'nullable|integer|min:0',
                'details.supplier_id' => 'nullable|integer',
                'details.lot_number' => 'nullable|string|max:255',
                'details.usage_rate' => 'nullable|numeric|min:0',
            ],

            'spaces' => [
                'details.capacity' => 'nullable|integer|min:0',
                'details.area' => 'nullable|numeric|min:0',
                'details.length' => 'nullable|numeric|min:0',
                'details.width' => 'nullable|numeric|min:0',
                'details.height' => 'nullable|numeric|min:0',
                'details.location' => 'nullable|string|max:255',
                'details.amenities' => 'nullable|array',
                'details.available_from' => 'nullable|date',
                'details.available_to' => 'nullable|date',
                'details.booking_rules.min_duration' => 'nullable|integer|min:0',
                'details.hourly_rate' => 'nullable|numeric|min:0',
            ],

            'equipment' => [
                'details.manufacturer' => 'nullable|string|max:255',
                'details.model' => 'nullable|string|max:255',
                'details.serial_number' => 'nullable|string|max:255',
                'details.purchase_date' => 'nullable|date',
                'details.maintenance_schedule' => 'nullable|array',
                'details.operating_manual_url' => 'nullable|url',
                'details.status' => 'nullable|string|in:active,maintenance,decommissioned',
            ],

            'vehicle' => [
                'details.vin' => 'nullable|string|max:255',
                'details.license_plate' => 'nullable|string|max:255',
                'details.make' => 'nullable|string|max:255',
                'details.model' => 'nullable|string|max:255',
                'details.year' => 'nullable|integer|min:1886',
                'details.mileage' => 'nullable|numeric|min:0',
                'details.registration_expiry' => 'nullable|date',
                'details.insurance_policy' => 'nullable|array',
                'details.fuel_type' => 'nullable|string|max:50',
            ],

            'appointment' => [
                'details.start_at' => 'required|date',
                'details.end_at' => 'nullable|date|after:details.start_at',
                'details.participant_ids' => 'nullable|array',
                'details.location' => 'nullable|string|max:255',
                'details.purpose' => 'nullable|string|max:1000',
                'details.organizer_id' => 'nullable|integer',
                'details.reminders' => 'nullable|array',
            ],

            'event' => [
                'details.start_at' => 'required|date',
                'details.end_at' => 'nullable|date|after:details.start_at',
                'details.venue' => 'nullable|string|max:255',
                'details.capacity' => 'nullable|integer|min:0',
                'details.organizer' => 'nullable|string|max:255',
                'details.ticketing' => 'nullable|array',
                'details.public' => 'nullable|boolean',
            ],

            'session' => [
                'details.start_at' => 'required|date',
                'details.end_at' => 'nullable|date|after:details.start_at',
                'details.instructor_id' => 'nullable|integer',
                'details.max_participants' => 'nullable|integer|min:0',
                'details.materials' => 'nullable|array',
                'details.recurrence' => 'nullable|array',
            ],

            'rental' => [
                'details.rate' => 'required|numeric|min:0',
                'details.rate_unit' => 'required|string|max:50',
                'details.deposit_required' => 'nullable|boolean',
                'details.availability_calendar' => 'nullable|array',
                'details.min_rental_period' => 'nullable|integer|min:0',
                'details.allowed_use' => 'nullable|string|max:255',
                'details.condition_on_return' => 'nullable|string',
            ],

            'digital' => [
                'details.download_url' => 'required|url',
                'details.license' => 'nullable|array',
                'details.file_type' => 'nullable|string|max:50',
                'details.file_size' => 'nullable|numeric|min:0',
                'details.access_expires_at' => 'nullable|date',
                'details.checksum' => 'nullable|string|max:255',
                'details.drm' => 'nullable|array',
            ],

            'personnel' => [
                'details.user_id' => 'required|integer',
                'details.role' => 'nullable|string|max:100',
                'details.contact.email' => 'nullable|email',
                'details.contact.phone' => 'nullable|string|max:50',
                'details.certifications' => 'nullable|array',
                'details.shift_pattern' => 'nullable|array',
                'details.employment_status' => 'nullable|string|in:active,on_leave,terminated',
            ],

            'ad-hoc' => [
                'details.fields' => 'nullable|array',
                'details.notes' => 'nullable|string',
                'details.metadata' => 'nullable|array',
            ],
        ];

        $rules = $rulesMap[$type] ?? [];
        return Validator::make($payload, $rules);
    }
}
