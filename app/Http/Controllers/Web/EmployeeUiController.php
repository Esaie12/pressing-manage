<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Agency;
use App\Models\Client;
use App\Models\EmployeeRequest;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\OrderItem;
use App\Models\Service;
use App\Models\User;
use App\Models\UserNotification;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EmployeeUiController extends Controller
{
    public function dashboard(Request $request)
    {
        abort_unless(Auth::user()->is_active, 403);

        $employee = Auth::user();
        $from = $request->query('from', now()->toDateString());
        $to = $request->query('to', now()->toDateString());

        $query = Order::where('employee_id', $employee->id);
        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }

        $todayRevenue = Order::where('employee_id', $employee->id)
            ->whereDate('created_at', now()->toDateString())
            ->sum('total');

        $last7Labels = [];
        $last7Count = [];
        $last7Revenue = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $last7Labels[] = $date->translatedFormat('D');
            $daily = Order::where('employee_id', $employee->id)->whereDate('created_at', $date->toDateString());
            $last7Count[] = (clone $daily)->count();
            $last7Revenue[] = (float) (clone $daily)->sum('total');
        }

        $greeting = now()->hour >= 12 ? 'Bonsoir' : 'Bonjour';
        $closingAlert = null;
        if ($employee->pressing?->closing_time) {
            $closingTime = now()->setTimeFromTimeString($employee->pressing->closing_time);
            if (now()->between($closingTime->copy()->subHour(), $closingTime)) {
                $closingAlert = "Il va bientôt être l'heure de fermer, {$employee->name}.";
            }
        }

        return view('employee.dashboard', [
            'inProgress' => Order::where('employee_id', $employee->id)->whereNull('picked_up_at')->count(),
            'pickedUp' => Order::where('employee_id', $employee->id)->whereNotNull('picked_up_at')->count(),
            'periodOrders' => (clone $query)->count(),
            'periodRevenue' => (clone $query)->sum('total'),
            'todayRevenue' => $todayRevenue,
            'from' => $from,
            'to' => $to,
            'last7Labels' => $last7Labels,
            'last7Count' => $last7Count,
            'last7Revenue' => $last7Revenue,
            'greeting' => $greeting,
            'closingAlert' => $closingAlert,
        ]);
    }

    public function requests()
    {
        abort_unless(Auth::user()->is_active, 403);

        return view('employee.requests', [
            'myRequests' => EmployeeRequest::where('employee_id', Auth::id())->latest()->get(),
        ]);
    }

    public function orders(Request $request)
    {
        abort_unless(Auth::user()->is_active, 403);

        $status = $request->query('status');
        $arriveDate = $request->query('arrival_date');
        $pickupDate = $request->query('pickup_date');

        $orders = Order::where('agency_id', Auth::user()->agency_id)
            ->with(['items.service', 'client']);

        if ($status) {
            $orders->where('status', $status);
        }
        if ($arriveDate) {
            $orders->whereDate('created_at', $arriveDate);
        }
        if ($pickupDate) {
            $orders->whereDate('picked_up_at', $pickupDate);
        }

        return view('employee.orders', [
            'orders' => $orders->latest()->get(),
            'services' => Service::where('agency_id', Auth::user()->agency_id)->where('is_active', true)->orderBy('name')->get(),
            'orderStatuses' => OrderStatus::orderBy('sort_order')->get(),
            'filters' => [
                'status' => $status,
                'arrival_date' => $arriveDate,
                'pickup_date' => $pickupDate,
            ],
        ]);
    }

    public function invoices()
    {
        abort_unless(Auth::user()->is_active, 403);

        return view('employee.invoices', [
            'invoices' => Invoice::where('pressing_id', Auth::user()->pressing_id)
                ->with(['order.client', 'order.agency'])
                ->latest()
                ->get(),
        ]);
    }

    public function showInvoice(Invoice $invoice)
    {
        abort_unless(Auth::user()->is_active, 403);
        abort_unless($invoice->pressing_id === Auth::user()->pressing_id, 403);

        $invoice->load(['order.items.service', 'order.client', 'order.agency', 'pressing']);

        return view('employee.invoice-show', ['invoice' => $invoice]);
    }


    public function destroyInvoice(Invoice $invoice)
    {
        abort_unless($invoice->pressing_id === Auth::user()->pressing_id, 403);
        $number = $invoice->invoice_number;
        $invoice->delete();

        $owner = User::where('pressing_id', Auth::user()->pressing_id)->where('role', User::ROLE_OWNER)->first();
        if ($owner) {
            UserNotification::create([
                'user_id' => $owner->id,
                'type' => 'invoice_deleted',
                'title' => 'Facture supprimée',
                'message' => Auth::user()->name.' a supprimé la facture '.$number,
            ]);
        }

        return redirect()->route('employee.ui.invoices')->with('success', 'Facture supprimée.');
    }

    public function createRequest(Request $request)
    {
        abort_unless(Auth::user()->is_active, 403);

        $data = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:1000'],
        ]);

        $employee = Auth::user();

        EmployeeRequest::create([
            'pressing_id' => $employee->pressing_id,
            'agency_id' => $employee->agency_id,
            'employee_id' => $employee->id,
            'subject' => $data['subject'],
            'message' => $data['message'],
            'status' => 'pending',
        ]);

        $owner = User::where('pressing_id', $employee->pressing_id)->where('role', User::ROLE_OWNER)->first();
        if ($owner) {
            UserNotification::create([
                'user_id' => $owner->id,
                'type' => 'employee_request',
                'title' => 'Nouvelle demande employé',
                'message' => $employee->name.' a signalé: '.$data['subject'],
                'data' => ['employee_id' => $employee->id],
            ]);
        }

        return redirect()->route('employee.ui.requests')->with('success', 'Demande envoyée au propriétaire.');
    }

    public function updateRequest(Request $request, EmployeeRequest $employeeRequest)
    {
        abort_unless($employeeRequest->employee_id === Auth::id(), 403);
        abort_if($employeeRequest->status === 'read', 422, 'Cette demande est déjà marquée lue.');

        $data = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:1000'],
        ]);

        $employeeRequest->update($data);

        return redirect()->route('employee.ui.requests')->with('success', 'Demande modifiée.');
    }

    public function destroyRequest(EmployeeRequest $employeeRequest)
    {
        abort_unless($employeeRequest->employee_id === Auth::id(), 403);
        abort_if($employeeRequest->status === 'read', 422, 'Impossible de supprimer une demande déjà lue.');

        $employeeRequest->delete();

        return redirect()->route('employee.ui.requests')->with('success', 'Demande supprimée.');
    }

    public function storeOrder(Request $request)
    {
        abort_unless(Auth::user()->is_active, 403);
        $data = $this->validateOrderPayload($request);

        $agency = Agency::where('id', Auth::user()->agency_id)
            ->where('pressing_id', Auth::user()->pressing_id)
            ->where('is_active', true)
            ->firstOrFail();

        DB::transaction(function () use ($data, $agency) {
            [$order, $total] = $this->persistOrderFromPayload($data, $agency, null);

            Invoice::create([
                'order_id' => $order->id,
                'pressing_id' => Auth::user()->pressing_id,
                'invoice_number' => 'FAC-'.strtoupper(uniqid()),
                'amount' => $total,
                'issued_at' => now()->toDateString(),
            ]);

            if ((float) $order->advance_amount > 0) {
                Transaction::create([
                    'pressing_id' => Auth::user()->pressing_id,
                    'agency_id' => $order->agency_id,
                    'user_id' => Auth::id(),
                    'order_id' => $order->id,
                    'type' => 'encaissement',
                    'amount' => $order->advance_amount,
                    'payment_method' => $order->payment_method,
                    'label' => 'Acompte commande '.$order->reference,
                    'happened_at' => now(),
                ]);
            }
        });

        return redirect()->route('employee.ui.orders')->with('success', 'Commande enregistrée par employé.');
    }

    public function editOrder(Order $order)
    {
        abort_unless(Auth::user()->is_active, 403);
        abort_unless($order->agency_id === Auth::user()->agency_id, 403);
        $order->load(['items.service', 'client']);

        return view('employee.order-edit', [
            'order' => $order,
            'services' => Service::where('agency_id', Auth::user()->agency_id)->where('is_active', true)->orderBy('name')->get(),
            'orderStatuses' => OrderStatus::orderBy('sort_order')->get(),
        ]);
    }

    public function updateOrder(Request $request, Order $order)
    {
        abort_unless(Auth::user()->is_active, 403);
        abort_unless($order->agency_id === Auth::user()->agency_id, 403);

        $data = $this->validateOrderPayload($request);
        $agency = Agency::where('id', Auth::user()->agency_id)
            ->where('pressing_id', Auth::user()->pressing_id)
            ->where('is_active', true)
            ->firstOrFail();

        DB::transaction(function () use ($data, $agency, $order) {
            $order->items()->delete();
            [$updated, $total] = $this->persistOrderFromPayload($data, $agency, $order);

            if ($updated->invoice) {
                $updated->invoice->update(['amount' => $total]);
            }
        });

        return redirect()->route('employee.ui.orders')->with('success', 'Commande modifiée.');
    }

    public function addPayment(Request $request, Order $order)
    {
        abort_unless($order->agency_id === Auth::user()->agency_id, 403);

        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
            'payment_method' => ['nullable', 'in:cash,wave,orange_money,card'],
        ]);

        $remaining = max(0, (float) $order->total - (float) $order->advance_amount);
        $amount = min($remaining, (float) $data['amount']);

        if ($amount <= 0) {
            return redirect()->route('employee.ui.orders')->with('error', 'Commande déjà totalement payée.');
        }

        $order->advance_amount = (float) $order->advance_amount + $amount;
        $order->paid_advance = $order->advance_amount > 0;
        if (! empty($data['payment_method'])) {
            $order->payment_method = $data['payment_method'];
        }
        $order->save();

        Transaction::create([
            'pressing_id' => Auth::user()->pressing_id,
            'agency_id' => $order->agency_id,
            'user_id' => Auth::id(),
            'order_id' => $order->id,
            'type' => 'encaissement',
            'amount' => $amount,
            'payment_method' => $data['payment_method'] ?? $order->payment_method,
            'label' => 'Paiement commande '.$order->reference,
            'happened_at' => now(),
        ]);

        return redirect()->route('employee.ui.orders')->with('success', 'Paiement ajouté avec succès.');
    }

    public function applyDiscount(Request $request, Order $order)
    {
        abort_unless($order->agency_id === Auth::user()->agency_id, 403);

        $data = $request->validate([
            'discount_amount' => ['required', 'numeric', 'min:1'],
        ]);

        $discount = min((float) $data['discount_amount'], (float) $order->total);
        $order->discount_amount = (float) $order->discount_amount + $discount;
        $order->total = max(0, (float) $order->total - $discount);
        $order->save();

        if ($order->invoice) {
            $order->invoice->update(['amount' => $order->total]);
        }

        return redirect()->route('employee.ui.orders')->with('success', 'Réduction appliquée.');
    }

    public function transactions()
    {
        return view('employee.transactions', [
            'transactions' => Transaction::where('agency_id', Auth::user()->agency_id)
                ->with(['user', 'order', 'expense'])
                ->latest('happened_at')
                ->latest()
                ->get(),
        ]);
    }

    public function markReady(Order $order)
    {
        abort_unless($order->agency_id === Auth::user()->agency_id, 403);

        $order->update(['status' => 'ready', 'ready_at' => now()]);

        return redirect()->route('employee.ui.orders')->with('success', 'Commande marquée prête.');
    }

    public function markPickedUp(Order $order)
    {
        abort_unless($order->agency_id === Auth::user()->agency_id, 403);

        if ((float) $order->advance_amount < (float) $order->total) {
            return redirect()->route('employee.ui.orders')->with('error', 'Commande non totalement payée.');
        }

        $order->update(['status' => 'picked_up', 'picked_up_at' => now()]);

        return redirect()->route('employee.ui.orders')->with('success', 'Commande marquée retirée.');
    }

    private function validateOrderPayload(Request $request): array
    {
        return $request->validate([
            'client_name' => ['required', 'string', 'max:255'],
            'client_phone' => ['nullable', 'string', 'max:50'],
            'client_email' => ['nullable', 'email'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.service_id' => ['required', 'exists:services,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'paid_advance' => ['nullable', 'boolean'],
            'advance_amount' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['nullable', 'in:cash,wave,orange_money,card'],
            'status' => ['nullable', 'in:pending,ready,picked_up'],
            'is_delivery' => ['nullable', 'boolean'],
            'delivery_address' => ['nullable', 'string', 'max:255'],
            'delivery_fee' => ['nullable', 'numeric', 'min:0'],
        ]);
    }

    private function persistOrderFromPayload(array $data, Agency $agency, ?Order $existing): array
    {
        $client = $existing?->client;
        if (! $client) {
            $client = Client::create([
                'agency_id' => $agency->id,
                'name' => $data['client_name'],
                'phone' => $data['client_phone'] ?? null,
                'email' => $data['client_email'] ?? null,
            ]);
        } else {
            $client->update([
                'agency_id' => $agency->id,
                'name' => $data['client_name'],
                'phone' => $data['client_phone'] ?? null,
                'email' => $data['client_email'] ?? null,
            ]);
        }

        $total = 0;
        $preparedItems = [];
        foreach ($data['items'] as $item) {
            $service = Service::where('id', $item['service_id'])
                ->where('agency_id', $agency->id)
                ->where('is_active', true)
                ->firstOrFail();

            $lineTotal = $service->price * $item['quantity'];
            $total += $lineTotal;

            $preparedItems[] = [
                'service_id' => $service->id,
                'quantity' => $item['quantity'],
                'unit_price' => $service->price,
                'line_total' => $lineTotal,
            ];
        }

        $deliveryFee = (float) ($data['delivery_fee'] ?? 0);
        $isDelivery = (bool) ($data['is_delivery'] ?? false);
        if ($isDelivery) {
            $total += $deliveryFee;
        } else {
            $deliveryFee = 0;
        }

        $discountAmount = min((float) ($existing?->discount_amount ?? 0), $total);
        $total -= $discountAmount;

        $advanceAmount = min((float) ($data['advance_amount'] ?? 0), $total);

        $order = $existing ?? new Order();
        if (! $existing) {
            $order->reference = 'CMD-'.strtoupper(uniqid());
            $order->employee_id = Auth::id();
        }

        $order->fill([
            'agency_id' => $agency->id,
            'client_id' => $client->id,
            'status' => $data['status'] ?? ($existing?->status ?? 'pending'),
            'paid_advance' => (bool) ($data['paid_advance'] ?? false),
            'advance_amount' => $advanceAmount,
            'payment_method' => $data['payment_method'] ?? null,
            'is_delivery' => $isDelivery,
            'delivery_address' => $isDelivery ? ($data['delivery_address'] ?? null) : null,
            'delivery_fee' => $deliveryFee,
            'discount_amount' => $discountAmount,
            'total' => $total,
        ]);
        $order->save();

        foreach ($preparedItems as $item) {
            OrderItem::create($item + ['order_id' => $order->id]);
        }

        return [$order, $total];
    }

}