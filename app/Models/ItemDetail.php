<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ItemDetail extends Model
{
    use HasFactory;

    protected $table = 'item_details';

    protected $fillable = [
        'item_id',
        'type',
        'description',
        'details',
    ];

    protected $casts = [
        'details' => 'array',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}