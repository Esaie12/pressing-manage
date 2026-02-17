@extends('layouts.app')

@section('title', 'Dashboard Employé')
@section('heading', 'Espace Employé')

@section('content')
<div class="grid gap-4 sm:grid-cols-2">
    <div class="bg-white rounded-xl border p-5 shadow-sm">
        <p class="text-sm text-slate-500">Commandes en cours</p>
        <p class="mt-2 text-3xl font-bold text-indigo-600">{{ $inProgress }}</p>
    </div>
    <div class="bg-white rounded-xl border p-5 shadow-sm">
        <p class="text-sm text-slate-500">Commandes retirées</p>
        <p class="mt-2 text-3xl font-bold text-emerald-600">{{ $pickedUp }}</p>
    </div>
</div>

<div class="mt-6 bg-white rounded-xl border p-5 shadow-sm">
    <h2 class="font-semibold mb-3">Rappels opérationnels</h2>
    <ul class="space-y-2 text-sm list-disc pl-5 text-slate-600">
        <li>Confirmer le statut "prête" via <code>PATCH /employee/orders/{'{order}'}/ready</code>.</li>
        <li>Marquer "retirée" via <code>PATCH /employee/orders/{'{order}'}/picked-up</code>.</li>
        <li>Un employé est rattaché à une seule agence.</li>
    </ul>
</div>
@endsection
