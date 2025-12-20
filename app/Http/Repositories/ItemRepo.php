<?php

// CRUD DB transactionsfor Item model
namespace App\Http\Repositories;
use App\Models\Item;

class ItemRepo
{
    protected $itemModel;

    public function __construct(Item $itemModel)
    {
        $this->itemModel = $itemModel;
    }

    public function createItem(array $itemData): Item
    {
        return $this->itemModel->create($itemData);
    }

    public function getAllItems()
    {
        return $this->itemModel->all();
    }

    public function findItemById($id)
    {
        return $this->itemModel->find($id);
    }

    public function findByName($name)
    {
        return $this->itemModel->where('name', $name)->first();
    }

    public function findBySku($sku)
    {
        return $this->itemModel->where('sku', $sku)->first();
    }

    public function updateItem($id, $itemData)
    {
        $item = $this->findItemById($id);

        if(!$item)
        {
            return null;
        }

        if ($item) {
            $item->update($itemData);
            return $item;
        }
        return null;
    }

    public function deleteItem($id)
    {
        $item = $this->findItemById($id);

        if(!$item)
        {
            return null;
        }

        if ($item) {
            return $item->delete();
        }
        return false;
    }

    
}