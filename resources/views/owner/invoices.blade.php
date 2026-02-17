@extends('layouts.app')
@section('title','Owner - Factures')
@section('heading','Owner • Factures')
@section('content')
<div class="card">
  <div class="card-header">Liste des factures</div>
  <div class="table-responsive"><table class="table mb-0 datatable"><thead><tr><th>N°</th><th>Client</th><th>Agence</th><th>Montant</th><th>Date</th><th>Action</th></tr></thead><tbody>@forelse($invoices as $invoice)<tr><td>{{ $invoice->invoice_number }}</td><td>{{ $invoice->order?->client?->name ?? '-' }}</td><td>{{ $invoice->order?->agency?->name ?? '-' }}</td><td>{{ number_format($invoice->amount,0,',',' ') }} FCFA</td><td>{{ optional($invoice->issued_at)->format('d/m/Y') }}</td><td><a class="btn btn-sm btn-outline-primary" href="{{ route('owner.ui.invoices.show', $invoice) }}">Voir</a></td></tr>@empty<tr><td colspan="6" class="text-center text-muted">Aucune facture</td></tr>@endforelse</tbody></table></div>
</div>
@endsection
