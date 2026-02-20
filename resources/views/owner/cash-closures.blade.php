@extends('layouts.app')
@section('title','Owner - Clôture de caisse')
@section('heading','Owner • Clôture de caisse')
@section('content')
<div class="card shadow-sm mb-4">
  <div class="card-header">Nouvelle clôture</div>
  <div class="card-body">
    <form method="POST" action="{{ route('owner.ui.cash-closures.store') }}" class="row g-2 align-items-end">
      @csrf
      <div class="col-md-2">
        <label class="form-label">Date</label>
        <input type="date" class="form-control" name="closure_date" value="{{ old('closure_date', $closureDate) }}" required>
      </div>
      <div class="col-md-3">
        <label class="form-label">Agence (optionnel)</label>
        <select class="form-select" name="agency_id">
          <option value="">Toutes les agences</option>
          @foreach($agencies as $agency)
            <option value="{{ $agency->id }}">{{ $agency->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">Employé (optionnel)</label>
        <select class="form-select" name="employee_id">
          <option value="">Tous les employés</option>
          @foreach($employees as $employee)
            <option value="{{ $employee->id }}">{{ $employee->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">Note (optionnel)</label>
        <input class="form-control" name="note" placeholder="Commentaire de clôture">
      </div>
      <div class="col-md-1 d-grid">
        <button class="btn btn-primary">Clôturer</button>
      </div>
    </form>
  </div>
</div>

<div class="card shadow-sm">
  <div class="card-header">Historique des clôtures</div>
  <div class="table-responsive">
    <table class="table datatable mb-0 align-middle">
      <thead><tr><th>Date clôture</th><th>Agence</th><th>Employé</th><th>Encaissements</th><th>Paiements</th><th>Net</th><th>Transactions</th><th>Clôturé par</th><th>Action</th></tr></thead>
      <tbody>
      @forelse($closures as $closure)
        <tr>
          <td>{{ $closure->closure_date?->format('d/m/Y') }}<br><small class="text-muted">{{ $closure->closed_at?->format('H:i') }}</small></td>
          <td>{{ $closure->agency?->name ?? 'Toutes' }}</td>
          <td>{{ $closure->employee?->name ?? 'Tous' }}</td>
          <td>{{ number_format($closure->encaissement_total,0,',',' ') }} FCFA</td>
          <td>{{ number_format($closure->paiement_total,0,',',' ') }} FCFA</td>
          <td><strong>{{ number_format($closure->net_total,0,',',' ') }} FCFA</strong></td>
          <td>{{ $closure->transactions_count }}</td>
          <td>{{ $closure->closedBy?->name ?? '-' }}</td>
          <td><a class="btn btn-sm btn-outline-primary" href="{{ route('owner.ui.cash-closures.show', $closure) }}">Détails</a></td>
        </tr>
      @empty
        <tr><td colspan="9" class="text-center text-muted">Aucune clôture enregistrée.</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
