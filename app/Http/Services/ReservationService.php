<?php

namespace App\Http\Services;

use App\Http\Repositories\ReservationRepo;
use App\Http\Repositories\InventoryItemRepo;
use App\Http\Repositories\InventoryReserveRepo;
use App\Http\Repositories\InventoryTransRepo;
use App\Http\Services\RedisLockService;
use Illuminate\Support\Facades\DB;

class ReservationService
{
    protected $reservationRepo;
    protected $inventoryItemRepo;
    protected $inventoryReserveRepo;
    protected $inventoryTransRepo;
    protected $lockService;

    public function __construct(
        ReservationRepo $reservationRepo,
        InventoryItemRepo $inventoryItemRepo,
        InventoryReserveRepo $inventoryReserveRepo,
        InventoryTransRepo $inventoryTransRepo,
        RedisLockService $lockService
    ) {
        $this->reservationRepo = $reservationRepo;
        $this->inventoryItemRepo = $inventoryItemRepo;
        $this->inventoryReserveRepo = $inventoryReserveRepo;
        $this->inventoryTransRepo = $inventoryTransRepo;
        $this->lockService = $lockService;
    }

    /** Create a held reservation and reserve inventory atomically. */
    public function hold(array $payload)
    {
        $lockKey = $this->lockKey($payload['tenant_id'], $payload['item_id']);

        // Try to acquire Redis-based lock to avoid high-concurrency oversells
        $token = $this->lockService->acquire($lockKey, 10000, 5000, 100);
        if (! $token) {
            throw new \Exception('Could not acquire lock, try again');
        }

        try {
            return DB::transaction(function () use ($payload) {
                $holdSeconds = (int) ($payload['hold_seconds'] ?? config('app.reservation_hold_seconds', 600));
                $expiresAt = now()->addSeconds($holdSeconds);

                $reservationId = $this->reservationRepo->create([
                    'tenant_id' => $payload['tenant_id'],
                    'reference_id' => $payload['idempotency_key'] ?? null,
                    'user_id' => $payload['user_id'] ?? null,
                    'item_id' => $payload['item_id'],
                    'quantity' => $payload['quantity'],
                    'start_at' => $payload['start_at'],
                    'end_at' => $payload['end_at'] ?? null,
                    'status' => 'held',
                    'hold_expires_at' => $expiresAt,
                    'price_amount' => $payload['price_amount'] ?? null,
                    'price_currency' => $payload['price_currency'] ?? 'USD',
                    'meta' => isset($payload['meta']) ? json_encode($payload['meta']) : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // find inventory item record
                $inv = $this->inventoryItemRepo->findByItemId($payload['item_id']);
                if (!$inv) {
                    throw new \Exception('No inventory configured for item');
                }

                if ($inv->available_stock < $payload['quantity']) {
                    throw new \Exception('Insufficient stock');
                }

                // create inventory_reservation and decrement available_stock
                $invResId = $this->inventoryReserveRepo->create($inv->id, $payload['quantity'], $expiresAt, $reservationId);

                // decrement available_stock
                $this->inventoryItemRepo->decrementAvailable($inv->id, $payload['quantity']);

                // write ledger
                $this->inventoryTransRepo->record($inv->id, 'reserve', $payload['quantity'], (string) $reservationId, $expiresAt);

                return $reservationId;
            });
        } finally {
            // best-effort release
            $this->lockService->release($lockKey, $token);
        }
    }

    public function confirm(int $reservationId)
    {
        return DB::transaction(function () use ($reservationId) {
            $reservation = $this->reservationRepo->find($reservationId);
            if (!$reservation) {
                throw new \Exception('Reservation not found');
            }
            if ($reservation->status !== 'held') {
                throw new \Exception('Reservation not in held state');
            }

            // mark reservation confirmed
            $this->reservationRepo->updateStatus($reservationId, 'confirmed', ['hold_expires_at' => null]);

            // mark inventory_reservations committed for this reservation reference
            $invRes = $this->inventoryReserveRepo->findByReservationReference($reservationId);
            foreach ($invRes as $r) {
                $this->inventoryReserveRepo->markCommitted($r->id);
                $this->inventoryTransRepo->record($r->inventory_item_id, 'commit', $r->quantity, (string) $reservationId, null);
            }

            return true;
        });
    }

    public function cancel(int $reservationId, ?string $reason = null)
    {
        return DB::transaction(function () use ($reservationId, $reason) {
            $reservation = $this->reservationRepo->find($reservationId);
            if (!$reservation) {
                throw new \Exception('Reservation not found');
            }

            // release inventory reservations tied to this reservation
            $invRes = $this->inventoryReserveRepo->findByReservationReference($reservationId);
            foreach ($invRes as $r) {
                // only release if still active/committed
                $this->inventoryReserveRepo->markReleased($r->id);
                // increment available stock
                $this->inventoryItemRepo->incrementAvailable($r->inventory_item_id, $r->quantity);
                // ledger
                $this->inventoryTransRepo->record($r->inventory_item_id, 'release', $r->quantity, (string) $reservationId, null);
            }

            $this->reservationRepo->updateStatus($reservationId, 'cancelled', ['cancel_reason' => $reason, 'updated_at' => now()]);

            return true;
        });
    }

    public function expireHeld()
    {
        $held = $this->reservationRepo->findHeldExpired();
        foreach ($held as $res) {
            // release inventory
            $invRes = $this->inventoryReserveRepo->findByReservationReference($res->id);
            foreach ($invRes as $r) {
                $this->inventoryReserveRepo->markReleased($r->id);
                $this->inventoryItemRepo->incrementAvailable($r->inventory_item_id, $r->quantity);
                $this->inventoryTransRepo->record($r->inventory_item_id, 'expire', $r->quantity, (string) $res->id, null);
            }

            $this->reservationRepo->updateStatus($res->id, 'expired');
        }

        return true;
    }

    protected function lockKey($tenantId, $itemId)
    {
        return "tenant:{$tenantId}:lock:item:{$itemId}";
    }

    public function getById(int $id)
    {
        return $this->reservationRepo->find($id);
    }

    public function getByUser(int $userId)
    {
        return $this->reservationRepo->findByUser($userId);
    }

    public function getByItem(int $itemId)
    {
        return $this->reservationRepo->findByItem($itemId);
    }

    public function getByTenant(int $tenantId)
    {
        return $this->reservationRepo->findByTenant($tenantId);
    }
}
