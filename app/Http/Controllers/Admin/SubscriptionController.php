<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OwnerSubscription;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubscriptionController extends Controller
{
    public function plans()
    {
        return response()->json(SubscriptionPlan::all());
    }

    public function attachToOwner(Request $request)
    {
        $data = $request->validate([
            'pressing_id' => ['required', 'exists:pressings,id'],
            'subscription_plan_id' => ['required', 'exists:subscription_plans,id'],
            'billing_cycle' => ['required', Rule::in(['monthly', 'annual'])],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after_or_equal:starts_at'],
            'is_active' => ['boolean'],
        ]);

        $subscription = OwnerSubscription::create($data + [
            'is_active' => $data['is_active'] ?? true,
        ]);

        return response()->json($subscription, 201);
    }
}
