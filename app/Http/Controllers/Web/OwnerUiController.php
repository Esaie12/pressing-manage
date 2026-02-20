<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Agency;
use App\Models\AccountingReport;
use App\Models\AccountingReportEntry;
use App\Models\AccountingSetup;
use App\Models\CategoryExpense;
use App\Models\CashClosure;
use App\Models\CashClosureEntry;
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
            'pressing' => $pressing,
        ]);
    }

    public function toggleCashClosureModule()
    {
        $pressing = Pressing::findOrFail(Auth::user()->pressing_id);
        $pressing->update(['module_cash_closure_enabled' => ! $pressing->module_cash_closure_enabled]);

        return redirect()->route('owner.ui.dashboard')->with('success', $pressing->module_cash_closure_enabled
            ? 'Module Clôture de caisse activé.'
            : 'Module Clôture de caisse désactivé.');
    }

    public function toggleAccountingModule()
    {
        $pressing = Pressing::findOrFail(Auth::user()->pressing_id);
        $pressing->update(['module_accounting_enabled' => ! $pressing->module_accounting_enabled]);

        return redirect()->route('owner.ui.dashboard')->with('success', $pressing->module_accounting_enabled
            ? 'Module Comptabilité activé.'
            : 'Module Comptabilité désactivé.');
    }

    public function accountingSettings(Request $request)
    {
        $pressing = Pressing::findOrFail(Auth::user()->pressing_id);
        abort_if(! $pressing->module_accounting_enabled, 403, 'Module Comptabilité non activé.');

        $agencyId = $request->query('agency_id');
        $setup = AccountingSetup::where('pressing_id', $pressing->id)
            ->where('agency_id', $agencyId)
            ->first();

        return view('owner.accounting-settings', [
            'pressing' => $pressing,
            'agencies' => Agency::where('pressing_id', $pressing->id)->orderBy('name')->get(),
            'selectedAgencyId' => $agencyId,
            'setup' => $setup,
        ]);

        $agencyId = $data['agency_id'] ?? null;
        if ($agencyId) {
            Agency::where('id', $agencyId)->where('pressing_id', $pressing->id)->firstOrFail();
        }

        $preview = $this->buildAccountingPreview($pressing->id, $data['month'], $agencyId);

        DB::transaction(function () use ($pressing, $data, $agencyId, $preview) {
            $report = AccountingReport::create([
                'pressing_id' => $pressing->id,
                'agency_id' => $agencyId,
                'accounting_setup_id' => $preview['setup']?->id,
                'created_by_user_id' => Auth::id(),
                'month' => Carbon::parse($data['month'])->startOfMonth()->toDateString(),
                'total_credits' => $preview['total_credits'],
                'total_debits' => $preview['total_debits'],
                'net_result' => $preview['net_result'],
                'snapshot' => $preview['snapshot'],
                'note' => $data['note'] ?? null,
                'saved_at' => now(),
            ]);

            foreach ($preview['entries'] as $entry) {
                AccountingReportEntry::create([
                    'accounting_report_id' => $report->id,
                    'transaction_id' => $entry['transaction_id'],
                    'agency_id' => $entry['agency_id'],
                    'user_id' => $entry['user_id'],
                    'entry_type' => $entry['entry_type'],
                    'amount' => $entry['amount'],
                    'payment_method' => $entry['payment_method'],
                    'label' => $entry['label'],
                    'order_reference' => $entry['order_reference'],
                    'happened_at' => $entry['happened_at'],
                ]);
            }
        });

        return redirect()->route('owner.ui.accounting.reports', ['month' => $data['month'], 'agency_id' => $agencyId])->with('success', 'Bilan mensuel sauvegardé.');
    }

    public function showAccountingReport(AccountingReport $report)
    {
        abort_unless($report->pressing_id === Auth::user()->pressing_id, 403);
        $report->load(['agency', 'entries']);

        return view('owner.accounting-report-show', ['report' => $report]);
    }

    private function buildAccountingPreview(int $pressingId, string $month, ?string $agencyId): array
    {
        $monthStart = Carbon::parse($month)->startOfMonth();
        $monthEnd = $monthStart->copy()->endOfMonth();

        $transactions = Transaction::where('pressing_id', $pressingId)
            ->where('is_cancelled', false)
            ->whereBetween('happened_at', [$monthStart->startOfDay(), $monthEnd->endOfDay()])
            ->with('order');

        if ($agencyId) {
            $transactions->where('agency_id', $agencyId);
        }

        $txList = $transactions->get();
        $credits = (float) $txList->where('type', 'encaissement')->sum('amount');
        $debitsTx = (float) $txList->where('type', 'paiement')->sum('amount');

        $expenseQuery = Expense::where('pressing_id', $pressingId)->whereBetween('expense_date', [$monthStart->toDateString(), $monthEnd->toDateString()]);
        if ($agencyId) {
            $expenseQuery->where('agency_id', $agencyId);
        }
        $expenses = (float) $expenseQuery->sum('amount');

        $setup = AccountingSetup::where('pressing_id', $pressingId)->where('agency_id', $agencyId)->first();
        $setupDebits = (float) ($setup?->financial_debts ?? 0) + (float) ($setup?->operating_debts ?? 0) + (float) ($setup?->fixed_asset_debts ?? 0) + (float) ($setup?->other_debts ?? 0);

        $totalDebits = $debitsTx + $expenses + $setupDebits;
        $netResult = $credits - $totalDebits;

        $entries = $txList->map(function ($tx) {
            return [
                'transaction_id' => $tx->id,
                'agency_id' => $tx->agency_id,
                'user_id' => $tx->user_id,
                'entry_type' => $tx->type,
                'amount' => (float) $tx->amount,
                'payment_method' => $tx->payment_method,
                'label' => $tx->label,
                'order_reference' => $tx->order?->reference,
                'happened_at' => $tx->happened_at,
            ];
        })->values()->all();

        return [
            'total_credits' => $credits,
            'total_debits' => $totalDebits,
            'net_result' => $netResult,
            'entries' => $entries,
            'setup' => $setup,
            'snapshot' => [
                'period' => $monthStart->format('Y-m'),
                'credits' => $credits,
                'debits_transactions' => $debitsTx,
                'debits_expenses' => $expenses,
                'debits_setup' => $setupDebits,
                'net_result' => $netResult,
                'assets' => [
                    'capital' => (float) ($setup?->capital ?? 0),
                    'reserves' => (float) ($setup?->reserves ?? 0),
                    'retained_earnings' => (float) ($setup?->retained_earnings ?? 0),
                    'intangible_assets' => (float) ($setup?->intangible_assets ?? 0),
                    'tangible_assets' => (float) ($setup?->tangible_assets ?? 0),
                    'financial_assets' => (float) ($setup?->financial_assets ?? 0),
                    'stocks' => (float) ($setup?->stocks ?? 0),
                    'receivables' => (float) ($setup?->receivables ?? 0),
                    'treasury' => (float) ($setup?->treasury ?? 0),
                ],
                'liabilities' => [
                    'financial_debts' => (float) ($setup?->financial_debts ?? 0),
                    'operating_debts' => (float) ($setup?->operating_debts ?? 0),
                    'fixed_asset_debts' => (float) ($setup?->fixed_asset_debts ?? 0),
                    'other_debts' => (float) ($setup?->other_debts ?? 0),
                ],
            ],
        ];
    }

    public function cashClosures(Request $request)
    {
        $pressing = Pressing::findOrFail(Auth::user()->pressing_id);
        abort_if(! $pressing->module_cash_closure_enabled, 403, 'Module Clôture de caisse non activé.');

        $closureDate = $request->query('closure_date', now()->toDateString());

        return view('owner.cash-closures', [
            'closureDate' => $closureDate,
            'agencies' => Agency::where('pressing_id', Auth::user()->pressing_id)->orderBy('name')->get(),
            'employees' => User::where('pressing_id', Auth::user()->pressing_id)->where('role', User::ROLE_EMPLOYEE)->orderBy('name')->get(),
            'closures' => CashClosure::where('pressing_id', Auth::user()->pressing_id)
                ->with(['agency', 'employee', 'closedBy'])
                ->latest('closed_at')
                ->latest()
                ->get(),
        ]);
    }

    public function storeCashClosure(Request $request)
    {
        $pressing = Pressing::findOrFail(Auth::user()->pressing_id);
        abort_if(! $pressing->module_cash_closure_enabled, 403, 'Module Clôture de caisse non activé.');

        $data = $request->validate([
            'closure_date' => ['required', 'date'],
            'agency_id' => ['nullable', 'exists:agencies,id'],
            'employee_id' => ['nullable', 'exists:users,id'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $transactions = Transaction::where('pressing_id', Auth::user()->pressing_id)
            ->where('is_cancelled', false)
            ->whereDate('happened_at', $data['closure_date'])
            ->with('order');

        $agencyId = $data['agency_id'] ?? null;
        if ($agencyId) {
            Agency::where('id', $agencyId)->where('pressing_id', Auth::user()->pressing_id)->firstOrFail();
            $transactions->where('agency_id', $agencyId);
        }

        $employeeId = $data['employee_id'] ?? null;
        if ($employeeId) {
            User::where('id', $employeeId)
                ->where('pressing_id', Auth::user()->pressing_id)
                ->where('role', User::ROLE_EMPLOYEE)
                ->firstOrFail();
            $transactions->where('user_id', $employeeId);
        }

        $transactionsList = $transactions->get();
        $encaissement = (float) $transactionsList->where('type', 'encaissement')->sum('amount');
        $paiement = (float) $transactionsList->where('type', 'paiement')->sum('amount');
        $count = $transactionsList->count();

        DB::transaction(function () use ($agencyId, $employeeId, $data, $transactionsList, $encaissement, $paiement, $count) {
            $closure = CashClosure::create([
                'pressing_id' => Auth::user()->pressing_id,
                'agency_id' => $agencyId,
                'employee_id' => $employeeId,
                'closed_by_user_id' => Auth::id(),
                'closure_date' => $data['closure_date'],
                'encaissement_total' => $encaissement,
                'paiement_total' => $paiement,
                'net_total' => (float) $encaissement - (float) $paiement,
                'transactions_count' => $count,
                'closed_at' => now(),
                'note' => $data['note'] ?? null,
            ]);

            foreach ($transactionsList as $tx) {
                CashClosureEntry::create([
                    'cash_closure_id' => $closure->id,
                    'transaction_id' => $tx->id,
                    'user_id' => $tx->user_id,
                    'transaction_type' => $tx->type,
                    'amount' => $tx->amount,
                    'payment_method' => $tx->payment_method,
                    'label' => $tx->label,
                    'order_reference' => $tx->order?->reference,
                    'happened_at' => $tx->happened_at,
                ]);
            }
        });

        return redirect()->route('owner.ui.cash-closures')->with('success', 'Clôture de caisse enregistrée.');
    }

    public function showCashClosure(CashClosure $cashClosure)
    {
        abort_unless($cashClosure->pressing_id === Auth::user()->pressing_id, 403);
        $cashClosure->load(['agency', 'employee', 'closedBy', 'entries.user']);

        return view('owner.cash-closure-show', ['closure' => $cashClosure]);
    }

    public function saveAccountingSettings(Request $request)
    {
        $pressing = Pressing::findOrFail(Auth::user()->pressing_id);
        abort_if(! $pressing->module_accounting_enabled, 403, 'Module Comptabilité non activé.');

        $data = $request->validate([
            'agency_id' => ['nullable', 'exists:agencies,id'],
            'capital' => ['nullable', 'numeric', 'min:0'],
            'reserves' => ['nullable', 'numeric', 'min:0'],
            'retained_earnings' => ['nullable', 'numeric'],
            'intangible_assets' => ['nullable', 'numeric', 'min:0'],
            'tangible_assets' => ['nullable', 'numeric', 'min:0'],
            'financial_assets' => ['nullable', 'numeric', 'min:0'],
            'stocks' => ['nullable', 'numeric', 'min:0'],
            'receivables' => ['nullable', 'numeric', 'min:0'],
            'treasury' => ['nullable', 'numeric', 'min:0'],
            'financial_debts' => ['nullable', 'numeric', 'min:0'],
            'operating_debts' => ['nullable', 'numeric', 'min:0'],
            'fixed_asset_debts' => ['nullable', 'numeric', 'min:0'],
            'other_debts' => ['nullable', 'numeric', 'min:0'],
            'employee_salaries' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $agencyId = $data['agency_id'] ?? null;
        if ($agencyId) {
            Agency::where('id', $agencyId)->where('pressing_id', $pressing->id)->firstOrFail();
        }

        AccountingSetup::updateOrCreate(
            ['pressing_id' => $pressing->id, 'agency_id' => $agencyId],
            [
                'capital' => (float) ($data['capital'] ?? 0),
                'reserves' => (float) ($data['reserves'] ?? 0),
                'retained_earnings' => (float) ($data['retained_earnings'] ?? 0),
                'intangible_assets' => (float) ($data['intangible_assets'] ?? 0),
                'tangible_assets' => (float) ($data['tangible_assets'] ?? 0),
                'financial_assets' => (float) ($data['financial_assets'] ?? 0),
                'stocks' => (float) ($data['stocks'] ?? 0),
                'receivables' => (float) ($data['receivables'] ?? 0),
                'treasury' => (float) ($data['treasury'] ?? 0),
                'financial_debts' => (float) ($data['financial_debts'] ?? 0),
                'operating_debts' => (float) ($data['operating_debts'] ?? 0),
                'fixed_asset_debts' => (float) ($data['fixed_asset_debts'] ?? 0),
                'other_debts' => (float) ($data['other_debts'] ?? 0),
                'employee_salaries' => (float) ($data['employee_salaries'] ?? 0),
                'notes' => $data['notes'] ?? null,
            ]
        );

        return redirect()->route('owner.ui.accounting.settings', ['agency_id' => $agencyId])->with('success', 'Paramètres de comptabilité enregistrés.');
    }

    public function accountingReports(Request $request)
    {
        $pressing = Pressing::findOrFail(Auth::user()->pressing_id);
        abort_if(! $pressing->module_accounting_enabled, 403, 'Module Comptabilité non activé.');

        $month = $request->query('month', now()->startOfMonth()->toDateString());
        $agencyId = $request->query('agency_id');

        $preview = $this->buildAccountingPreview($pressing->id, $month, $agencyId);

        return view('owner.accounting-reports', [
            'month' => $month,
            'agencyId' => $agencyId,
            'agencies' => Agency::where('pressing_id', $pressing->id)->orderBy('name')->get(),
            'preview' => $preview,
            'savedReports' => AccountingReport::where('pressing_id', $pressing->id)
                ->with('agency')
                ->latest('saved_at')
                ->latest()
                ->get(),
        ]);
    }

    public function saveAccountingReport(Request $request)
    {
        $pressing = Pressing::findOrFail(Auth::user()->pressing_id);
        abort_if(! $pressing->module_accounting_enabled, 403, 'Module Comptabilité non activé.');

        $data = $request->validate([
            'month' => ['required', 'date'],
            'agency_id' => ['nullable', 'exists:agencies,id'],
            'note' => ['nullable', 'string', 'max:2000'],
        ]);

        $agencyId = $data['agency_id'] ?? null;
        if ($agencyId) {
            Agency::where('id', $agencyId)->where('pressing_id', $pressing->id)->firstOrFail();
        }

        $preview = $this->buildAccountingPreview($pressing->id, $data['month'], $agencyId);

        DB::transaction(function () use ($pressing, $data, $agencyId, $preview) {
            $report = AccountingReport::create([
                'pressing_id' => $pressing->id,
                'agency_id' => $agencyId,
                'accounting_setup_id' => $preview['setup']?->id,
                'created_by_user_id' => Auth::id(),
                'month' => Carbon::parse($data['month'])->startOfMonth()->toDateString(),
                'total_credits' => $preview['total_credits'],
                'total_debits' => $preview['total_debits'],
                'net_result' => $preview['net_result'],
                'snapshot' => $preview['snapshot'],
                'note' => $data['note'] ?? null,
                'saved_at' => now(),
            ]);

            foreach ($preview['entries'] as $entry) {
                AccountingReportEntry::create([
                    'accounting_report_id' => $report->id,
                    'transaction_id' => $entry['transaction_id'],
                    'agency_id' => $entry['agency_id'],
                    'user_id' => $entry['user_id'],
                    'entry_type' => $entry['entry_type'],
                    'amount' => $entry['amount'],
                    'payment_method' => $entry['payment_method'],
                    'label' => $entry['label'],
                    'order_reference' => $entry['order_reference'],
                    'happened_at' => $entry['happened_at'],
                ]);
            }
        });

        return redirect()->route('owner.ui.accounting.reports', ['month' => $data['month'], 'agency_id' => $agencyId])->with('success', 'Bilan mensuel sauvegardé.');
    }

    public function showAccountingReport(AccountingReport $report)
    {
        abort_unless($report->pressing_id === Auth::user()->pressing_id, 403);
        $report->load(['agency', 'entries']);

        return view('owner.accounting-report-show', ['report' => $report]);
    }

    private function buildAccountingPreview(int $pressingId, string $month, ?string $agencyId): array
    {
        $monthStart = Carbon::parse($month)->startOfMonth();
        $monthEnd = $monthStart->copy()->endOfMonth();

        $transactions = Transaction::where('pressing_id', $pressingId)
            ->where('is_cancelled', false)
            ->whereBetween('happened_at', [$monthStart->startOfDay(), $monthEnd->endOfDay()])
            ->with('order');

        if ($agencyId) {
            $transactions->where('agency_id', $agencyId);
        }

        $txList = $transactions->get();
        $credits = (float) $txList->where('type', 'encaissement')->sum('amount');
        $debitsTx = (float) $txList->where('type', 'paiement')->sum('amount');

        $expenseQuery = Expense::where('pressing_id', $pressingId)->whereBetween('expense_date', [$monthStart->toDateString(), $monthEnd->toDateString()]);
        if ($agencyId) {
            $expenseQuery->where('agency_id', $agencyId);
        }
        $expenses = (float) $expenseQuery->sum('amount');

        $setup = AccountingSetup::where('pressing_id', $pressingId)->where('agency_id', $agencyId)->first();
        $setupDebits = (float) ($setup?->financial_debts ?? 0) + (float) ($setup?->operating_debts ?? 0) + (float) ($setup?->fixed_asset_debts ?? 0) + (float) ($setup?->other_debts ?? 0) + (float) ($setup?->employee_salaries ?? 0);

        $totalDebits = $debitsTx + $expenses + $setupDebits;
        $netResult = $credits - $totalDebits;

        $entries = $txList->map(function ($tx) {
            return [
                'transaction_id' => $tx->id,
                'agency_id' => $tx->agency_id,
                'user_id' => $tx->user_id,
                'entry_type' => $tx->type,
                'amount' => (float) $tx->amount,
                'payment_method' => $tx->payment_method,
                'label' => $tx->label,
                'order_reference' => $tx->order?->reference,
                'happened_at' => $tx->happened_at,
            ];
        })->values()->all();

        return [
            'total_credits' => $credits,
            'total_debits' => $totalDebits,
            'net_result' => $netResult,
            'entries' => $entries,
            'setup' => $setup,
            'snapshot' => [
                'period' => $monthStart->format('Y-m'),
                'credits' => $credits,
                'debits_transactions' => $debitsTx,
                'debits_expenses' => $expenses,
                'debits_setup' => $setupDebits,
                'net_result' => $netResult,
                'assets' => [
                    'capital' => (float) ($setup?->capital ?? 0),
                    'reserves' => (float) ($setup?->reserves ?? 0),
                    'retained_earnings' => (float) ($setup?->retained_earnings ?? 0),
                    'intangible_assets' => (float) ($setup?->intangible_assets ?? 0),
                    'tangible_assets' => (float) ($setup?->tangible_assets ?? 0),
                    'financial_assets' => (float) ($setup?->financial_assets ?? 0),
                    'stocks' => (float) ($setup?->stocks ?? 0),
                    'receivables' => (float) ($setup?->receivables ?? 0),
                    'treasury' => (float) ($setup?->treasury ?? 0),
                ],
                'liabilities' => [
                    'financial_debts' => (float) ($setup?->financial_debts ?? 0),
                    'operating_debts' => (float) ($setup?->operating_debts ?? 0),
                    'fixed_asset_debts' => (float) ($setup?->fixed_asset_debts ?? 0),
                    'other_debts' => (float) ($setup?->other_debts ?? 0),
                    'employee_salaries' => (float) ($setup?->employee_salaries ?? 0),
                ],
            ],
        ];
    }

    public function cashClosures(Request $request)
    {
        $pressing = Pressing::findOrFail(Auth::user()->pressing_id);
        abort_if(! $pressing->module_cash_closure_enabled, 403, 'Module Clôture de caisse non activé.');

        $closureDate = $request->query('closure_date', now()->toDateString());

        return view('owner.cash-closures', [
            'closureDate' => $closureDate,
            'agencies' => Agency::where('pressing_id', Auth::user()->pressing_id)->orderBy('name')->get(),
            'employees' => User::where('pressing_id', Auth::user()->pressing_id)->where('role', User::ROLE_EMPLOYEE)->orderBy('name')->get(),
            'closures' => CashClosure::where('pressing_id', Auth::user()->pressing_id)
                ->with(['agency', 'employee', 'closedBy'])
                ->latest('closed_at')
                ->latest()
                ->get(),
        ]);
    }

    public function storeCashClosure(Request $request)
    {
        $pressing = Pressing::findOrFail(Auth::user()->pressing_id);
        abort_if(! $pressing->module_cash_closure_enabled, 403, 'Module Clôture de caisse non activé.');

        $data = $request->validate([
            'closure_date' => ['required', 'date'],
            'agency_id' => ['nullable', 'exists:agencies,id'],
            'employee_id' => ['nullable', 'exists:users,id'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $transactions = Transaction::where('pressing_id', Auth::user()->pressing_id)
            ->where('is_cancelled', false)
            ->whereDate('happened_at', $data['closure_date'])
            ->with('order');

        $agencyId = $data['agency_id'] ?? null;
        if ($agencyId) {
            Agency::where('id', $agencyId)->where('pressing_id', Auth::user()->pressing_id)->firstOrFail();
            $transactions->where('agency_id', $agencyId);
        }

        $employeeId = $data['employee_id'] ?? null;
        if ($employeeId) {
            User::where('id', $employeeId)
                ->where('pressing_id', Auth::user()->pressing_id)
                ->where('role', User::ROLE_EMPLOYEE)
                ->firstOrFail();
            $transactions->where('user_id', $employeeId);
        }

        $transactionsList = $transactions->get();
        $encaissement = (float) $transactionsList->where('type', 'encaissement')->sum('amount');
        $paiement = (float) $transactionsList->where('type', 'paiement')->sum('amount');
        $count = $transactionsList->count();

        DB::transaction(function () use ($agencyId, $employeeId, $data, $transactionsList, $encaissement, $paiement, $count) {
            $closure = CashClosure::create([
                'pressing_id' => Auth::user()->pressing_id,
                'agency_id' => $agencyId,
                'employee_id' => $employeeId,
                'closed_by_user_id' => Auth::id(),
                'closure_date' => $data['closure_date'],
                'encaissement_total' => $encaissement,
                'paiement_total' => $paiement,
                'net_total' => (float) $encaissement - (float) $paiement,
                'transactions_count' => $count,
                'closed_at' => now(),
                'note' => $data['note'] ?? null,
            ]);

            foreach ($transactionsList as $tx) {
                CashClosureEntry::create([
                    'cash_closure_id' => $closure->id,
                    'transaction_id' => $tx->id,
                    'user_id' => $tx->user_id,
                    'transaction_type' => $tx->type,
                    'amount' => $tx->amount,
                    'payment_method' => $tx->payment_method,
                    'label' => $tx->label,
                    'order_reference' => $tx->order?->reference,
                    'happened_at' => $tx->happened_at,
                ]);
            }
        });

        return redirect()->route('owner.ui.cash-closures')->with('success', 'Clôture de caisse enregistrée.');
    }

    public function showCashClosure(CashClosure $cashClosure)
    {
        abort_unless($cashClosure->pressing_id === Auth::user()->pressing_id, 403);
        $cashClosure->load(['agency', 'employee', 'closedBy', 'entries.user']);

        return view('owner.cash-closure-show', ['closure' => $cashClosure]);
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
        abort_if($order->status !== 'pending', 422, 'Seules les commandes en attente peuvent être modifiées.');

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
        abort_if($order->status !== 'pending', 422, 'Seules les commandes en attente peuvent être modifiées.');

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
        abort_if($order->status !== 'pending', 422, 'Seules les commandes en attente peuvent être supprimées.');

        $order->delete();

        return redirect()->route('owner.ui.orders')->with('success', 'Commande supprimée (soft delete).');
    }


    public function markReady(Order $order)
    {
        $order->load('agency');
        abort_unless($order->agency && $order->agency->pressing_id === Auth::user()->pressing_id, 403);
        abort_if($order->status !== 'pending', 422, 'Seules les commandes en attente peuvent être marquées prêtes.');

        $order->update(['status' => 'ready', 'ready_at' => now()]);

        return redirect()->route('owner.ui.orders')->with('success', 'Commande marquée prête.');
    }

    public function markPickedUp(Order $order)
    {
        $order->load('agency');
        abort_unless($order->agency && $order->agency->pressing_id === Auth::user()->pressing_id, 403);
        abort_if($order->status !== 'pending', 422, 'Seules les commandes en attente peuvent être marquées retirées.');

        if ((float) $order->advance_amount < (float) $order->total) {
            return redirect()->route('owner.ui.orders')->with('error', 'Commande non totalement payée.');
        }

        $order->update(['status' => 'picked_up', 'picked_up_at' => now()]);

        return redirect()->route('owner.ui.orders')->with('success', 'Commande marquée retirée.');
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

    public function transactions()
    {
        $pressing = Pressing::findOrFail(Auth::user()->pressing_id);

        return view('owner.transactions', [
            'transactions' => Transaction::where('pressing_id', Auth::user()->pressing_id)
                ->with(['agency', 'user', 'order', 'expense', 'cancelledBy'])
                ->latest('happened_at')
                ->latest()
                ->get(),
            'pressing' => $pressing,
        ]);
    }

    public function cancelTransaction(Request $request, Transaction $transaction)
    {
        $pressing = Pressing::findOrFail(Auth::user()->pressing_id);
        abort_unless($transaction->pressing_id === $pressing->id, 403);

        if (! $this->canCancelTransaction($pressing, $transaction)) {
            return redirect()->route('owner.ui.transactions')->with('error', 'Cette transaction ne peut plus être annulée.');
        }

        DB::transaction(function () use ($transaction) {
            if ($transaction->type === 'encaissement' && $transaction->order_id) {
                $order = Order::lockForUpdate()->find($transaction->order_id);
                if ($order) {
                    $order->advance_amount = max(0, (float) $order->advance_amount - (float) $transaction->amount);
                    $order->paid_advance = (float) $order->advance_amount > 0;
                    $order->save();
                }
            }

            $transaction->update([
                'is_cancelled' => true,
                'cancelled_by_user_id' => Auth::id(),
                'cancelled_at' => now(),
                'cancellation_note' => 'Annulée par le propriétaire.',
            ]);
        });

        return redirect()->route('owner.ui.transactions')->with('success', 'Transaction annulée.');
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
            'allow_transaction_cancellation' => ['nullable', 'boolean'],
            'transaction_cancellation_window_minutes' => ['nullable', 'required_if:allow_transaction_cancellation,1', 'integer', 'min:1', 'max:1440'],
        ]);

        $pressing = Pressing::findOrFail(Auth::user()->pressing_id);

        if ($request->hasFile('invoice_logo')) {
            $data['invoice_logo_path'] = $request->file('invoice_logo')->store('logos', 'public');
        }

        unset($data['invoice_logo']);

        $data['allow_transaction_cancellation'] = (bool) ($data['allow_transaction_cancellation'] ?? false);
        if (! $data['allow_transaction_cancellation']) {
            $data['transaction_cancellation_window_minutes'] = null;
        }

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
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
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

        $discountAmount = min((float) ($data['discount_amount'] ?? 0), $total);
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

    private function canCancelTransaction(Pressing $pressing, Transaction $transaction): bool
    {
        if (! $pressing->allow_transaction_cancellation || $transaction->is_cancelled) {
            return false;
        }

        $window = (int) ($pressing->transaction_cancellation_window_minutes ?? 0);
        if ($window <= 0) {
            return false;
        }

        $referenceTime = $transaction->happened_at ?? $transaction->created_at;

        return now()->lessThanOrEqualTo($referenceTime->copy()->addMinutes($window));
    }
}
