<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Item;

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
    ];

    /** @var \Tests\TestCase $this */
    $response = $this->postJson('/api/items', $payload);

    $response->assertStatus(201)
             ->assertJson([ 'success' => true ]);

    $this->assertDatabaseHas('items', [ 'name' => 'Test Item', 'sku' => 'TEST-SKU-123' ]);
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
