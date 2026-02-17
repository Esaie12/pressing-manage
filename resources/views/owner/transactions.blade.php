@extends('layouts.app')
@section('title','Owner - Transactions')
@section('heading','Owner • Transactions')
@section('content')
<div class="card shadow-sm">
  <div class="card-header">Transactions reçues</div>
  <div class="table-responsive">
    <table class="table datatable mb-0 align-middle">
      <thead><tr><th>Date</th><th>Type</th><th>Montant</th><th>Agence</th><th>Référence</th><th>Auteur</th></tr></thead>
      <tbody>
      @forelse($transactions as $tx)
        <tr>
          <td>{{ optional($tx->happened_at ?? $tx->created_at)->format('d/m/Y H:i') }}</td>
          <td>{!! $tx->type === 'encaissement' ? '<span class="badge bg-success">Encaissement</span>' : '<span class="badge bg-danger">Paiement</span>' !!}</td>
          <td>{{ number_format($tx->amount,0,',',' ') }} FCFA</td>
          <td>{{ $tx->agency?->name ?? '-' }}</td>
          <td>{{ $tx->order?->reference ?? $tx->expense?->title ?? ($tx->label ?? '-') }}</td>
          <td>{{ $tx->user?->name ?? '-' }}</td>
        </tr>
      @empty
        <tr><td colspan="6" class="text-center text-muted">Aucune transaction</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
