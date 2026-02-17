@extends('layouts.app')
@section('title','Admin - Abonnements')
@section('heading','Admin • Abonnements')
@section('content')
<div class="row g-3 mb-3">
  <div class="col-md-4"><div class="card"><div class="card-body"><div class="text-muted">Total</div><div class="h2">{{ $total }}</div></div></div></div>
  <div class="col-md-4"><div class="card"><div class="card-body"><div class="text-muted">Actifs</div><div class="h2 text-success">{{ $active }}</div></div></div></div>
  <div class="col-md-4"><div class="card"><div class="card-body"><div class="text-muted">Inactifs</div><div class="h2 text-danger">{{ $inactive }}</div></div></div></div>
</div>
<div class="row g-4">
  <div class="col-lg-4">
    <div class="card shadow-sm">
      <div class="card-header">Créer un abonnement</div>
      <div class="card-body">
        <form method="POST" action="{{ route('admin.ui.subscriptions.store') }}" class="vstack gap-2">
          @csrf
          <select class="form-select" name="pressing_id" required><option value="">-- Pressing --</option>@foreach($pressings as $pressing)<option value="{{ $pressing->id }}">{{ $pressing->name }}</option>@endforeach</select>
          <select class="form-select" name="subscription_plan_id" required><option value="">-- Plan --</option>@foreach($plans as $plan)<option value="{{ $plan->id }}">{{ $plan->name }}</option>@endforeach</select>
          <select class="form-select" name="billing_cycle" required><option value="monthly">Mensuel</option><option value="annual">Annuel</option></select>
          <input class="form-control" type="date" name="starts_at" required>
          <input class="form-control" type="date" name="ends_at" required>
          <div class="form-check"><input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active"><label class="form-check-label" for="is_active">Actif</label></div>
          <button class="btn btn-primary" type="submit">Ajouter</button>
        </form>
      </div>
    </div>
  </div>
  <div class="col-lg-8">
    <div class="card shadow-sm"><div class="card-header">Détail abonnements</div><div class="table-responsive"><table class="table mb-0 datatable"><thead><tr><th>Pressing</th><th>Plan</th><th>Cycle</th><th>Début</th><th>Fin</th><th>Statut</th></tr></thead><tbody>@forelse($subscriptions as $sub)<tr><td>{{ $sub->pressing?->name }}</td><td>{{ $sub->plan?->name }}</td><td>{{ strtoupper($sub->billing_cycle) }}</td><td>{{ $sub->starts_at }}</td><td>{{ $sub->ends_at }}</td><td>{!! $sub->is_active ? '<span class="badge text-bg-success">Actif</span>' : '<span class="badge text-bg-secondary">Inactif</span>' !!}</td></tr>@empty<tr><td colspan="6" class="text-center text-muted">Aucun abonnement</td></tr>@endforelse</tbody></table></div></div>
  </div>
</div>
@endsection
