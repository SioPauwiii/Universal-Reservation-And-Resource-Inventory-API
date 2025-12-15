<?php

// CRUD controller for Item model
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests\ItemRequest;
use App\Http\Services\ItemService;
use App\Http\Repositories\ItemRepo;

class ItemController extends Controller
{
    protected $itemService;

    public function __construct(ItemService $itemService)
    {
        $this->itemService = $itemService;
    }
}