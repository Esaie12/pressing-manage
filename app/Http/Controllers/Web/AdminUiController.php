<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Agency;
use App\Models\Expense;
use App\Models\Order;
use App\Models\OwnerSubscription;
use App\Models\Pressing;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminUiController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard', [
            'ownersCount' => User::where('role', User::ROLE_OWNER)->count(),
            'agenciesCount' => Agency::count(),
            'activeSubscriptions' => OwnerSubscription::where('is_active', true)->count(),
            'plansCount' => SubscriptionPlan::count(),
        ]);
    }

    public function owners()
    {
        return view('admin.owners', [
            'owners' => User::where('role', User::ROLE_OWNER)->with('pressing')->latest()->get(),
        ]);
    }

    public function ownerStats(User $owner)
    {
        abort_unless($owner->role === User::ROLE_OWNER && $owner->pressing_id, 404);

        $pressingId = $owner->pressing_id;
        $orders = Order::whereHas('agency', fn ($q) => $q->where('pressing_id', $pressingId));

        return view('admin.owner-stats', [
            'owner' => $owner->load('pressing'),
            'agencies' => Agency::where('pressing_id', $pressingId)->orderBy('name')->get(),
            'employeesCount' => User::where('pressing_id', $pressingId)->where('role', User::ROLE_EMPLOYEE)->count(),
            'revenue' => (clone $orders)->sum('total'),
            'expenses' => Expense::where('pressing_id', $pressingId)->sum('amount'),
            'activeSubscription' => OwnerSubscription::where('pressing_id', $pressingId)
                ->where('is_active', true)
                ->with('plan')
                ->latest('ends_at')
                ->first(),
        ]);
    }

    public function storeOwner(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'pressing_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

        $owner = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => User::ROLE_OWNER,
        ]);

        $pressing = Pressing::create([
            'name' => $data['pressing_name'],
            'owner_id' => $owner->id,
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
        ]);

        $owner->update(['pressing_id' => $pressing->id]);

        return redirect()->route('admin.ui.owners')->with('success', 'Propriétaire créé avec succès.');
    }

    public function agencies()
    {
        return view('admin.agencies', [
            'agencies' => Agency::with('pressing')->latest()->get(),
        ]);
    }

    public function subscriptions()
    {
        $subscriptions = OwnerSubscription::with(['pressing', 'plan'])->latest()->get();

        return view('admin.subscriptions', [
            'subscriptions' => $subscriptions,
            'plans' => SubscriptionPlan::orderBy('monthly_price')->get(),
            'pressings' => Pressing::orderBy('name')->get(),
            'total' => $subscriptions->count(),
            'active' => $subscriptions->where('is_active', true)->count(),
            'inactive' => $subscriptions->where('is_active', false)->count(),
        ]);
    }

    public function storeSubscription(Request $request)
    {
        $data = $request->validate([
            'pressing_id' => ['required', 'exists:pressings,id'],
            'subscription_plan_id' => ['required', 'exists:subscription_plans,id'],
            'billing_cycle' => ['required', 'in:monthly,annual'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after_or_equal:starts_at'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        OwnerSubscription::create([
            ...$data,
            'is_active' => (bool) ($data['is_active'] ?? false),
        ]);

        return redirect()->route('admin.ui.subscriptions')->with('success', 'Abonnement ajouté.');
    }

    public function pricing()
    {
        return view('admin.pricing', [
            'plans' => SubscriptionPlan::orderBy('monthly_price')->get(),
        ]);
    }

    public function storePlan(Request $request)
    {
        $data = $this->validatePlan($request);
        SubscriptionPlan::create($data);

        return redirect()->route('admin.ui.pricing')->with('success', 'Pack créé.');
    }

    public function updatePlan(Request $request, SubscriptionPlan $plan)
    {
        $data = $this->validatePlan($request);
        $plan->update($data);

        return redirect()->route('admin.ui.pricing')->with('success', 'Pack mis à jour.');
    }

    private function validatePlan(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'monthly_price' => ['required', 'numeric', 'min:0'],
            'annual_price' => ['required', 'numeric', 'min:0'],
            'max_agencies' => ['required', 'integer', 'min:1'],
            'max_employees' => ['required', 'integer', 'min:1'],
            'allow_customization' => ['nullable', 'boolean'],
            'allow_cash_closure_module' => ['nullable', 'boolean'],
            'allow_accounting_module' => ['nullable', 'boolean'],
            'allow_stock_module' => ['nullable', 'boolean'],
        ]);

        $data['allow_customization'] = (bool) ($data['allow_customization'] ?? false);
        $data['allow_cash_closure_module'] = (bool) ($data['allow_cash_closure_module'] ?? false);
        $data['allow_accounting_module'] = (bool) ($data['allow_accounting_module'] ?? false);
        $data['allow_stock_module'] = (bool) ($data['allow_stock_module'] ?? false);

        return $data;
    }
}
