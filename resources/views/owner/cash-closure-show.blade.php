@extends('layouts.app')
@section('title','Owner - Détails clôture')
@section('heading','Owner • Détails clôture')
@section('content')
<div class="card shadow-sm mb-3">
  <div class="card-body">
    <div><strong>Date:</strong> {{ $closure->closure_date?->format('d/m/Y') }}</div>
    <div><strong>Agence:</strong> {{ $closure->agency?->name ?? 'Toutes' }}</div>
    <div><strong>Employé:</strong> {{ $closure->employee?->name ?? 'Tous' }}</div>
    <div><strong>Clôturé par:</strong> {{ $closure->closedBy?->name ?? '-' }} ({{ $closure->closed_at?->format('H:i') }})</div>
    <a href="{{ route('owner.ui.cash-closures') }}" class="btn btn-outline-secondary btn-sm mt-2">Retour</a>
  </div>
</div>

<div class="card shadow-sm">
  <div class="card-header">Détails des entrées de clôture</div>
  <div class="table-responsive">
    <table class="table datatable mb-0 align-middle">
      <thead><tr><th>Date</th><th>Type</th><th>Montant</th><th>Méthode</th><th>Référence</th><th>Auteur</th></tr></thead>
      <tbody>
      @forelse($closure->entries as $entry)
        <tr>
          <td>{{ optional($entry->happened_at)->format('d/m/Y H:i') }}</td>
          <td>{{ $entry->transaction_type }}</td>
          <td>{{ number_format($entry->amount,0,',',' ') }} FCFA</td>
          <td>{{ $entry->payment_method ?? '-' }}</td>
          <td>{{ $entry->order_reference ?? ($entry->label ?? '-') }}</td>
          <td>{{ $entry->user?->name ?? '-' }}</td>
        </tr>
      @empty
        <tr><td colspan="6" class="text-center text-muted">Aucune entrée</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
