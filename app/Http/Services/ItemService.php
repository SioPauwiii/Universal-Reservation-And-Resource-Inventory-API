<?php

// CRUD services for Item model
namespace App\Http\Services;
use App\Http\Repositories\ItemRepo;
use App\Http\Services\CacheService;
use App\Models\Item;
use Illuminate\Support\Facades\Cache;


class ItemService
{
    protected $itemRepo, $cacheService;

    public function __construct(ItemRepo $itemRepo, CacheService $cacheService)
    {
        $this->itemRepo = $itemRepo;
        $this->cacheService = $cacheService;
    }

    public function createItem(array $data)
    {
        return $this->itemRepo->createItem($data);
    }

    public function getAllItems()
    {
        $cachedItem = $this->cacheService->getFromCache('all_active_items');
        if ($cachedItem) {
            return $cachedItem;
        }

        $items = $this->itemRepo->getAllItems();

        $this->cacheService->storeInCache('all_active_items', collect($items)->where('status', 'active')->values(), 300);
        
        return collect($items)->where('status', 'active')->values();
    }

    public function itemFetchById($id)
    {

        $cachedItem = $this->cacheService->getFromCache('item_by_id_' . $id);

        if($cachedItem){
            return $cachedItem;
        }

        $item = $this->itemRepo->findItemById($id);

        if (!$item) {
            return null;
        }

        $status = is_array($item) ? ($item['status'] ?? null) : ($item->status ?? null);

        $this->cacheService->storeInCache('item_by_id_' . $id, $item, 300);

        return $status === 'active' ? $item : null;
    }

    public function itemFetchByName($name)
    {
        $cachedItem = $this->cacheService->getFromCache('item_by_name_' . $name);

        if($cachedItem){
            return $cachedItem;
        }

        $item = $this->itemRepo->findByName($name);

        if (!$item) {
            return null;
        }

        $status = is_array($item) ? ($item['status'] ?? null) : ($item->status ?? null);

        $this->cacheService->storeInCache('item_by_name_' . $name, $item, 300);

        return $status === 'active' ? $item : null;
    }

    public function itemFetchBySku($sku)
    {
        $cachedItem = $this->cacheService->getFromCache('item_by_sku_' . $sku);

        if($cachedItem){
            return $cachedItem;
        }

        $item = $this->itemRepo->findBySku($sku);

        if (!$item) {
            return null;
        }

        $status = is_array($item) ? ($item['status'] ?? null) : ($item->status ?? null);

        $this->cacheService->storeInCache('item_by_sku_' . $sku, $item, 300);
        
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
        $item = $this->itemRepo->findItemById($id);

        if (!$item) {
            return null;
        }

        $this->itemRepo->deleteItem($id);

        $items = $this->itemRepo->getAllItems();

        $this->cacheService->storeInCache('all_active_items', collect($items)->where('status', 'active')->values(), 300);

        $name = is_array($item) ? ($item['name'] ?? null) : ($item->name ?? null);
        $sku = is_array($item) ? ($item['sku'] ?? null) : ($item->sku ?? null);

        $this->cacheService->clearCache('item_by_id_' . $id);
        if ($name) $this->cacheService->clearCache('item_by_name_' . $name);
        if ($sku) $this->cacheService->clearCache('item_by_sku_' . $sku);

        return true;
    }
}   