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

    
}