@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('heading', 'Espace Administration')

@section('content')
<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
    <div class="bg-white rounded-xl border p-5 shadow-sm">
        <p class="text-sm text-slate-500">Propriétaires enregistrés</p>
        <p class="mt-2 text-3xl font-bold text-indigo-600">{{ $ownersCount }}</p>
    </div>
    <div class="bg-white rounded-xl border p-5 shadow-sm">
        <p class="text-sm text-slate-500">Abonnements actifs</p>
        <p class="mt-2 text-3xl font-bold text-emerald-600">{{ $activeSubscriptions }}</p>
    </div>
    <div class="bg-white rounded-xl border p-5 shadow-sm sm:col-span-2 lg:col-span-1">
        <p class="text-sm text-slate-500">Plans disponibles</p>
        <p class="mt-2 text-3xl font-bold text-fuchsia-600">{{ $plansCount }}</p>
    </div>
</div>

<div class="mt-6 bg-white rounded-xl border shadow-sm overflow-hidden">
    <div class="px-4 py-3 border-b">
        <h2 class="font-semibold">Plans d'abonnement</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="text-left px-4 py-3">Nom</th>
                    <th class="text-left px-4 py-3">Mensuel</th>
                    <th class="text-left px-4 py-3">Annuel</th>
                    <th class="text-left px-4 py-3">Agences max</th>
                    <th class="text-left px-4 py-3">Employés max</th>
                </tr>
            </thead>
            <tbody>
                @foreach($plans as $plan)
                    <tr class="border-t">
                        <td class="px-4 py-3 font-medium">{{ $plan->name }}</td>
                        <td class="px-4 py-3">{{ number_format($plan->monthly_price, 0, ',', ' ') }} FCFA</td>
                        <td class="px-4 py-3">{{ number_format($plan->annual_price, 0, ',', ' ') }} FCFA</td>
                        <td class="px-4 py-3">{{ $plan->max_agencies }}</td>
                        <td class="px-4 py-3">{{ $plan->max_employees }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
