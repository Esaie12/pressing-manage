@extends('layouts.app')
@section('title','Owner - Agences')
@section('heading','Owner • Agences')
@section('content')
<div class="row g-4">
  <div class="col-lg-4">
    <div class="card shadow-sm">
      <div class="card-header">Créer une agence</div>
      <div class="card-body">
        <form method="POST" action="{{ route('owner.ui.agencies.store') }}" class="vstack gap-2">
          @csrf
          <input class="form-control" name="name" placeholder="Nom de l'agence" required>
          <input class="form-control" name="phone" placeholder="Téléphone">
          <input class="form-control" name="address" placeholder="Adresse">
          <button class="btn btn-primary" type="submit">Créer</button>
        </form>
      </div>
    </div>
  </div>
  <div class="col-lg-8">
    <div class="card"><div class="card-header">Mes agences</div><div class="table-responsive"><table class="table mb-0 datatable"><thead><tr><th>Nom</th><th>Téléphone</th><th>Adresse</th><th>Statut</th><th>Action</th></tr></thead><tbody>@forelse($agencies as $agency)<tr><td>{{ $agency->name }}</td><td>{{ $agency->phone ?? '-' }}</td><td>{{ $agency->address ?? '-' }}</td><td>{!! $agency->is_active ? '<span class="badge text-bg-success">Active</span>' : '<span class="badge text-bg-secondary">Désactivée</span>' !!}</td><td><form method="POST" action="{{ route('owner.ui.agencies.toggle', $agency) }}">@csrf<button class="btn btn-sm btn-outline-warning" type="submit">{{ $agency->is_active ? 'Désactiver' : 'Réactiver' }}</button></form></td></tr>@empty<tr><td colspan="5" class="text-center text-muted">Aucune agence</td></tr>@endforelse</tbody></table></div></div>
  </div>
</div>
@endsection
