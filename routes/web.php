<?php

use App\Http\Controllers\Admin\OwnerController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\Employee\OrderLifecycleController;
use App\Http\Controllers\Owner\AgencyController;
use App\Http\Controllers\Owner\EmployeeController;
use App\Http\Controllers\Owner\OrderController;
use App\Http\Controllers\Owner\ServiceController;
use App\Http\Controllers\Web\AdminUiController;
use App\Http\Controllers\Web\EmployeeUiController;
use App\Http\Controllers\Web\NotificationUiController;
use App\Http\Controllers\Web\OwnerUiController;
use App\Http\Controllers\Web\ProfileUiController;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('landing'))->name('landing');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();

        return match ($user->role) {
            'admin' => redirect()->route('admin.ui.dashboard'),
            'owner' => redirect()->route('owner.ui.dashboard'),
            default => redirect()->route('employee.ui.dashboard'),
        };
    })->name('dashboard');

    Route::get('/profile', [ProfileUiController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileUiController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [ProfileUiController::class, 'updatePassword'])->name('profile.password');

    Route::post('/notifications/mark-all-read', [NotificationUiController::class, 'markAllRead'])->name('notifications.markAllRead');
    Route::post('/notifications/clear-all', [NotificationUiController::class, 'clearAll'])->name('notifications.clearAll');
});

Route::middleware(['auth', RoleMiddleware::class.':admin'])->prefix('admin')->group(function () {
    Route::get('/owners', [OwnerController::class, 'index']);
    Route::post('/owners', [OwnerController::class, 'store']);
    Route::get('/subscription-plans', [SubscriptionController::class, 'plans']);
    Route::post('/owner-subscriptions', [SubscriptionController::class, 'attachToOwner']);

    Route::get('/ui/dashboard', [AdminUiController::class, 'dashboard'])->name('admin.ui.dashboard');
    Route::get('/ui/owners', [AdminUiController::class, 'owners'])->name('admin.ui.owners');
    Route::post('/ui/owners', [AdminUiController::class, 'storeOwner'])->name('admin.ui.owners.store');
    Route::get('/ui/owners/{owner}/stats', [AdminUiController::class, 'ownerStats'])->name('admin.ui.owners.stats');
    Route::get('/ui/subscriptions', [AdminUiController::class, 'subscriptions'])->name('admin.ui.subscriptions');
    Route::post('/ui/subscriptions', [AdminUiController::class, 'storeSubscription'])->name('admin.ui.subscriptions.store');
    Route::get('/ui/pricing', [AdminUiController::class, 'pricing'])->name('admin.ui.pricing');
    Route::post('/ui/pricing', [AdminUiController::class, 'storePlan'])->name('admin.ui.pricing.store');
    Route::post('/ui/pricing/{plan}', [AdminUiController::class, 'updatePlan'])->name('admin.ui.pricing.update');
});

