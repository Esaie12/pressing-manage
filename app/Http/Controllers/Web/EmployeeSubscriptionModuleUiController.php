<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
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
            'orders' => SubscriptionOrder::where('pressing_id', $employee->pressing_id)
                ->with('contract.client', 'employee')
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
            'order_date' => ['required', 'date'],
            'pickup_date' => ['nullable', 'date', 'after_or_equal:order_date'],
            'items_count' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        SubscriptionOrder::create($data + [
            'pressing_id' => $employee->pressing_id,
            'agency_id' => $employee->agency_id,
            'employee_id' => $employee->id,
            'status' => 'pending',
            'reference' => 'ABO-EMP-'.now()->format('ymdHis').'-'.random_int(100, 999),
        ]);

        return redirect()->route('employee.ui.subscription-orders')->with('success', 'Commande abonnement enregistrée.');
    }

    public function updateStatus(Request $request, SubscriptionOrder $order)
    {
        $pressing = Pressing::findOrFail(Auth::user()->pressing_id);
        abort_if(! $pressing->module_subscription_enabled, 403, 'Module Abonnements non activé.');
        abort_unless($order->pressing_id === Auth::user()->pressing_id, 403);

        $data = $request->validate(['status' => ['required', 'in:pending,ready,delivered']]);
        $order->update(['status' => $data['status']]);

        return redirect()->route('employee.ui.subscription-orders')->with('success', 'Statut mis à jour.');
    }
}
