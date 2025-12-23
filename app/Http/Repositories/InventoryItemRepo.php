<?php

namespace App\Http\Repositories;

use Illuminate\Support\Facades\DB;
use App\Models\ItemInventory;

class InventoryItemRepo
{
    protected $itemInventory;

    public function __construct(ItemInventory $inventoryItem)
    {
        $this->itemInventory = $inventoryItem;
    }   

    public function createInventoryItem(int $itemId, int $totalStock, int $availableStock, int $locationId = 0, int $version = 1)
    {
        $item = $this->itemInventory->create([
            'item_id' => $itemId,
            'location_id' => $locationId,
            'total_stock' => $totalStock,
            'available_stock' => $availableStock,
            'version' => $version,
        ]);

        return $item->id;
    }

	public function findByItemId(int $itemId)
	{
		return DB::table('inventory_items')->where('item_id', $itemId)->first();
	}

	public function decrementAvailable(int $inventoryId, int $amount)
	{
		return DB::table('inventory_items')->where('id', $inventoryId)->decrement('available_stock', $amount);
	}

	public function incrementAvailable(int $inventoryId, int $amount)
	{
		return DB::table('inventory_items')->where('id', $inventoryId)->increment('available_stock', $amount);
	}
}

