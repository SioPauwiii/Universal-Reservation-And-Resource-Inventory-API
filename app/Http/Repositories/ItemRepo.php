<?php

// CRUD DB transactionsfor Item model
namespace App\Http\Repositories;
use App\Models\Item;
use App\Models\ItemDetail;

class ItemRepo
{
    protected $itemModel, $detailModel;

    public function __construct(Item $itemModel, ItemDetail $detailModel)
    {
        $this->itemModel = $itemModel;
        $this->detailModel = $detailModel;
    }

    public function createItem(array $itemData)
    {
        $item = $this->itemModel->create([
            'name' => $itemData['name'],
            'sku' => $itemData['sku'],
        ]);

        $detailPayload = [
            'type' => $itemData['type'],
            'description' => $itemData['description'] ?? null,
            'details' => $itemData['details'] ?? null,
        ];

        // create via relationship so item_id is set correctly
        $details = $item->details()->create($detailPayload);

        return $item->load('details');
    }

    public function getAllItems()
    {
        return $this->itemModel->all();
    }

    public function findItemById($id)
    {
        return $this->itemModel->with('details')->find($id);
    }

    public function findByName($name)
    {
        return $this->itemModel->with('details')->where('name', $name)->first();
    }

    public function findBySku($sku)
    {
        return $this->itemModel->with('details')->where('sku', $sku)->first();
    }

    public function updateItem($id, $itemData)
    {
        $item = $this->itemModel->find($id);

        if (!$item) {
            return null;
        }

        // update item core fields (Item model's $fillable controls what's updated)
        $item->update($itemData);

        // update or create details if provided
        $detailPayload = [];
        if (array_key_exists('type', $itemData)) {
            $detailPayload['type'] = $itemData['type'];
        }
        if (array_key_exists('description', $itemData)) {
            $detailPayload['description'] = $itemData['description'];
        }
        if (array_key_exists('details', $itemData)) {
            $detailPayload['details'] = $itemData['details'];
        }

        if (!empty($detailPayload)) {
            $existing = $item->details;
            if ($existing) {
                $existing->update($detailPayload);
            } else {
                $item->details()->create($detailPayload);
            }
        }

        return $item->fresh('details');
    }

    public function archiveItem($id)
    {
        return $this->itemModel->where('id', $id)->update(['status' => 'inactive']);
    }

    public function unarchiveItem($id)
    {
        return $this->itemModel->where('id', $id)->update(['status' => 'active']);
    }

    public function deleteItem($id)
    {
        $item = $this->itemModel->find($id);

        if (!$item) {
            return null;
        }

        return $item->delete();
    }    
}