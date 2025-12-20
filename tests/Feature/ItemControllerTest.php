<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Item;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

it('returns validation errors when creating an item with invalid data', function () {
    /** @var \Tests\TestCase $this */
    $response = $this->postJson('/api/items', []);

    $response->assertStatus(422)
             ->assertJsonStructure([
                 'success',
                 'message',
                 'errors' => ['name','sku'],
             ]);
});

it('creates an item when data is valid', function () {
    $payload = [
        'name' => 'Test Item',
        'sku' => 'TEST-SKU-123',
        'type' => 'physical',
        'details' => [
            'weight' => 2.5,
            'dimensions' => ['length' => 10, 'width' => 20, 'height' => 30],
        ]
    ];

    /** @var \Tests\TestCase $this */
    $response = $this->postJson('/api/items', $payload);

    $response->assertStatus(201)
             ->assertJson([ 'success' => true ]);

    $this->assertDatabaseHas('items', [ 'name' => 'Test Item', 'sku' => 'TEST-SKU-123' ]);
});

it('creates items for every supported type with valid details', function () {
    $types = [
        'physical' => [
            'weight' => 2.5,
            'dimensions' => ['length' => 10, 'width' => 20, 'height' => 30],
        ],
        'consumable' => [
            'quantity_on_hand' => 100,
            'unit' => 'pcs',
            'expiry_date' => '2030-01-01',
        ],
        'spaces' => [
            'capacity' => 50,
            'area' => 120.5,
            'length' => 10,
            'width' => 12,
            'height' => 3,
            'amenities' => ['wifi','projector'],
        ],
        'equipment' => [
            'manufacturer' => 'ACME',
            'model' => 'X1000',
            'serial_number' => 'SN12345',
        ],
        'vehicle' => [
            'vin' => '1HGCM82633A004352',
            'license_plate' => 'ABC-1234',
            'make' => 'Make',
            'model' => 'Model',
            'year' => 2020,
            'mileage' => 15000,
        ],
        'appointment' => [
            'start_at' => '2025-12-21',
            'end_at' => '2025-12-22',
            'participant_ids' => [1,2],
        ],
        'event' => [
            'start_at' => '2025-12-21',
            'end_at' => '2025-12-22',
            'venue' => 'Main Hall',
        ],
        'session' => [
            'start_at' => '2025-12-21',
            'end_at' => '2025-12-22',
        ],
        'rental' => [
            'rate' => 50,
            'rate_unit' => 'day',
        ],
        'digital' => [
            'download_url' => 'https://example.com/file.zip',
            'file_type' => 'zip',
        ],
        'personnel' => [
            'user_id' => 1,
            'role' => 'technician',
        ],
        'ad-hoc' => [
            'fields' => ['custom' => 'value'],
            'notes' => 'Some notes',
        ],
    ];

    $i = 1;
    foreach ($types as $type => $details) {
        $payload = [
            'name' => 'Type Test ' . $type . ' ' . $i,
            'sku' => 'TYPE-SKU-' . strtoupper($type) . '-' . $i,
            'type' => $type,
            'details' => $details,
        ];

        /** @var \Tests\TestCase $this */
        $response = $this->postJson('/api/items', $payload);

        $response->assertStatus(201)->assertJson(['success' => true]);

        $this->assertDatabaseHas('items', ['name' => $payload['name'], 'sku' => $payload['sku']]);

        $i++;
    }
});

it('fetches all items', function () {
    // Create some items
    Item::factory()->create([ 'name' => 'Item 1', 'sku' => 'SKU-1' ]);
    Item::factory()->create([ 'name' => 'Item 2', 'sku' => 'SKU-2' ]);

    /** @var \Tests\TestCase $this */
    $response = $this->getJson('/api/items');

    $response->assertStatus(200)
             ->assertJsonStructure([
                 'success',
                 'items' => [
                     ['id', 'name', 'sku', 'created_at', 'updated_at'],
                 ],
             ])
             ->assertJsonCount(2, 'items');
});

it('returns 404 when no items exist', function () {
    /** @var \Tests\TestCase $this */
    $response = $this->getJson('/api/items');

    $response->assertStatus(404)
             ->assertJson([
                 'success' => false,
                 'message' => 'No items found',
             ]);
});

// Additional tests for fetching by id, name, sku, and searching can be added here.
it('fetches an item by ID', function () {
    $item = Item::factory()->create([ 'name' => 'FetchByID', 'sku' => 'FETCH-ID' ]);

    /** @var \Tests\TestCase $this */
    $response = $this->getJson('/api/items/id/' . $item->id);

    $response->assertStatus(200)
             ->assertJson([
                 'success' => true,
                 'item' => [
                     'id' => $item->id,
                     'name' => 'FetchByID',
                     'sku' => 'FETCH-ID',
                 ],
             ]);
});

it('fetches an item by SKU', function () {
    $item = Item::factory()->create([ 'name' => 'FetchBySKU', 'sku' => 'FETCH-SKU' ]);

    /** @var \Tests\TestCase $this */
    $response = $this->getJson('/api/items/sku/' . $item->sku);

    $response->assertStatus(200)
             ->assertJson([
                 'success' => true,
                 'item' => [
                     'id' => $item->id,
                     'name' => 'FetchBySKU',
                     'sku' => 'FETCH-SKU',
                 ],
             ]);
});

it('fetches an item by Name', function () {
    $item = Item::factory()->create([ 'name' => 'FetchByName', 'sku' => 'FETCH-NAME' ]);

    /** @var \Tests\TestCase $this */
    $response = $this->getJson('/api/items/name/' . urlencode($item->name));

    $response->assertStatus(200)
             ->assertJson([
                 'success' => true,
                 'item' => [
                     'id' => $item->id,
                     'name' => 'FetchByName',
                     'sku' => 'FETCH-NAME',
                 ],
             ]);
});