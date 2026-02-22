<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Agency;
use App\Models\SubscriptionClient;
use App\Models\SubscriptionContract;
use App\Models\SubscriptionOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionModuleUiController extends Controller
{
    public function index(Request $request)
    {
        $pressingId = Auth::user()->pressing_id;
        $section = in_array($request->query('section'), ['clients', 'contracts', 'orders'], true)
            ? $request->query('section')
            : 'clients';

        return view('owner.subscription-module', [
            'section' => $section,
            'clients' => SubscriptionClient::where('pressing_id', $pressingId)->orderByDesc('id')->get(),
            'contracts' => SubscriptionContract::where('pressing_id', $pressingId)->with('client')->orderByDesc('id')->get(),
            'orders' => SubscriptionOrder::where('pressing_id', $pressingId)->with('contract.client')->orderByDesc('id')->get(),
            'agencies' => Agency::where('pressing_id', $pressingId)->orderBy('name')->get(),
            'statuses' => ['pending' => 'En préparation', 'ready' => 'Prête', 'delivered' => 'Livrée'],
            'frequencies' => ['day' => 'Jour', 'week' => 'Semaine', 'month' => 'Mois'],
        ]);
    }

    public function storeClient(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'company_type' => ['nullable', 'string', 'max:120'],
            'contact_person' => ['nullable', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:40'],
            'email' => ['nullable', 'email', 'max:120'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

        SubscriptionClient::create($data + ['pressing_id' => Auth::user()->pressing_id, 'is_active' => true]);

        return redirect()->route('owner.ui.subscriptions-module', ['section' => 'clients'])->with('success', 'Client abonnement ajouté.');
    }

    public function updateClient(Request $request, SubscriptionClient $client)
    {
        abort_unless($client->pressing_id === Auth::user()->pressing_id, 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'company_type' => ['nullable', 'string', 'max:120'],
            'contact_person' => ['nullable', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:40'],
            'email' => ['nullable', 'email', 'max:120'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

        $client->update($data);

        return redirect()->route('owner.ui.subscriptions-module', ['section' => 'clients'])->with('success', 'Client abonnement modifié.');
    }

    public function destroyClient(SubscriptionClient $client)
    {
        abort_unless($client->pressing_id === Auth::user()->pressing_id, 403);
        $client->update(['is_active' => false]);
        $client->delete();

        return redirect()->route('owner.ui.subscriptions-module', ['section' => 'clients'])->with('success', 'Client supprimé (soft delete).');
    }

    public function storeContract(Request $request)
    {
        $data = $request->validate([
            'subscription_client_id' => ['required', 'exists:subscription_clients,id'],
            'title' => ['required', 'string', 'max:140'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'frequency' => ['required', 'in:day,week,month'],
            'price' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        SubscriptionContract::create($data + ['pressing_id' => Auth::user()->pressing_id, 'is_active' => true]);

        return redirect()->route('owner.ui.subscriptions-module', ['section' => 'contracts'])->with('success', 'Contrat ajouté.');
    }

    public function updateContract(Request $request, SubscriptionContract $contract)
    {
        abort_unless($contract->pressing_id === Auth::user()->pressing_id, 403);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:140'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'frequency' => ['required', 'in:day,week,month'],
            'price' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $contract->update($data + ['is_active' => (bool) ($data['is_active'] ?? false)]);

        return redirect()->route('owner.ui.subscriptions-module', ['section' => 'contracts'])->with('success', 'Contrat modifié.');
    }

    public function storeOrder(Request $request)
    {
        $data = $request->validate([
            'subscription_contract_id' => ['required', 'exists:subscription_contracts,id'],
            'agency_id' => ['nullable', 'exists:agencies,id'],
            'order_date' => ['required', 'date'],
            'pickup_date' => ['nullable', 'date', 'after_or_equal:order_date'],
            'items_count' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        SubscriptionOrder::create($data + [
            'pressing_id' => Auth::user()->pressing_id,
            'employee_id' => Auth::id(),
            'status' => 'pending',
            'reference' => 'ABO-'.now()->format('ymdHis').'-'.random_int(100, 999),
        ]);

        return redirect()->route('owner.ui.subscriptions-module', ['section' => 'orders'])->with('success', 'Commande abonnement créée.');
    }

    public function updateOrderStatus(Request $request, SubscriptionOrder $order)
    {
        abort_unless($order->pressing_id === Auth::user()->pressing_id, 403);
        $data = $request->validate(['status' => ['required', 'in:pending,ready,delivered']]);
        $order->update(['status' => $data['status']]);

        return redirect()->route('owner.ui.subscriptions-module', ['section' => 'orders'])->with('success', 'Statut de la commande mis à jour.');
    }
}
