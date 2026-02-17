@extends('layouts.app')
@section('title','Owner - Services')
@section('heading','Owner • Services')
@section('content')
<div class="row g-4">
  <div class="col-lg-4">
    <div class="card shadow-sm">
      <div class="card-header">Ajouter un service</div>
      <div class="card-body">
        <form method="POST" action="{{ route('owner.ui.services.store') }}" class="vstack gap-2">@csrf
          <select class="form-select" name="agency_id" required><option value="">-- Agence --</option>@foreach($agencies as $agency)<option value="{{ $agency->id }}">{{ $agency->name }}</option>@endforeach</select>
          <input class="form-control" name="name" placeholder="Nom du service" required>
          <input class="form-control" name="price" type="number" min="0" step="0.01" placeholder="Prix" required>
          <textarea class="form-control" name="description" placeholder="Description"></textarea>
          <button class="btn btn-primary" type="submit">Ajouter</button>
        </form>
      </div>
    </div>
  </div>
  <div class="col-lg-8">
    <div class="d-flex justify-content-end mb-2">
      <a class="btn btn-sm btn-outline-secondary" href="{{ route('owner.ui.services', ['show_deleted' => $filters['show_deleted'] ? null : 1]) }}">{{ $filters['show_deleted'] ? 'Masquer supprimés' : 'Voir supprimés' }}</a>
    </div>
    <div class="card"><div class="card-header">Services du pressing</div><div class="table-responsive"><table class="table mb-0 datatable"><thead><tr><th>Service</th><th>Agence</th><th>Prix</th><th>Statut</th><th>Actions</th></tr></thead><tbody>@forelse($services as $service)<tr><td>{{ $service->name }} @if($service->deleted_at)<span class='badge bg-danger'>Supprimé</span>@endif</td><td>{{ $service->agency?->name ?? '-' }}</td><td>{{ number_format($service->price,0,',',' ') }} FCFA</td><td>{!! $service->is_active ? '<span class="badge text-bg-success">Actif</span>' : '<span class="badge text-bg-secondary">Inactif</span>' !!}</td><td class="d-flex gap-1 flex-wrap">@if(!$service->deleted_at)<form method="POST" action="{{ route('owner.ui.services.toggle', $service) }}">@csrf<button class="btn btn-sm btn-outline-warning">{{ $service->is_active ? 'Désactiver' : 'Activer' }}</button></form><button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editService{{ $service->id }}">Modifier</button><form method="POST" action="{{ route('owner.ui.services.delete', $service) }}">@csrf<button class="btn btn-sm btn-outline-danger">Supprimer</button></form>@else<form method="POST" action="{{ route('owner.ui.services.forceDelete', $service->id) }}" onsubmit="return confirm('Suppression définitive ?')">@csrf<button class="btn btn-sm btn-danger">Supprimer définitivement</button></form>@endif</td></tr>
<div class="modal fade" id="editService{{ $service->id }}" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Modifier service</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><form method="POST" action="{{ route('owner.ui.services.update', $service) }}" class="vstack gap-2">@csrf
<select class="form-select" name="agency_id" required><option value="">-- Agence --</option>@foreach($agencies as $agency)<option value="{{ $agency->id }}" @selected($service->agency_id===$agency->id)>{{ $agency->name }}</option>@endforeach</select>
<input class="form-control" name="name" value="{{ $service->name }}" required>
<input class="form-control" name="price" type="number" min="0" step="0.01" value="{{ $service->price }}" required>
<textarea class="form-control" name="description">{{ $service->description }}</textarea>
<button class="btn btn-success">Enregistrer</button></form></div></div></div></div>
@empty<tr><td colspan="5" class="text-center text-muted">Aucun service</td></tr>@endforelse</tbody></table></div></div>
  </div>
</div>
@endsection
