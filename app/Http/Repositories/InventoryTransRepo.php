<?php

namespace App\Http\Repositories;

use Illuminate\Support\Facades\DB;

class InventoryTransRepo
{
    public function record(int $inventoryItemId, string $type, int $quantity, string $referenceId, ?\DateTimeInterface $expiresAt = null)
    {
        return DB::table('inventory_transactions')->insertGetId([
            'inventory_item_id' => $inventoryItemId,
            'type' => $type,
            'quantity' => $quantity,
            'reference_id' => $referenceId,
            'expires_at' => $expiresAt,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
