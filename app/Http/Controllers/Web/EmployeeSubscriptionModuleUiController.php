<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Agency;
use App\Models\Pressing;
use App\Models\SubscriptionContract;
use App\Models\SubscriptionOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeSubscriptionModuleUiController extends Controller
{
    public function index()
    {
        $employee = Auth::user();
        $pressing = Pressing::findOrFail($employee->pressing_id);
        abort_if(! $pressing->module_subscription_enabled, 403, 'Module Abonnements non activé.');

        return view('employee/subscription-orders', [
            'contracts' => SubscriptionContract::where('pressing_id', $employee->pressing_id)
                ->where('is_active', true)
                ->with('client')
                ->orderByDesc('id')
                ->get(),
            'agencies' => Agency::where('pressing_id', $employee->pressing_id)->orderBy('name')->get(),
            'orders' => SubscriptionOrder::where('pressing_id', $employee->pressing_id)
                ->with('contract.client', 'employee', 'agency')
                ->orderByDesc('id')
                ->get(),
            'statuses' => ['pending' => 'En préparation', 'ready' => 'Prête', 'delivered' => 'Livrée'],
        ]);
    }

    public function store(Request $request)
    {
        $employee = Auth::user();
        $pressing = Pressing::findOrFail($employee->pressing_id);
        abort_if(! $pressing->module_subscription_enabled, 403, 'Module Abonnements non activé.');

        $data = $request->validate([
            'subscription_contract_id' => ['required', 'exists:subscription_contracts,id'],
            'agency_id' => ['required', 'exists:agencies,id'],
            'order_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        SubscriptionOrder::create($data + [
            'pressing_id' => $employee->pressing_id,
            'employee_id' => $employee->id,
            'pickup_date' => null,
            'items_count' => 1,
            'status' => 'pending',
            'reference' => 'ABO-EMP-'.now()->format('ymdHis').'-'.random_int(100, 999),
        ]);

        return redirect()->route('employee.ui.subscription-orders')->with('success', 'Commande abonnement enregistrée.');
    }

    public function markReady(SubscriptionOrder $order)
    {
        $pressing = Pressing::findOrFail(Auth::user()->pressing_id);
        abort_if(! $pressing->module_subscription_enabled, 403, 'Module Abonnements non activé.');
        abort_unless($order->pressing_id === Auth::user()->pressing_id, 403);

        if ($order->status !== 'pending') {
            return redirect()->route('employee.ui.subscription-orders')->with('error', 'Seules les commandes en préparation peuvent passer à prête.');
        }

        $order->update(['status' => 'ready']);

        return redirect()->route('employee.ui.subscription-orders')->with('success', 'Commande marquée prête.');
    }

    public function markDelivered(SubscriptionOrder $order)
    {
        $pressing = Pressing::findOrFail(Auth::user()->pressing_id);
        abort_if(! $pressing->module_subscription_enabled, 403, 'Module Abonnements non activé.');
        abort_unless($order->pressing_id === Auth::user()->pressing_id, 403);

        if ($order->status !== 'ready') {
            return redirect()->route('employee.ui.subscription-orders')->with('error', 'Seules les commandes prêtes peuvent être livrées.');
        }

        $order->update(['status' => 'delivered']);

        return redirect()->route('employee.ui.subscription-orders')->with('success', 'Commande marquée livrée.');
    }
}
