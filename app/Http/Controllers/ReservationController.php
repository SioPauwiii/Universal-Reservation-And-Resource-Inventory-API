<?php

namespace App\Http\Controllers;

use App\Http\Requests\ItemReservationRequest;
use App\Http\Services\ReservationService;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    protected $service;

    public function __construct(ReservationService $service)
    {
        $this->service = $service;
    }

    public function reserve(ItemReservationRequest $req)
    {
        $id = $this->service->hold($req->validated());
        return response()->json(['reservation_id' => $id], 201);
    }

    public function confirm($id)
    {
        $this->service->confirm((int) $id);
        return response()->json(['status' => 'confirmed']);
    }

    public function cancel(Request $req, $id)
    {
        $reason = $req->input('reason');
        $this->service->cancel((int) $id, $reason);
        return response()->json(['status' => 'cancelled']);
    }

    public function expire() {
        $this->service->expireHeld();
        return response()->json(['status' => 'expired-run']);
    }

    public function fetchById($id)
    {
        $reservation = $this->service->getById((int) $id);
        return response()->json(['reservation' => $reservation]);
    }

    public function fetchByUser($userId)
    {
        $reservations = $this->service->getByUser((int) $userId);
        return response()->json(['reservations' => $reservations]);
    }

    public function fetchByItem($itemId)
    {
        $reservations = $this->service->getByItem((int) $itemId);
        return response()->json(['reservations' => $reservations]);
    }

    public function fetchByTenant($tenantId)
    {
        $reservations = $this->service->getByTenant((int) $tenantId);
        return response()->json(['reservations' => $reservations]);
    }
}
