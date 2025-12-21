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
        $items = $this->itemRepo->getAllItems();

        // ensure we only return active items
        return collect($items)->where('status', 'active')->values();
    }

    public function itemFetchById($id)
    {
        $item = $this->itemRepo->findItemById($id);

        if (!$item) {
            return null;
        }

        // support array or model
        $status = is_array($item) ? ($item['status'] ?? null) : ($item->status ?? null);
        return $status === 'active' ? $item : null;
    }

    public function itemFetchByName($name)
    {
        $item = $this->itemRepo->findByName($name);

        if (!$item) {
            return null;
        }

        $status = is_array($item) ? ($item['status'] ?? null) : ($item->status ?? null);
        return $status === 'active' ? $item : null;
    }

    public function itemFetchBySku($sku)
    {
        $item = $this->itemRepo->findBySku($sku);

        if (!$item) {
            return null;
        }

        $status = is_array($item) ? ($item['status'] ?? null) : ($item->status ?? null);
        return $status === 'active' ? $item : null;
    }

    public function updateItem($id, array $data)
    {
        return $this->itemRepo->updateItem($id, $data);
    }

    public function searchItem($payload)
    {
        $item = $this->itemRepo->findByName($payload);
        if ($item && (is_array($item) ? ($item['status'] ?? null) === 'active' : ($item->status ?? null) === 'active')) {
            return $item;
        }

        $item = $this->itemRepo->findBySku($payload);
        if ($item && (is_array($item) ? ($item['status'] ?? null) === 'active' : ($item->status ?? null) === 'active')) {
            return $item;
        }

        return null;
    }

    public function archiveItem($id)
    {
        $item = $this->itemRepo->findItemById($id);

        if (!$item) {
            return null;
        }

        $status = is_array($item) ? ($item['status'] ?? null) : ($item->status ?? null);

        if ($status === 'inactive') {
            return $item;
        }

        return $this->itemRepo->archiveItem($id);
    }

    public function unarchiveItem($id)
    {
        $item = $this->itemRepo->findItemById($id);

        if (!$item) {
            return null;
        }

        $status = is_array($item) ? ($item['status'] ?? null) : ($item->status ?? null);

        if ($status === 'active') {
            return $item;
        }

        return $this->itemRepo->unarchiveItem($id);
    }

    public function deleteItem($id)
    {
        return $this->itemRepo->deleteItem($id);
    }
}   