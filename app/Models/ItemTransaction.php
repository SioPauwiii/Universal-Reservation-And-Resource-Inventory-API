<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ItemTransaction extends Model
{
    use HasFactory;

    protected $table = 'item_transactions';

    protected $fillable = [
        'inventoru_item_id',
        'type',
        'quantity',
        'reference_id',
        'expires_at',
    ];
}