Route::middleware(['auth', RoleMiddleware::class.':owner'])->prefix('owner')->group(function () {
    Route::get('/agencies', [AgencyController::class, 'index']);
    Route::post('/agencies', [AgencyController::class, 'store']);
    Route::get('/employees', [EmployeeController::class, 'index']);
    Route::post('/employees', [EmployeeController::class, 'store']);
    Route::get('/services', [ServiceController::class, 'index']);
    Route::post('/services', [ServiceController::class, 'store']);
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/stats', [OrderController::class, 'stats']);

    Route::get('/ui/dashboard', [OwnerUiController::class, 'dashboard'])->name('owner.ui.dashboard');
    Route::get('/ui/agencies', [OwnerUiController::class, 'agencies'])->name('owner.ui.agencies');
    Route::post('/ui/agencies', [OwnerUiController::class, 'storeAgency'])->name('owner.ui.agencies.store');
    Route::post('/ui/agencies/{agency}/toggle', [OwnerUiController::class, 'toggleAgency'])->name('owner.ui.agencies.toggle');

    Route::get('/ui/employees', [OwnerUiController::class, 'employees'])->name('owner.ui.employees');
    Route::post('/ui/employees', [OwnerUiController::class, 'storeEmployee'])->name('owner.ui.employees.store');
    Route::post('/ui/employees/{employee}/toggle', [OwnerUiController::class, 'toggleEmployee'])->name('owner.ui.employees.toggle');
    Route::post('/ui/employees/{employee}/password', [OwnerUiController::class, 'updateEmployeePassword'])->name('owner.ui.employees.password');

    Route::get('/ui/services', [OwnerUiController::class, 'services'])->name('owner.ui.services');
    Route::post('/ui/services', [OwnerUiController::class, 'storeService'])->name('owner.ui.services.store');
    Route::post('/ui/services/{service}', [OwnerUiController::class, 'updateService'])->name('owner.ui.services.update');
    Route::post('/ui/services/{service}/toggle', [OwnerUiController::class, 'toggleService'])->name('owner.ui.services.toggle');
    Route::post('/ui/services/{service}/delete', [OwnerUiController::class, 'destroyService'])->name('owner.ui.services.delete');
    Route::post('/ui/services/{service}/force-delete', [OwnerUiController::class, 'forceDeleteService'])->name('owner.ui.services.forceDelete');

    Route::get('/ui/orders', [OwnerUiController::class, 'orders'])->name('owner.ui.orders');
    Route::post('/ui/orders/{order}/payments', [OwnerUiController::class, 'addPayment'])->name('owner.ui.orders.payments.store');
    Route::post('/ui/orders/{order}/discount', [OwnerUiController::class, 'applyDiscount'])->name('owner.ui.orders.discount');
    Route::post('/ui/orders', [OwnerUiController::class, 'storeOrder'])->name('owner.ui.orders.store');
    Route::get('/ui/orders/{order}/edit', [OwnerUiController::class, 'editOrder'])->name('owner.ui.orders.edit');
    Route::post('/ui/orders/{order}', [OwnerUiController::class, 'updateOrder'])->name('owner.ui.orders.update');
    Route::post('/ui/orders/{order}/delete', [OwnerUiController::class, 'destroyOrder'])->name('owner.ui.orders.delete');

    Route::get('/ui/invoices', [OwnerUiController::class, 'invoices'])->name('owner.ui.invoices');
    Route::get('/ui/transactions', [OwnerUiController::class, 'transactions'])->name('owner.ui.transactions');
    Route::get('/ui/invoices/{invoice}', [OwnerUiController::class, 'showInvoice'])->name('owner.ui.invoices.show');

    Route::get('/ui/settings', [OwnerUiController::class, 'settings'])->name('owner.ui.settings');
    Route::post('/ui/settings', [OwnerUiController::class, 'updateSettings'])->name('owner.ui.settings.update');

    Route::get('/ui/stats', [OwnerUiController::class, 'stats'])->name('owner.ui.stats');

    Route::get('/ui/requests', [OwnerUiController::class, 'requests'])->name('owner.ui.requests');
    Route::post('/ui/requests/{employeeRequest}/read', [OwnerUiController::class, 'markRequestRead'])->name('owner.ui.requests.read');

    Route::get('/ui/expenses', [OwnerUiController::class, 'expenses'])->name('owner.ui.expenses');
    Route::post('/ui/expenses', [OwnerUiController::class, 'storeExpense'])->name('owner.ui.expenses.store');
    Route::post('/ui/expenses/{expense}', [OwnerUiController::class, 'updateExpense'])->name('owner.ui.expenses.update');
    Route::post('/ui/expenses/{expense}/delete', [OwnerUiController::class, 'destroyExpense'])->name('owner.ui.expenses.delete');

    Route::get('/ui/pricing', [OwnerUiController::class, 'pricing'])->name('owner.ui.pricing');
    Route::post('/ui/pricing/subscribe', [OwnerUiController::class, 'subscribePlan'])->name('owner.ui.pricing.subscribe');
});

Route::middleware(['auth', RoleMiddleware::class.':employee,owner'])->prefix('employee')->group(function () {
    Route::patch('/orders/{order}/ready', [OrderLifecycleController::class, 'markReady']);
    Route::patch('/orders/{order}/picked-up', [OrderLifecycleController::class, 'markPickedUp']);
});

Route::middleware(['auth', RoleMiddleware::class.':employee'])->prefix('employee')->group(function () {
    Route::get('/ui/dashboard', [EmployeeUiController::class, 'dashboard'])->name('employee.ui.dashboard');
    Route::get('/ui/requests', [EmployeeUiController::class, 'requests'])->name('employee.ui.requests');
    Route::post('/ui/requests', [EmployeeUiController::class, 'createRequest'])->name('employee.ui.requests.store');
    Route::post('/ui/requests/{employeeRequest}', [EmployeeUiController::class, 'updateRequest'])->name('employee.ui.requests.update');
    Route::post('/ui/requests/{employeeRequest}/delete', [EmployeeUiController::class, 'destroyRequest'])->name('employee.ui.requests.delete');

    Route::get('/ui/orders', [EmployeeUiController::class, 'orders'])->name('employee.ui.orders');
    Route::post('/ui/orders', [EmployeeUiController::class, 'storeOrder'])->name('employee.ui.orders.store');
    Route::get('/ui/orders/{order}/edit', [EmployeeUiController::class, 'editOrder'])->name('employee.ui.orders.edit');
    Route::post('/ui/orders/{order}', [EmployeeUiController::class, 'updateOrder'])->name('employee.ui.orders.update');
    Route::post('/ui/orders/{order}/ready', [EmployeeUiController::class, 'markReady'])->name('employee.ui.orders.ready');
    Route::post('/ui/orders/{order}/picked-up', [EmployeeUiController::class, 'markPickedUp'])->name('employee.ui.orders.picked');
    Route::post('/ui/orders/{order}/payments', [EmployeeUiController::class, 'addPayment'])->name('employee.ui.orders.payments.store');
    Route::post('/ui/orders/{order}/discount', [EmployeeUiController::class, 'applyDiscount'])->name('employee.ui.orders.discount');

    Route::get('/ui/invoices', [EmployeeUiController::class, 'invoices'])->name('employee.ui.invoices');
    Route::get('/ui/transactions', [EmployeeUiController::class, 'transactions'])->name('employee.ui.transactions');
    Route::get('/ui/invoices/{invoice}', [EmployeeUiController::class, 'showInvoice'])->name('employee.ui.invoices.show');
    Route::post('/ui/invoices/{invoice}/delete', [EmployeeUiController::class, 'destroyInvoice'])->name('employee.ui.invoices.delete');
});
