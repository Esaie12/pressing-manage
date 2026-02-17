@extends('layouts.app')
@section('title','Employé - Factures')
@section('heading','Employé • Factures')
@section('content')
<div class="card shadow-sm">
  <div class="card-header bg-white">Factures du pressing</div>
  <div class="table-responsive">
    <table class="table table-striped datatable mb-0">
      <thead><tr><th>N°</th><th>Date</th><th>Client</th><th>Agence</th><th>Montant</th><th>Action</th></tr></thead>
      <tbody>
      @forelse($invoices as $invoice)
        <tr>
          <td>{{ $invoice->invoice_number }}</td>
          <td>{{ optional($invoice->issued_at)->format('d/m/Y') }}</td>
          <td>{{ $invoice->order?->client?->name ?: '-' }}</td>
          <td>{{ $invoice->order?->agency?->name ?: '-' }}</td>
          <td>{{ number_format($invoice->amount,0,',',' ') }} FCFA</td>
          <td class="d-flex gap-1 flex-wrap">
            <a class="btn btn-sm btn-outline-primary" href="{{ route('employee.ui.invoices.show', $invoice) }}">Voir</a>
            <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#inv-edit-{{ $invoice->id }}">Modifier</button>
            <form method="POST" action="{{ route('employee.ui.invoices.delete', $invoice) }}" onsubmit="return confirm('Supprimer cette facture ?')">@csrf<button class="btn btn-sm btn-outline-danger">Supprimer</button></form>
          </td>
        </tr>
        <tr id="inv-edit-{{ $invoice->id }}" class="collapse">
          <td colspan="6">
            <form method="POST" action="{{ route('employee.ui.invoices.update', $invoice) }}" class="row g-2">
              @csrf
              <div class="col-md-4"><input class="form-control" type="number" min="0" step="0.01" name="amount" value="{{ $invoice->amount }}" required></div>
              <div class="col-md-3 d-grid"><button class="btn btn-success">Enregistrer</button></div>
            </form>
          </td>
        </tr>
      @empty
        <tr><td colspan="6" class="text-center text-muted">Aucune facture</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
