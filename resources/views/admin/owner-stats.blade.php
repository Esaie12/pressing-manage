@extends('layouts.app')
@section('title','Admin - Stats propriétaire')
@section('heading','Admin • Stats propriétaire')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h4 class="mb-0">{{ $owner->name }}</h4>
    <small class="text-muted">{{ $owner->pressing?->name }}</small>
  </div>
  <a href="{{ route('admin.ui.owners') }}" class="btn btn-outline-secondary">Retour</a>
</div>

<div class="row g-3 mb-3">
  <div class="col-md-3"><div class="card"><div class="card-body"><div class="text-muted">Nb agences</div><div class="h3">{{ $agencies->count() }}</div></div></div></div>
  <div class="col-md-3"><div class="card"><div class="card-body"><div class="text-muted">Nb employés</div><div class="h3">{{ $employeesCount }}</div></div></div></div>
  <div class="col-md-3"><div class="card"><div class="card-body"><div class="text-muted">CA</div><div class="h3">{{ number_format($revenue,0,',',' ') }}</div></div></div></div>
  <div class="col-md-3"><div class="card"><div class="card-body"><div class="text-muted">Dépenses</div><div class="h3">{{ number_format($expenses,0,',',' ') }}</div></div></div></div>
</div>

<div class="card shadow-sm mb-3">
  <div class="card-header bg-white">Abonnement actif</div>
  <div class="card-body">
    @if($activeSubscription)
      <p class="mb-1"><strong>Pack:</strong> {{ $activeSubscription->plan?->name ?? '-' }}</p>
      <p class="mb-1"><strong>Cycle:</strong> {{ $activeSubscription->billing_cycle }}</p>
      <p class="mb-0"><strong>Période:</strong> {{ optional($activeSubscription->starts_at)->format('d/m/Y') }} - {{ optional($activeSubscription->ends_at)->format('d/m/Y') }}</p>
    @else
      <p class="text-muted mb-0">Aucun abonnement actif.</p>
    @endif
  </div>
</div>

<div class="card shadow-sm">
  <div class="card-header bg-white">Agences du pressing</div>
  <div class="table-responsive">
    <table class="table mb-0">
      <thead><tr><th>Nom</th><th>Téléphone</th><th>Adresse</th><th>Statut</th></tr></thead>
      <tbody>
        @forelse($agencies as $agency)
          <tr>
            <td>{{ $agency->name }}</td>
            <td>{{ $agency->phone ?: '-' }}</td>
            <td>{{ $agency->address ?: '-' }}</td>
            <td>{!! $agency->is_active ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>' !!}</td>
          </tr>
        @empty
          <tr><td colspan="4" class="text-muted text-center">Aucune agence</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
