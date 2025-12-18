<?php

// CRUD controller for Item model
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests\ItemRequest;
use App\Http\Services\ItemService;
use App\Http\Repositories\ItemRepo;

class ItemController extends Controller
{
    protected ItemService $itemService;

    public function __construct(ItemService $itemService)
    {
        $this->itemService = $itemService;
    }

    public function store(ItemRequest $request)
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

        return response()->json([
            'success' => true,
            'items' => $items,
        ], 200);
    }
}