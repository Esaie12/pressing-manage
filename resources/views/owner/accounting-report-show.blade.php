@extends('layouts.app')
@section('title','Owner - Détail bilan')
@section('heading','Owner • Comptabilité • Détail bilan')
@section('content')
<div class="card shadow-sm mb-3">
  <div class="card-body">
    <div><strong>Mois:</strong> {{ $report->month?->format('m/Y') }}</div>
    <div><strong>Agence:</strong> {{ $report->agency?->name ?? 'Global' }}</div>
    <div><strong>Crédits:</strong> {{ number_format($report->total_credits,0,',',' ') }} FCFA</div>
    <div><strong>Débits:</strong> {{ number_format($report->total_debits,0,',',' ') }} FCFA</div>
    <div><strong>Résultat net:</strong> {{ number_format($report->net_result,0,',',' ') }} FCFA</div>
    <a class="btn btn-outline-secondary btn-sm mt-2" href="{{ route('owner.ui.accounting.reports') }}">Retour</a>
  </div>
</div>

<div class="card shadow-sm">
  <div class="card-header">Détails des entrées du bilan</div>
  <div class="table-responsive">
    <table class="table datatable mb-0 align-middle">
      <thead><tr><th>Date</th><th>Type</th><th>Montant</th><th>Méthode</th><th>Référence</th></tr></thead>
      <tbody>
      @forelse($report->entries as $entry)
        <tr>
          <td>{{ optional($entry->happened_at)->format('d/m/Y H:i') }}</td>
          <td>{{ $entry->entry_type }}</td>
          <td>{{ number_format($entry->amount,0,',',' ') }} FCFA</td>
          <td>{{ $entry->payment_method ?? '-' }}</td>
          <td>{{ $entry->order_reference ?? ($entry->label ?? '-') }}</td>
        </tr>
      @empty
        <tr><td colspan="5" class="text-center text-muted">Aucune entrée.</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
