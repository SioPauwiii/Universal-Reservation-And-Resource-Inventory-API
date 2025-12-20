<?php

// CRUD services for Item model
namespace App\Http\Services;
use App\Http\Repositories\ItemRepo;
use App\Models\Item;

class ItemService
{
    protected $itemRepo;

    public function __construct(ItemRepo $itemRepo)
    {
        $this->itemRepo = $itemRepo;
    }

    public function createItem(array $data)
    {
        return $this->itemRepo->createItem($data);
    }

    public function getAllItems()
    {
        return $this->itemRepo->getAllItems();
    }

    public function itemFetchById($id)
    {
        return $this->itemRepo->findItemById($id);
    }

    public function itemFetchByName($name)
    {
        return $this->itemRepo->findByName($name);
    }

    public function itemFetchBySku($sku)
    {
        return $this->itemRepo->findBySku($sku);
    }
}