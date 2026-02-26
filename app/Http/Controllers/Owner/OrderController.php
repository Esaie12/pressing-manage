<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Agency;
use App\Models\Client;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Pressing;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::whereHas('items', fn ($q) => $q)
            ->where('agency_id', $request->user()->agency_id ?? $request->query('agency_id'))
            ->latest()
            ->get();

        return response()->json($orders);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'agency_id' => ['required', 'exists:agencies,id'],
            'client.name' => ['required', 'string', 'max:255'],
            'client.phone' => ['nullable', 'string'],
            'client.email' => ['nullable', 'email'],
            'paid_advance' => ['boolean'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.service_id' => ['required', 'exists:services,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        $order = DB::transaction(function () use ($request, $data) {
            $client = Client::firstOrCreate([
                'agency_id' => $data['agency_id'],
                'phone' => $data['client']['phone'] ?? null,
            ], [
                'name' => $data['client']['name'],
                'email' => $data['client']['email'] ?? null,
            ]);

            $pressingId = $request->user()->pressing_id ?? Agency::findOrFail($data['agency_id'])->pressing_id;
            $pressing = Pressing::with('invoiceSetting')->findOrFail($pressingId);

            $order = Order::create([
                'agency_id' => $data['agency_id'],
                'client_id' => $client->id,
                'employee_id' => $request->user()->id,
                'reference' => $this->generateOrderReference($pressing),
                'status' => 'pending',
                'paid_advance' => $data['paid_advance'] ?? false,
                'total' => 0,
            ]);

            $total = 0;
            foreach ($data['items'] as $item) {
                $service = Service::findOrFail($item['service_id']);
                $lineTotal = $service->price * $item['quantity'];
                $total += $lineTotal;

                OrderItem::create([
                    'order_id' => $order->id,
                    'service_id' => $service->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $service->price,
                    'line_total' => $lineTotal,
                ]);
            }

            $order->update(['total' => $total]);

            return $order->load('items');
        });

        return response()->json($order, 201);
    }

    public function stats(Request $request)
    {
        $agencyId = $request->query('agency_id');
        $query = Order::query();

        if ($agencyId) {
            $query->where('agency_id', $agencyId);
        }

        return response()->json([
            'total_orders' => (clone $query)->count(),
            'total_revenue' => (clone $query)->sum('total'),
            'advance_paid_count' => (clone $query)->where('paid_advance', true)->count(),
            'picked_up_count' => (clone $query)->whereNotNull('picked_up_at')->count(),
        ]);
    }

    private function generateOrderReference(Pressing $pressing): string
    {
        $setting = $pressing->invoiceSetting;
        $prefix = strtoupper($setting?->invoice_order_reference_prefix ?? 'CMD');

        if (! $setting || ($setting->invoice_reference_mode ?? 'random') !== 'custom' || empty($setting->invoice_reference_parts)) {
            return $prefix.'-'.strtoupper(uniqid());
        }

        $nextId = ((int) Order::max('id')) + 1;
        $separator = in_array($setting->invoice_reference_separator, ['-', '/'], true) ? $setting->invoice_reference_separator : '-';
        $date = now();

        $map = [
            'ID' => (string) $nextId,
            'ANNEE' => $date->format('Y'),
            'MOIS' => $date->format('m'),
            'JOUR' => $date->format('d'),
        ];

        $parts = [$prefix];
        foreach ((array) $setting->invoice_reference_parts as $part) {
            $parts[] = $map[$part] ?? $part;
        }

        return implode($separator, $parts);
    }
}
