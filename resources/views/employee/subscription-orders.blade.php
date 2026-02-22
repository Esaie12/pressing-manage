@extends('layouts.app')
@section('title','Employé - Commandes abonnements')
@section('heading','Employé • Commandes Abonnement')
@section('content')
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card"><div class="card-header">Nouvelle commande abonnement</div><div class="card-body">
            <form method="POST" action="{{ route('employee.ui.subscription-orders.store') }}" class="vstack gap-2">@csrf
                <label class="form-label mb-0">Contrat</label>
                <select class="form-select" name="subscription_contract_id" required><option value="">Contrat</option>@foreach($contracts as $ct)<option value="{{ $ct->id }}">{{ $ct->client?->name }} - {{ $ct->title }}</option>@endforeach</select>

                <label class="form-label mb-0">Agence</label>
                <select class="form-select" name="agency_id" required><option value="">Agence</option>@foreach($agencies as $agency)<option value="{{ $agency->id }}" @selected((string)auth()->user()->agency_id === (string)$agency->id)>{{ $agency->name }}</option>@endforeach</select>

                <label class="form-label mb-0">Date commande</label>
                <input class="form-control" type="date" name="order_date" required>

                <label class="form-label mb-0">Notes</label>
                <input class="form-control" name="notes" placeholder="Notes">
                <button class="btn btn-primary">Enregistrer</button>
            </form>
        </div></div>
    </div>
    <div class="col-lg-8">
        <div class="card"><div class="card-header">Liste commandes abonnement</div><div class="table-responsive"><table class="table mb-0"><thead><tr><th>Réf</th><th>Client</th><th>Agence</th><th>Date</th><th>Statut</th><th>Action</th><th>Employé</th></tr></thead><tbody>
            @forelse($orders as $order)
            <tr>
                <td>{{ $order->reference }}</td><td>{{ $order->contract?->client?->name }}</td><td>{{ $order->agency?->name ?? '-' }}</td><td>{{ $order->order_date?->format('d/m/Y') }}</td>
                <td><span class="badge text-bg-{{ $order->status === 'pending' ? 'warning' : ($order->status === 'ready' ? 'primary' : 'success') }}{{ $order->status === 'pending' ? ' text-dark' : '' }}">{{ $statuses[$order->status] ?? $order->status }}</span></td>
                <td>
                    @if($order->status === 'pending')
                        <form method="POST" action="{{ route('employee.ui.subscription-orders.ready', $order) }}">@csrf<button class="btn btn-sm btn-outline-primary">Prête</button></form>
                    @elseif($order->status === 'ready')
                        <form method="POST" action="{{ route('employee.ui.subscription-orders.delivered', $order) }}">@csrf<button class="btn btn-sm btn-outline-success">Livrée</button></form>
                    @else
                        <span class="text-muted small">Terminée</span>
                    @endif
                </td>
                <td>{{ $order->employee?->name ?? '-' }}</td>
            </tr>
            @empty <tr><td colspan="7" class="text-center text-muted">Aucune commande abonnement</td></tr> @endforelse
        </tbody></table></div></div>
    </div>
</div>
@endsection
