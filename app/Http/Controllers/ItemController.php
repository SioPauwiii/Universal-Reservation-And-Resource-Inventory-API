<?php

// CRUD controller for Item model
namespace App\Http\Controllers;
use App\Http\Services\ItemService;
use App\Http\Requests\ItemCreateRequest;
use App\Http\Requests\ItemEditRequest;
use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    protected ItemService $itemService;

    public function __construct(ItemService $itemService)
    {
        $this->itemService = $itemService;
    }

    public function create(ItemCreateRequest $request)
    {
        $payload = $request->validated();
        $item = $this->itemService->createItem($payload);

        return response()->json([
            'success' => true,
            'message' => 'Item created successfully',
            'item' => $item,
        ], 201);
    }

    public function fetchAll()
    {
        $items = $this->itemService->getAllItems();

        if (!$items || $items->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No items found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'items' => $items,
        ], 200);
    }
    
    public function fetchOneById($id)
    {
        $item = $this->itemService->itemFetchById($id);

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Item not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'item' => $item,
        ], 200);
    }

    public function fetchOneByName($name)
    {
        $item = $this->itemService->itemFetchByName(urldecode($name));

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Item not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'item' => $item,
        ], 200);
    }

    public function fetchOneBySku($sku)
    {
        $item = $this->itemService->itemFetchBySku($sku);

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Item not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'item' => $item,
        ], 200);
    }

    public function search(Request $request)
    {
        $payload = $request->query('q') ?? $request->input('q') ?? null;

        if (empty($payload)) {
            return response()->json([
                'success' => false,
                'message' => 'Missing search query parameter `q`',
            ], 400);
        }

        $item = $this->itemService->searchItem($payload);

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Item not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'item' => $item,
        ], 200);
    }

    public function update($id, ItemEditRequest $request)
    {
        $payload = $request->validated();
        $item = $this->itemService->updateItem($id, $payload);

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Item not found or update failed',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Item updated successfully',
            'item' => $item,
        ], 200);
    }

    public function archive($id)
    {
        $item = $this->itemService->archiveItem($id);

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Item not found or already archived',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Item archived successfully',
            'item' => $item,
        ], 200);
    }

     public function unarchive($id)
    {
        $item = $this->itemService->unarchiveItem($id);

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'already unarchived',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Item unarchived successfully',
            'item' => $item,
        ], 200);
    }

    public function delete($id)
    {
        $deleted = $this->itemService->deleteItem($id);

        if (!$deleted) {
            return response()->json([
                'success' => false,
                'message' => 'Item not found or delete failed',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Item deleted successfully',
        ], 200);
    }
}