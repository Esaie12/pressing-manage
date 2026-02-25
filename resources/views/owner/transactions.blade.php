@extends('layouts.app')
@section('title','Owner - Transactions')
@section('heading','Owner • Transactions')
@section('content')
<div class="card shadow-sm">
  <div class="card-header">Transactions reçues</div>
  <div class="table-responsive">
    @if(count($transactions) >0)
    <table class="table datatable mb-0 align-middle">
      <thead><tr><th>Date</th><th>Type</th><th>Montant</th><th>Agence</th><th>Référence</th><th>Auteur</th><th>Statut</th><th>Action</th></tr></thead>
      <tbody>
      @foreach($transactions as $tx)
        @php
          $window = (int) ($pressing->transaction_cancellation_window_minutes ?? 0);
          $deadline = optional($tx->happened_at ?? $tx->created_at)?->copy()?->addMinutes($window);
          $canCancel = $pressing->allow_transaction_cancellation && ! $tx->is_cancelled && $window > 0 && $deadline && now()->lessThanOrEqualTo($deadline);
        @endphp
        <tr>
          <td>{{ optional($tx->happened_at ?? $tx->created_at)->format('d/m/Y H:i') }}</td>
          <td>{!! $tx->type === 'encaissement' ? '<span class="badge bg-success">Encaissement</span>' : '<span class="badge bg-danger">Paiement</span>' !!}</td>
          <td>{{ number_format($tx->amount,0,',',' ') }} FCFA</td>
          <td>{{ $tx->agency?->name ?? '-' }}</td>
          <td>{{ $tx->order?->reference ?? $tx->expense?->title ?? ($tx->label ?? '-') }}</td>
          <td>{{ $tx->user?->name ?? '-' }}</td>
          <td>
            @if($tx->is_cancelled)
              <span class="badge bg-secondary">Annulée</span>
            @else
              <span class="badge bg-success">Active</span>
            @endif
          </td>
          <td>
            @if($canCancel)
              <form method="POST" action="{{ route('owner.ui.transactions.cancel', $tx) }}">@csrf<button class="btn btn-sm btn-outline-danger">Annuler</button></form>
            @elseif($tx->is_cancelled)
              <small class="text-muted">Annulée par {{ $tx->cancelledBy?->name ?? '-' }}</small>
            @else
              <small class="text-muted">Délai dépassé</small>
            @endif
          </td>
        </tr>
      @endforeach
      </tbody>
    </table>
     @else 
    <div class="text-center py-4 text-danger">
                Aucune transaction
              </div>
    @endif
  </div>
</div>
@endsection
