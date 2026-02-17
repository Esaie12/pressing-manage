@extends('layouts.app')

@section('title', 'Dashboard Propriétaire')
@section('heading', 'Espace Propriétaire')

@section('content')
<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <div class="bg-white rounded-xl border p-5 shadow-sm">
        <p class="text-sm text-slate-500">Agences</p>
        <p class="mt-2 text-3xl font-bold text-indigo-600">{{ $agenciesCount }}</p>
    </div>
    <div class="bg-white rounded-xl border p-5 shadow-sm">
        <p class="text-sm text-slate-500">Employés</p>
        <p class="mt-2 text-3xl font-bold text-emerald-600">{{ $employeesCount }}</p>
    </div>
    <div class="bg-white rounded-xl border p-5 shadow-sm">
        <p class="text-sm text-slate-500">Commandes</p>
        <p class="mt-2 text-3xl font-bold text-fuchsia-600">{{ $ordersCount }}</p>
    </div>
    <div class="bg-white rounded-xl border p-5 shadow-sm">
        <p class="text-sm text-slate-500">CA total</p>
        <p class="mt-2 text-3xl font-bold text-orange-600">{{ number_format($revenue, 0, ',', ' ') }}</p>
    </div>
</div>

<div class="mt-6 grid gap-4 lg:grid-cols-3">
    <div class="bg-white rounded-xl border p-5 shadow-sm lg:col-span-2">
        <h2 class="font-semibold mb-3">Actions rapides</h2>
        <div class="grid sm:grid-cols-2 gap-3 text-sm">
            <div class="rounded-lg border p-3">Créer une agence: <code>POST /owner/agencies</code></div>
            <div class="rounded-lg border p-3">Ajouter un employé: <code>POST /owner/employees</code></div>
            <div class="rounded-lg border p-3">Créer un service: <code>POST /owner/services</code></div>
            <div class="rounded-lg border p-3">Créer une commande: <code>POST /owner/orders</code></div>
        </div>
    </div>
    <div class="bg-white rounded-xl border p-5 shadow-sm">
        <h2 class="font-semibold mb-3">Suivi commandes</h2>
        <ul class="space-y-2 text-sm">
            <li class="flex justify-between"><span>Payées d'avance</span><span class="font-semibold">{{ $advancePaidCount }}</span></li>
            <li class="flex justify-between"><span>Retirées</span><span class="font-semibold">{{ $pickedUpCount }}</span></li>
        </ul>
    </div>
</div>
@endsection
