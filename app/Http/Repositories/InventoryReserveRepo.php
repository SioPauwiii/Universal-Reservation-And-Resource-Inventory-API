<?php

namespace App\Http\Repositories;

use Illuminate\Support\Facades\DB;

class InventoryReserveRepo
{
    public function create(int $inventoryItemId, int $quantity, \DateTimeInterface $expiresAt, ?int $reservationId = null)
    {
        return DB::table('inventory_reservations')->insertGetId([
            'inventory_item_id' => $inventoryItemId,
            'quantity' => $quantity,
            'status' => 'active',
            'expires_at' => $expiresAt,
            'reserved_at' => now(),
            'order_reference' => $reservationId ? (string) $reservationId : null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function findByReservationReference($reservationId)
    {
        return DB::table('inventory_reservations')->where('order_reference', $reservationId)->get();
    }

    public function markCommitted(int $id)
    {
        return DB::table('inventory_reservations')->where('id', $id)->update(['status' => 'committed', 'committed_at' => now()]);
    }

    public function markReleased(int $id)
    {
        return DB::table('inventory_reservations')->where('id', $id)->update(['status' => 'released', 'released_at' => now()]);
    }

    public function expireDue()
    {
        return DB::table('inventory_reservations')
            ->where('status', 'active')
            ->where('expires_at', '<=', now())
            ->get();
    }
}
