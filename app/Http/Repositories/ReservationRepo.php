<?php

namespace App\Http\Repositories;

use Illuminate\Support\Facades\DB;

class ReservationRepo
{
    public function create(array $payload)
    {
        return DB::table('reservations')->insertGetId($payload);
    }

    public function find(int $id)
    {
        return DB::table('reservations')->where('id', $id)->first();
    }

    public function findByUser(int $userId)
    {
        return DB::table('reservations')->where('user_id', $userId)->get();
    }

    public function findByItem(int $itemId)
    {
        return DB::table('reservations')->where('item_id', $itemId)->get();
    }

    public function findByTenant(int $tenantId)
    {
        return DB::table('reservations')->where('tenant_id', $tenantId)->get();
    }

    public function updateStatus(int $id, string $status, array $extra = [])
    {
        $data = array_merge(['status' => $status], $extra);
        return DB::table('reservations')->where('id', $id)->update($data);
    }

    public function findHeldExpired()
    {
        return DB::table('reservations')
            ->where('status', 'held')
            ->whereNotNull('hold_expires_at')
            ->where('hold_expires_at', '<=', now())
            ->get();
    }

    public function attachOrder(int $id, string $orderReference)
    {
        return DB::table('reservations')->where('id', $id)->update(['reference_id' => $orderReference]);
    }
}
