@extends('layouts.app')
@section('title','Owner - Module abonnements')
@section('heading','Owner • Module Contrats abonnements clients')
@section('content')
<div class="row g-4">
    <div class="col-lg-3">
        <div class="list-group shadow-sm">
            <a class="list-group-item list-group-item-action @if($section==='clients') active @endif" href="{{ route('owner.ui.subscriptions-module', ['section' => 'clients']) }}">Clients</a>
            <a class="list-group-item list-group-item-action @if($section==='contracts') active @endif" href="{{ route('owner.ui.subscriptions-module', ['section' => 'contracts']) }}">Contrats</a>
            <a class="list-group-item list-group-item-action @if($section==='orders') active @endif" href="{{ route('owner.ui.subscriptions-module', ['section' => 'orders']) }}">Commandes abonnements</a>
        </div>
    </div>

    <div class="col-lg-9">
        @if($section==='clients')
            <div class="card mb-3"><div class="card-header">Ajouter un client</div><div class="card-body">
                <form method="POST" action="{{ route('owner.ui.subscriptions-module.clients.store') }}" class="row g-2">@csrf
                    <div class="col-md-6"><input class="form-control" name="name" placeholder="Nom client (ex: Hôtel X)" required></div>
                    <div class="col-md-6"><input class="form-control" name="company_type" placeholder="Type (Hôtel, Restaurant...)" ></div>
                    <div class="col-md-6"><input class="form-control" name="contact_person" placeholder="Personne de contact" ></div>
                    <div class="col-md-6"><input class="form-control" name="phone" placeholder="Téléphone" ></div>
                    <div class="col-md-6"><input class="form-control" type="email" name="email" placeholder="Email" ></div>
                    <div class="col-md-6"><input class="form-control" name="address" placeholder="Adresse" ></div>
                    <div class="col-12"><button class="btn btn-primary">Ajouter</button></div>
                </form>
            </div></div>

            <div class="card"><div class="card-header">Liste des clients</div><div class="table-responsive"><table class="table mb-0"><thead><tr><th>Client</th><th>Contact</th><th>Actions</th></tr></thead><tbody>
                @forelse($clients as $client)
                    <tr>
                        <td>{{ $client->name }} <div class="small text-muted">{{ $client->company_type }}</div></td>
                        <td>{{ $client->contact_person }}<br><span class="small">{{ $client->phone }} • {{ $client->email }}</span></td>
                        <td class="d-flex gap-1">
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editClient{{ $client->id }}">Modifier</button>
                            <form method="POST" action="{{ route('owner.ui.subscriptions-module.clients.delete', $client) }}" onsubmit="return confirm('Supprimer ce client ?')">@csrf<button class="btn btn-sm btn-outline-danger">Supprimer</button></form>
                        </td>
                    </tr>
                @empty <tr><td colspan="3" class="text-center text-muted">Aucun client abonnement</td></tr> @endforelse
            </tbody></table></div></div>
        @endif

        @if($section==='contracts')
            <div class="card mb-3"><div class="card-header">Créer un contrat</div><div class="card-body">
                <form method="POST" action="{{ route('owner.ui.subscriptions-module.contracts.store') }}" class="row g-2">@csrf
                    <div class="col-md-6"><label class="form-label">Client</label><select class="form-select" name="subscription_client_id" required><option value="">Choisir un client</option>@foreach($clients as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select></div>
                    <div class="col-md-6"><label class="form-label">Titre du contrat</label><input class="form-control" name="title" placeholder="Ex: Contrat Hôtel X" required></div>
                    <div class="col-md-4"><label class="form-label">Date de début</label><input class="form-control" type="date" name="starts_at" required></div>
                    <div class="col-md-4"><label class="form-label">Date de fin</label><input class="form-control" type="date" name="ends_at"></div>
                    <div class="col-md-2"><label class="form-label">Fréquence</label><input class="form-control" type="number" min="1" max="365" name="frequency_interval" value="1" required></div>
                    <div class="col-md-2"><label class="form-label">Unité</label><select class="form-select" name="frequency_unit" required>@foreach($frequencyUnits as $k=>$label)<option value="{{ $k }}">{{ $label }}</option>@endforeach</select></div>
                    <div class="col-md-6"><label class="form-label">Prix du contrat</label><input class="form-control" type="number" min="0" step="0.01" name="price" required></div>
                    <div class="col-md-6"><label class="form-label">Notes</label><input class="form-control" name="notes" placeholder="Notes"></div>
                    <div class="col-12"><button class="btn btn-primary">Créer contrat</button></div>
                </form>
            </div></div>

            <div class="card"><div class="card-header">Liste des contrats</div><div class="table-responsive"><table class="table mb-0"><thead><tr><th>Client</th><th>Période</th><th>Fréquence</th><th>Statut</th><th>Prix</th><th>Actions</th></tr></thead><tbody>
                @forelse($contracts as $contract)
                <tr>
                    <td>{{ $contract->client?->name }}<div class="small text-muted">{{ $contract->title }}</div></td>
                    <td>{{ $contract->starts_at?->format('d/m/Y') }} - {{ $contract->ends_at?->format('d/m/Y') ?? 'Sans fin' }}</td>
                    <td>Tous les {{ $contract->frequency_interval }} {{ $frequencyUnits[$contract->frequency_unit] ?? $contract->frequency_unit }}</td>
                    <td><span class="badge bg-{{ $contract->status?->badge_class ?? 'secondary' }}{{ ($contract->status?->badge_class==='warning') ? ' text-dark' : '' }}">{{ $contract->status?->label ?? '-' }}</span></td>
                    <td>{{ number_format($contract->price,0,',',' ') }} FCFA</td>
                    <td><button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editContract{{ $contract->id }}">Modifier</button></td>
                </tr>
                @empty <tr><td colspan="6" class="text-center text-muted">Aucun contrat</td></tr> @endforelse
            </tbody></table></div></div>
        @endif

        @if($section==='orders')
            <div class="card mb-3"><div class="card-header">Créer une commande abonnement</div><div class="card-body">
                <form method="POST" action="{{ route('owner.ui.subscriptions-module.orders.store') }}" class="row g-2">@csrf
                    <div class="col-md-6"><label class="form-label">Contrat</label><select class="form-select" name="subscription_contract_id" required><option value="">Contrat</option>@foreach($contracts as $ct)<option value="{{ $ct->id }}">{{ $ct->client?->name }} - {{ $ct->title }}</option>@endforeach</select></div>
                    <div class="col-md-6"><label class="form-label">Agence</label><select class="form-select" name="agency_id" required><option value="">Agence</option>@foreach($agencies as $a)<option value="{{ $a->id }}">{{ $a->name }}</option>@endforeach</select></div>
                    <div class="col-md-6"><label class="form-label">Date commande</label><input class="form-control" type="date" name="order_date" required></div>
                    <div class="col-md-6"><label class="form-label">Notes</label><input class="form-control" name="notes" placeholder="Notes"></div>
                    <div class="col-12"><button class="btn btn-primary">Créer commande</button></div>
                </form>
            </div></div>

            <div class="card"><div class="card-header">Liste commandes abonnements</div><div class="table-responsive"><table class="table mb-0"><thead><tr><th>Réf</th><th>Client</th><th>Agence</th><th>Date</th><th>Statut</th><th>Action</th></tr></thead><tbody>
                @forelse($orders as $order)
                <tr>
                    <td>{{ $order->reference }}</td><td>{{ $order->contract?->client?->name }}</td><td>{{ $order->agency?->name ?? '-' }}</td><td>{{ $order->order_date?->format('d/m/Y') }}</td>
                    <td><span class="badge text-bg-{{ $order->status === 'pending' ? 'warning' : ($order->status === 'ready' ? 'primary' : 'success') }}{{ $order->status === 'pending' ? ' text-dark' : '' }}">{{ $statuses[$order->status] ?? $order->status }}</span></td>
                    <td>@if($order->status === 'pending')<form method="POST" action="{{ route('owner.ui.subscriptions-module.orders.ready', $order) }}">@csrf<button class="btn btn-sm btn-outline-primary">Prête</button></form>@elseif($order->status === 'ready')<form method="POST" action="{{ route('owner.ui.subscriptions-module.orders.delivered', $order) }}">@csrf<button class="btn btn-sm btn-outline-success">Livrée</button></form>@else<span class="text-muted small">Terminée</span>@endif</td>
                </tr>
                @empty <tr><td colspan="6" class="text-center text-muted">Aucune commande abonnement</td></tr> @endforelse
            </tbody></table></div></div>
        @endif
    </div>
</div>

@foreach($clients as $client)
<div class="modal fade" id="editClient{{ $client->id }}" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Modifier client</h5><button class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body">
<form method="POST" action="{{ route('owner.ui.subscriptions-module.clients.update', $client) }}" class="vstack gap-2">@csrf
<input class="form-control" name="name" value="{{ $client->name }}" required>
<input class="form-control" name="company_type" value="{{ $client->company_type }}">
<input class="form-control" name="contact_person" value="{{ $client->contact_person }}">
<input class="form-control" name="phone" value="{{ $client->phone }}">
<input class="form-control" type="email" name="email" value="{{ $client->email }}">
<input class="form-control" name="address" value="{{ $client->address }}">
<button class="btn btn-primary">Enregistrer</button>
</form></div></div></div></div>
@endforeach

@foreach($contracts as $contract)
<div class="modal fade" id="editContract{{ $contract->id }}" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Modifier contrat</h5><button class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body">
<form method="POST" action="{{ route('owner.ui.subscriptions-module.contracts.update', $contract) }}" class="vstack gap-2">@csrf
<input class="form-control" name="title" value="{{ $contract->title }}" required>
<input class="form-control" type="date" name="starts_at" value="{{ $contract->starts_at?->toDateString() }}" required>
<input class="form-control" type="date" name="ends_at" value="{{ $contract->ends_at?->toDateString() }}">
<div class="row g-2"><div class="col-6"><label class="form-label">Fréquence</label><input class="form-control" type="number" min="1" max="365" name="frequency_interval" value="{{ $contract->frequency_interval }}" required></div><div class="col-6"><label class="form-label">Unité</label><select class="form-select" name="frequency_unit">@foreach($frequencyUnits as $k=>$label)<option value="{{ $k }}" @selected($contract->frequency_unit===$k)>{{ $label }}</option>@endforeach</select></div></div>
<input class="form-control" type="number" min="0" step="0.01" name="price" value="{{ $contract->price }}" required>
<input class="form-control" name="notes" value="{{ $contract->notes }}">
<label class="form-label">Statut du contrat</label><select class="form-select" name="subscription_contract_status_id" required>@foreach($contractStatuses as $status)<option value="{{ $status->id }}" @selected($contract->subscription_contract_status_id===$status->id)>{{ $status->label }}</option>@endforeach</select><div class="form-check"><input class="form-check-input" type="checkbox" value="1" name="is_active" id="active{{ $contract->id }}" @checked($contract->is_active)><label class="form-check-label" for="active{{ $contract->id }}">Actif</label></div>
<button class="btn btn-primary">Enregistrer</button>
</form></div></div></div></div>
@endforeach
@endsection
