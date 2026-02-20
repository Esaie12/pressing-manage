@extends('layouts.app')
@section('title','Employé - Clôture de caisse')
@section('heading','Employé • Clôture de caisse')
@section('content')
<div class="card shadow-sm mb-4">
  <div class="card-header">Clôturer mon compte</div>
  <div class="card-body">
    <form method="POST" action="{{ route('employee.ui.cash-closures.store') }}" class="row g-2 align-items-end">
      @csrf
      <div class="col-md-3">
        <label class="form-label">Date</label>
        <input type="date" class="form-control" name="closure_date" value="{{ old('closure_date', $closureDate) }}" required>
      </div>
      <div class="col-md-7">
        <label class="form-label">Note (optionnel)</label>
        <input class="form-control" name="note" placeholder="Commentaire de clôture">
      </div>
      <div class="col-md-2 d-grid">
        <button class="btn btn-primary">CLOTURER</button>
      </div>
    </form>
  </div>
</div>

<div class="card shadow-sm">
  <div class="card-header">Mes clôtures</div>
  <div class="table-responsive">
    <table class="table datatable mb-0 align-middle">
      <thead><tr><th>Date</th><th>Encaissements</th><th>Paiements</th><th>Net</th><th>Transactions</th><th>Action</th></tr></thead>
      <tbody>
      @forelse($closures as $closure)
        <tr>
          <td>{{ $closure->closure_date?->format('d/m/Y') }} <small class="text-muted">{{ $closure->closed_at?->format('H:i') }}</small></td>
          <td>{{ number_format($closure->encaissement_total,0,',',' ') }} FCFA</td>
          <td>{{ number_format($closure->paiement_total,0,',',' ') }} FCFA</td>
          <td><strong>{{ number_format($closure->net_total,0,',',' ') }} FCFA</strong></td>
          <td>{{ $closure->transactions_count }}</td>
          <td><a class="btn btn-sm btn-outline-primary" href="{{ route('employee.ui.cash-closures.show', $closure) }}">Détails</a></td>
        </tr>
      @empty
        <tr><td colspan="6" class="text-center text-muted">Aucune clôture</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
