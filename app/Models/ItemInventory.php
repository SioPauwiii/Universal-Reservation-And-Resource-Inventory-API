<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ItemInventory extends Model
{
    use HasFactory;

    protected $table = 'inventory_items';

    protected $fillable = [
        'item_id',
        'location_id',
        'total_stock',
        'available_stock',
        'version',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}