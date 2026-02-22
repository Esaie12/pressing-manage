@extends('layouts.app')
@section('title','Employé - Commandes abonnements')
@section('heading','Employé • Commandes Abonnement')
@section('content')
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card"><div class="card-header">Nouvelle commande abonnement</div><div class="card-body">
            <form method="POST" action="{{ route('employee.ui.subscription-orders.store') }}" class="vstack gap-2">@csrf
                <select class="form-select" name="subscription_contract_id" required><option value="">Contrat</option>@foreach($contracts as $ct)<option value="{{ $ct->id }}">{{ $ct->client?->name }} - {{ $ct->title }}</option>@endforeach</select>
                <input class="form-control" type="date" name="order_date" required>
                <input class="form-control" type="date" name="pickup_date">
                <input class="form-control" type="number" min="1" name="items_count" value="1" required>
                <input class="form-control" name="notes" placeholder="Notes">
                <button class="btn btn-primary">Enregistrer</button>
            </form>
        </div></div>
    </div>
    <div class="col-lg-8">
        <div class="card"><div class="card-header">Liste commandes abonnement</div><div class="table-responsive"><table class="table mb-0"><thead><tr><th>Réf</th><th>Client</th><th>Pièces</th><th>Statut</th><th>Employé</th></tr></thead><tbody>
            @forelse($orders as $order)
            <tr>
                <td>{{ $order->reference }}</td><td>{{ $order->contract?->client?->name }}</td><td>{{ $order->items_count }}</td>
                <td><form method="POST" action="{{ route('employee.ui.subscription-orders.status', $order) }}" class="d-flex gap-2">@csrf
                    <select class="form-select form-select-sm" name="status">@foreach($statuses as $k=>$label)<option value="{{ $k }}" @selected($order->status===$k)>{{ $label }}</option>@endforeach</select>
                    <button class="btn btn-sm btn-outline-primary">Changer</button>
                </form></td>
                <td>{{ $order->employee?->name ?? '-' }}</td>
            </tr>
            @empty <tr><td colspan="5" class="text-center text-muted">Aucune commande abonnement</td></tr> @endforelse
        </tbody></table></div></div>
    </div>
</div>
@endsection
