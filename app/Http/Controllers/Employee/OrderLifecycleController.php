<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderLifecycleController extends Controller
{
    public function markReady(Order $order)
    {
        $order->update([
            'status' => 'ready',
            'ready_at' => now(),
        ]);

        return response()->json($order);
    }

    public function markPickedUp(Order $order, Request $request)
    {
        $order->update([
            'status' => 'picked_up',
            'picked_up_at' => now(),
        ]);

        return response()->json($order);
    }
}
