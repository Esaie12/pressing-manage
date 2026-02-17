<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Agency;
use App\Models\CategoryExpense;
use App\Models\Client;
use App\Models\EmployeeRequest;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\OrderItem;
use App\Models\OwnerSubscription;
use App\Models\Pressing;
use App\Models\Service;
use App\Models\SubscriptionPlan;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class OwnerUiController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = Auth::user();
        $selectedAgencyId = $request->query('agency_id');

        $orders = Order::whereHas('agency', fn ($q) => $q->where('pressing_id', $user->pressing_id));
        if ($selectedAgencyId) {
            $orders->where('agency_id', $selectedAgencyId);
        }

        $pressing = Pressing::find($user->pressing_id);
        $greeting = now()->hour >= 12 ? 'Bonsoir' : 'Bonjour';
        $closingAlert = null;
        if ($pressing?->closing_time) {
            $closingTime = now()->setTimeFromTimeString($pressing->closing_time);
            if (now()->between($closingTime->copy()->subHour(), $closingTime)) {
                $closingAlert = "Il va bientôt être l'heure de fermer, {$user->name}.";
            }
        }

        $todayCash = (clone $orders)->whereDate('created_at', now()->toDateString())->sum('advance_amount');

        return view('owner.dashboard', [
            'agenciesCount' => Agency::where('pressing_id', $user->pressing_id)->count(),
            'employeesCount' => User::where('pressing_id', $user->pressing_id)->where('role', User::ROLE_EMPLOYEE)->count(),
            'ordersCount' => (clone $orders)->count(),
            'todayCash' => $todayCash,
            'revenue' => (clone $orders)->sum('total'),
            'greeting' => $greeting,
            'closingAlert' => $closingAlert,
            'agencies' => Agency::where('pressing_id', $user->pressing_id)->orderBy('name')->get(),
            'selectedAgencyId' => $selectedAgencyId,
        ]);
    }

    public function agencies()
    {
        return view('owner.agencies', [
            'agencies' => Agency::where('pressing_id', Auth::user()->pressing_id)->latest()->get(),
        ]);
    }

    public function storeAgency(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

        Agency::create($data + ['pressing_id' => Auth::user()->pressing_id, 'is_active' => true]);

        return redirect()->route('owner.ui.agencies')->with('success', 'Agence créée.');
    }

    public function toggleAgency(Agency $agency)
    {
        abort_unless($agency->pressing_id === Auth::user()->pressing_id, 403);
        $agency->update(['is_active' => ! $agency->is_active]);

        return redirect()->route('owner.ui.agencies')->with('success', 'Statut agence mis à jour.');
    }

    public function employees()
    {
        return view('owner.employees', [
            'employees' => User::where('pressing_id', Auth::user()->pressing_id)
                ->where('role', User::ROLE_EMPLOYEE)
                ->with('agency')
                ->latest()
                ->get(),
            'agencies' => Agency::where('pressing_id', Auth::user()->pressing_id)->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function storeEmployee(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'agency_id' => ['required', 'exists:agencies,id'],
            'gender' => ['nullable', 'in:homme,femme,autre'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

        $agency = Agency::where('id', $data['agency_id'])
            ->where('pressing_id', Auth::user()->pressing_id)
            ->where('is_active', true)
            ->firstOrFail();

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => User::ROLE_EMPLOYEE,
            'is_active' => true,
            'pressing_id' => Auth::user()->pressing_id,
            'agency_id' => $agency->id,
            'gender' => $data['gender'] ?? null,
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
        ]);

        return redirect()->route('owner.ui.employees')->with('success', 'Employé ajouté.');
    }

    public function toggleEmployee(User $employee)
    {
        abort_unless($employee->pressing_id === Auth::user()->pressing_id && $employee->role === User::ROLE_EMPLOYEE, 403);
        $employee->update(['is_active' => ! $employee->is_active]);

        return redirect()->route('owner.ui.employees')->with('success', 'Statut employé mis à jour.');
    }

    public function updateEmployeePassword(Request $request, User $employee)
    {
        abort_unless($employee->pressing_id === Auth::user()->pressing_id && $employee->role === User::ROLE_EMPLOYEE, 403);

        $data = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $employee->update(['password' => Hash::make($data['password'])]);

        return redirect()->route('owner.ui.employees')->with('success', 'Nouveau mot de passe employé enregistré.');
    }

    public function services(Request $request)
    {
        $showDeleted = (bool) $request->query('show_deleted');

        $services = Service::whereHas('agency', fn ($q) => $q->where('pressing_id', Auth::user()->pressing_id))
            ->with('agency');

        if ($showDeleted) {
            $services->withTrashed();
        }

        return view('owner.services', [
            'services' => $services->latest()->get(),
            'agencies' => Agency::where('pressing_id', Auth::user()->pressing_id)->where('is_active', true)->orderBy('name')->get(),
            'filters' => ['show_deleted' => $showDeleted],
        ]);
    }

    public function storeService(Request $request)
    {
        $data = $request->validate([
            'agency_id' => ['required', 'exists:agencies,id'],
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
        ]);

        $agency = Agency::where('id', $data['agency_id'])
            ->where('pressing_id', Auth::user()->pressing_id)
            ->where('is_active', true)
            ->firstOrFail();

        Service::create($data + ['agency_id' => $agency->id, 'is_active' => true]);

        return redirect()->route('owner.ui.services')->with('success', 'Service ajouté.');
    }



    public function updateService(Request $request, Service $service)
    {
        abort_unless($service->agency && $service->agency->pressing_id === Auth::user()->pressing_id, 403);

        $data = $request->validate([
            'agency_id' => ['required', 'exists:agencies,id'],
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
        ]);

        Agency::where('id', $data['agency_id'])->where('pressing_id', Auth::user()->pressing_id)->where('is_active', true)->firstOrFail();

        $service->update($data);

        return redirect()->route('owner.ui.services')->with('success', 'Service modifié.');
    }

    public function toggleService(Service $service)
    {
        abort_unless($service->agency && $service->agency->pressing_id === Auth::user()->pressing_id, 403);
        $service->update(['is_active' => ! $service->is_active]);

        return redirect()->route('owner.ui.services')->with('success', 'Statut service mis à jour.');
    }

    public function destroyService(Service $service)
    {
        abort_unless($service->agency && $service->agency->pressing_id === Auth::user()->pressing_id, 403);
        $service->delete();

        return redirect()->route('owner.ui.services')->with('success', 'Service supprimé (soft delete).');
    }

    public function forceDeleteService(int $service)
    {
        $serviceModel = Service::withTrashed()->findOrFail($service);
        abort_unless($serviceModel->agency && $serviceModel->agency->pressing_id === Auth::user()->pressing_id, 403);
        abort_if(! $serviceModel->trashed(), 422, 'Le service doit être supprimé avant suppression définitive.');

        $serviceModel->forceDelete();

        return redirect()->route('owner.ui.services', ['show_deleted' => 1])->with('success', 'Service supprimé définitivement.');
    }

    public function orders(Request $request)
    {
        $status = $request->query('status');
        $arriveDate = $request->query('arrival_date');
        $pickupDate = $request->query('pickup_date');
        $showDeleted = (bool) $request->query('show_deleted');

        $ordersQuery = Order::whereHas('agency', fn ($q) => $q->where('pressing_id', Auth::user()->pressing_id))
            ->with(['agency', 'client', 'employee', 'items.service', 'invoice']);

        if ($showDeleted) {
            $ordersQuery->withTrashed();
        }
        if ($status) {
            $ordersQuery->where('status', $status);
        }
        if ($arriveDate) {
            $ordersQuery->whereDate('created_at', $arriveDate);
        }
        if ($pickupDate) {
            $ordersQuery->whereDate('picked_up_at', $pickupDate);
        }

        return view('owner.orders', [
            'orders' => $ordersQuery->latest()->get(),
            'agencies' => Agency::where('pressing_id', Auth::user()->pressing_id)->where('is_active', true)->orderBy('name')->get(),
            'services' => Service::whereHas('agency', fn ($q) => $q->where('pressing_id', Auth::user()->pressing_id))->where('is_active', true)->orderBy('name')->get(),
            'orderStatuses' => OrderStatus::orderBy('sort_order')->get(),
            'filters' => [
                'status' => $status,
                'arrival_date' => $arriveDate,
                'pickup_date' => $pickupDate,
                'show_deleted' => $showDeleted,
            ],
        ]);
    }

    public function storeOrder(Request $request)
    {
        $data = $this->validateOrderPayload($request);

        $agency = Agency::where('id', $data['agency_id'])
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

        return redirect()->route('owner.ui.orders')->with('success', 'Commande créée avec plusieurs items.');
    }

    public function editOrder(Order $order)
    {
        $order->load(['items.service', 'agency', 'client']);
        abort_unless($order->agency && $order->agency->pressing_id === Auth::user()->pressing_id, 403);

        return view('owner.order-edit', [
            'order' => $order,
            'agencies' => Agency::where('pressing_id', Auth::user()->pressing_id)->where('is_active', true)->orderBy('name')->get(),
            'services' => Service::whereHas('agency', fn ($q) => $q->where('pressing_id', Auth::user()->pressing_id))->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function updateOrder(Request $request, Order $order)
    {
        $order->load('agency');
        abort_unless($order->agency && $order->agency->pressing_id === Auth::user()->pressing_id, 403);

        $data = $this->validateOrderPayload($request);
        $agency = Agency::where('id', $data['agency_id'])
            ->where('pressing_id', Auth::user()->pressing_id)
            ->where('is_active', true)
            ->firstOrFail();

        DB::transaction(function () use ($data, $agency, $order) {
            $order->items()->delete();
            [$updated, $total] = $this->persistOrderFromPayload($data, $agency, $order);

            if ($updated->invoice) {
                $updated->invoice->update(['amount' => $total]);
            } else {
                Invoice::create([
                    'order_id' => $updated->id,
                    'pressing_id' => Auth::user()->pressing_id,
                    'invoice_number' => 'FAC-'.strtoupper(uniqid()),
                    'amount' => $total,
                    'issued_at' => now()->toDateString(),
                ]);
            }
        });

        return redirect()->route('owner.ui.orders')->with('success', 'Commande modifiée.');
    }

    public function destroyOrder(Order $order)
    {
        $order->load('agency');
        abort_unless($order->agency && $order->agency->pressing_id === Auth::user()->pressing_id, 403);

        $order->delete();

        return redirect()->route('owner.ui.orders')->with('success', 'Commande supprimée (soft delete).');
    }


    public function addPayment(Request $request, Order $order)
    {
        $order->load('agency');
        abort_unless($order->agency && $order->agency->pressing_id === Auth::user()->pressing_id, 403);

        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
            'payment_method' => ['nullable', 'in:cash,wave,orange_money,card'],
        ]);

        $remaining = max(0, (float) $order->total - (float) $order->advance_amount);
        $amount = min($remaining, (float) $data['amount']);

        if ($amount <= 0) {
            return redirect()->route('owner.ui.orders')->with('error', 'Commande déjà totalement payée.');
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

        return redirect()->route('owner.ui.orders')->with('success', 'Paiement ajouté avec succès.');
    }

    public function applyDiscount(Request $request, Order $order)
    {
        $order->load('agency');
        abort_unless($order->agency && $order->agency->pressing_id === Auth::user()->pressing_id, 403);

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

        return redirect()->route('owner.ui.orders')->with('success', 'Réduction appliquée.');
    }

    public function transactions()
    {
        return view('owner.transactions', [
            'transactions' => Transaction::where('pressing_id', Auth::user()->pressing_id)
                ->with(['agency', 'user', 'order', 'expense'])
                ->latest('happened_at')
                ->latest()
                ->get(),
        ]);
    }

    public function invoices()
    {
        return view('owner.invoices', [
            'invoices' => Invoice::where('pressing_id', Auth::user()->pressing_id)
                ->with(['order.client', 'order.agency'])
                ->latest()
                ->get(),
        ]);
    }

    public function showInvoice(Invoice $invoice)
    {
        abort_unless($invoice->pressing_id === Auth::user()->pressing_id, 403);
        $invoice->load(['order.items.service', 'order.client', 'order.agency', 'pressing']);

        return view('owner.invoice-show', ['invoice' => $invoice]);
    }

    public function settings()
    {
        return view('owner.settings', [
            'pressing' => Pressing::findOrFail(Auth::user()->pressing_id),
        ]);
    }

    public function updateSettings(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:255'],
            'invoice_template' => ['required', 'in:classic,modern,minimal'],
            'invoice_primary_color' => ['required', 'string', 'max:20'],
            'invoice_welcome_message' => ['nullable', 'string', 'max:255'],
            'invoice_logo' => ['nullable', 'image', 'max:2048'],
            'opening_time' => ['nullable', 'date_format:H:i'],
            'closing_time' => ['nullable', 'date_format:H:i'],
        ]);

        $pressing = Pressing::findOrFail(Auth::user()->pressing_id);

        if ($request->hasFile('invoice_logo')) {
            $data['invoice_logo_path'] = $request->file('invoice_logo')->store('logos', 'public');
        }

        unset($data['invoice_logo']);

        $pressing->update($data);

        return redirect()->route('owner.ui.settings')->with('success', 'Informations du pressing mises à jour.');
    }

    public function pricing()
    {
        $user = Auth::user();

        $currentSubscription = OwnerSubscription::where('pressing_id', $user->pressing_id)
            ->with('plan')
            ->orderByDesc('is_active')
            ->latest('ends_at')
            ->first();

        return view('owner.pricing', [
            'plans' => SubscriptionPlan::orderBy('monthly_price')->get(),
            'currentSubscription' => $currentSubscription,
        ]);
    }

    public function subscribePlan(Request $request)
    {
        $data = $request->validate([
            'subscription_plan_id' => ['required', 'exists:subscription_plans,id'],
            'billing_cycle' => ['required', 'in:monthly,annual'],
        ]);

        $start = now()->startOfDay();
        $end = $data['billing_cycle'] === 'monthly' ? now()->addMonth()->endOfDay() : now()->addYear()->endOfDay();

        OwnerSubscription::where('pressing_id', Auth::user()->pressing_id)->where('is_active', true)->update(['is_active' => false]);

        OwnerSubscription::create([
            'pressing_id' => Auth::user()->pressing_id,
            'subscription_plan_id' => $data['subscription_plan_id'],
            'billing_cycle' => $data['billing_cycle'],
            'starts_at' => $start->toDateString(),
            'ends_at' => $end->toDateString(),
            'is_active' => true,
        ]);

        return redirect()->route('owner.ui.pricing')->with('success', 'Souscription effectuée avec succès.');
    }

    public function stats(Request $request)
    {
        $user = Auth::user();
        $from = $request->query('from');
        $to = $request->query('to');
        $selectedAgencyId = $request->query('agency_id');

        $query = Order::whereHas('agency', fn ($q) => $q->where('pressing_id', $user->pressing_id));

        if ($selectedAgencyId) {
            $query->where('agency_id', $selectedAgencyId);
        }
        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }

        $startWeek = now()->startOfWeek(Carbon::MONDAY);
        $weekLabels = [];
        $weekRevenue = [];
        for ($i = 0; $i < 7; $i++) {
            $day = $startWeek->copy()->addDays($i);
            $weekLabels[] = $day->translatedFormat('D');
            $dayQuery = Order::whereHas('agency', fn ($q) => $q->where('pressing_id', $user->pressing_id))
                ->whereDate('created_at', $day->toDateString());
            if ($selectedAgencyId) {
                $dayQuery->where('agency_id', $selectedAgencyId);
            }
            $weekRevenue[] = (float) $dayQuery->sum('total');
        }

        $monthLabels = [];
        $monthRevenue = [];
        $monthExpenses = [];
        for ($i = 3; $i >= 0; $i--) {
            $month = now()->startOfMonth()->subMonths($i);
            $monthLabels[] = $month->translatedFormat('M Y');

            $monthOrders = Order::whereHas('agency', fn ($q) => $q->where('pressing_id', $user->pressing_id))
                ->whereBetween('created_at', [$month->copy()->startOfMonth(), $month->copy()->endOfMonth()]);
            if ($selectedAgencyId) {
                $monthOrders->where('agency_id', $selectedAgencyId);
            }
            $monthRevenue[] = (float) $monthOrders->sum('total');

            $monthExpenseQuery = Expense::where('pressing_id', $user->pressing_id)
                ->whereBetween('expense_date', [$month->copy()->startOfMonth()->toDateString(), $month->copy()->endOfMonth()->toDateString()]);
            if ($selectedAgencyId) {
                $monthExpenseQuery->where('agency_id', $selectedAgencyId);
            }
            $monthExpenses[] = (float) $monthExpenseQuery->sum('amount');
        }

        $statusDistribution = [
            'pending' => (clone $query)->where('status', 'pending')->count(),
            'ready' => (clone $query)->where('status', 'ready')->count(),
            'picked_up' => (clone $query)->whereNotNull('picked_up_at')->count(),
        ];

        $expenseQuery = Expense::where('pressing_id', $user->pressing_id);
        if ($selectedAgencyId) {
            $expenseQuery->where('agency_id', $selectedAgencyId);
        }
        if ($from) {
            $expenseQuery->whereDate('expense_date', '>=', $from);
        }
        if ($to) {
            $expenseQuery->whereDate('expense_date', '<=', $to);
        }

        return view('owner.stats', [
            'totalOrders' => (clone $query)->count(),
            'totalRevenue' => (clone $query)->sum('total'),
            'advancePaidCount' => (clone $query)->where('paid_advance', true)->count(),
            'pickedUpCount' => (clone $query)->whereNotNull('picked_up_at')->count(),
            'totalExpenses' => (clone $expenseQuery)->sum('amount'),
            'agencies' => Agency::where('pressing_id', $user->pressing_id)->orderBy('name')->get(),
            'selectedAgencyId' => $selectedAgencyId,
            'from' => $from,
            'to' => $to,
            'weekLabels' => $weekLabels,
            'weekRevenue' => $weekRevenue,
            'monthLabels' => $monthLabels,
            'monthRevenue' => $monthRevenue,
            'monthExpenses' => $monthExpenses,
            'statusDistribution' => $statusDistribution,
        ]);
    }

    public function expenses()
    {
        $user = Auth::user();

        return view('owner.expenses', [
            'expenses' => Expense::where('pressing_id', $user->pressing_id)->with(['agency', 'categoryExpense'])->latest('expense_date')->get(),
            'agencies' => Agency::where('pressing_id', $user->pressing_id)->where('is_active', true)->orderBy('name')->get(),
            'categories' => CategoryExpense::orderBy('name')->get(),
        ]);
    }

    public function storeExpense(Request $request)
    {
        $data = $this->validateExpensePayload($request);
        $agencyId = $data['agency_id'] ?? null;

        if ($agencyId) {
            Agency::where('id', $agencyId)->where('pressing_id', Auth::user()->pressing_id)->firstOrFail();
        }

        $expense = Expense::create([
            ...$data,
            'pressing_id' => Auth::user()->pressing_id,
            'agency_id' => $agencyId,
            'category' => CategoryExpense::find($data['category_expense_id'])?->name,
        ]);

        Transaction::create([
            'pressing_id' => Auth::user()->pressing_id,
            'agency_id' => $agencyId,
            'user_id' => Auth::id(),
            'expense_id' => $expense->id,
            'type' => 'paiement',
            'amount' => $expense->amount,
            'label' => 'Dépense: '.$expense->title,
            'happened_at' => now(),
        ]);

        return redirect()->route('owner.ui.expenses')->with('success', 'Dépense ajoutée.');
    }

    public function updateExpense(Request $request, Expense $expense)
    {
        abort_unless($expense->pressing_id === Auth::user()->pressing_id, 403);

        $data = $this->validateExpensePayload($request);
        $agencyId = $data['agency_id'] ?? null;

        if ($agencyId) {
            Agency::where('id', $agencyId)->where('pressing_id', Auth::user()->pressing_id)->firstOrFail();
        }

        $expense->update([
            ...$data,
            'agency_id' => $agencyId,
            'category' => CategoryExpense::find($data['category_expense_id'])?->name,
        ]);

        return redirect()->route('owner.ui.expenses')->with('success', 'Dépense modifiée.');
    }

    public function destroyExpense(Expense $expense)
    {
        abort_unless($expense->pressing_id === Auth::user()->pressing_id, 403);
        $expense->delete();

        return redirect()->route('owner.ui.expenses')->with('success', 'Dépense supprimée (soft delete).');
    }


    public function requests()
    {
        $user = Auth::user();

        return view('owner.requests', [
            'requests' => EmployeeRequest::where('pressing_id', $user->pressing_id)
                ->with(['employee', 'agency'])
                ->latest()
                ->get(),
        ]);
    }

    public function markRequestRead(EmployeeRequest $employeeRequest)
    {
        abort_unless($employeeRequest->pressing_id === Auth::user()->pressing_id, 403);

        $employeeRequest->update([
            'status' => 'read',
            'read_at' => now(),
        ]);

        UserNotification::create([
            'user_id' => $employeeRequest->employee_id,
            'type' => 'request_read',
            'title' => 'Demande traitée',
            'message' => 'Le propriétaire a marqué votre demande "'.$employeeRequest->subject.'" comme lue.',
            'data' => ['request_id' => $employeeRequest->id],
        ]);

        return redirect()->route('owner.ui.requests')->with('success', 'Demande marquée comme lue.');
    }

    private function validateOrderPayload(Request $request): array
    {
        return $request->validate([
            'agency_id' => ['required', 'exists:agencies,id'],
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

    private function validateExpensePayload(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category_expense_id' => ['nullable', 'exists:category_expenses,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'expense_date' => ['required', 'date'],
            'agency_id' => ['nullable', 'exists:agencies,id'],
            'notes' => ['nullable', 'string'],
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
            'total' => $total,
        ]);
        $order->save();

        foreach ($preparedItems as $item) {
            OrderItem::create($item + ['order_id' => $order->id]);
        }

        return [$order, $total];
    }
}
