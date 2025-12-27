<?php

// Model for Item entity
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sku',
        'tenant_id',
        'status',
    ];

    // protected $hidden = [
    //     'created_at',
    //     'updated_at',
    // ];

    public function tenant() {
        return $this->belongsTo(Tenant::class);
    }

    public function details()
    {
        return $this->hasOne(ItemDetail::class, 'item_id');
    }
}