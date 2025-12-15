<?php

// CRUD services for Item model
namespace App\Http\Services;
use App\Http\Repositories\ItemRepo;

class ItemService
{
    protected $itemRepo;

    public function __construct(ItemRepo $itemRepo)
    {
        $this->itemRepo = $itemRepo;
    }
}