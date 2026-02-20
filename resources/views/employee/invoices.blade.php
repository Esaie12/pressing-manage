@extends('layouts.app')
@section('title','Employé - Factures')
@section('heading','Employé • Factures')
@section('content')
<div class="card shadow-sm">
  <div class="card-header bg-white">Factures du pressing</div>
  <div class="table-responsive">
    <table class="table table-striped datatable mb-0 align-middle">
      <thead><tr><th>N°</th><th>Date</th><th>Client</th><th>Agence</th><th>Montant</th><th>Action</th></tr></thead>
      <tbody>
      @foreach($invoices as $invoice)
        <tr>
          <td>{{ $invoice->invoice_number }}</td>
          <td>{{ optional($invoice->issued_at)->format('d/m/Y') }}</td>
          <td>{{ $invoice->order?->client?->name ?: '-' }}</td>
          <td>{{ $invoice->order?->agency?->name ?: '-' }}</td>
          <td>{{ number_format($invoice->amount,0,',',' ') }} FCFA</td>
          <td>
            <a class="btn btn-sm btn-outline-primary" href="{{ route('employee.ui.invoices.show', $invoice) }}">Voir</a>
          </td>
        </tr>
      @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection
