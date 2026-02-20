@extends('layouts.app')
@section('title','Owner - Comptabilité Bilan')
@section('heading','Owner • Comptabilité • Bilan')
@section('content')
<div class="card shadow-sm mb-3">
  <div class="card-body">
    <form method="GET" class="row g-2 align-items-end">
      <div class="col-md-3"><label class="form-label">Mois</label><input type="month" class="form-control" name="month" value="{{ \Illuminate\Support\Carbon::parse($month)->format('Y-m') }}"></div>
      <div class="col-md-4"><label class="form-label">Agence</label><select class="form-select" name="agency_id"><option value="">Global (tout le pressing)</option>@foreach($agencies as $agency)<option value="{{ $agency->id }}" @selected((string)$agencyId === (string)$agency->id)>{{ $agency->name }}</option>@endforeach</select></div>
      <div class="col-md-2"><button class="btn btn-outline-secondary">Voir bilan</button></div>
    </form>
  </div>
</div>

<div class="row g-3 mb-3">
  <div class="col-md-4"><div class="card shadow-sm"><div class="card-body"><div class="text-muted">Crédits</div><div class="h4 text-success">{{ number_format($preview['total_credits'],0,',',' ') }} FCFA</div></div></div></div>
  <div class="col-md-4"><div class="card shadow-sm"><div class="card-body"><div class="text-muted">Débits</div><div class="h4 text-danger">{{ number_format($preview['total_debits'],0,',',' ') }} FCFA</div></div></div></div>
  <div class="col-md-4"><div class="card shadow-sm"><div class="card-body"><div class="text-muted">Résultat net</div><div class="h4 {{ $preview['net_result'] >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($preview['net_result'],0,',',' ') }} FCFA</div></div></div></div>
</div>

<div class="card shadow-sm mb-3">
  <div class="card-body">
    <form method="POST" action="{{ route('owner.ui.accounting.reports.save') }}" class="row g-2 align-items-end">
      @csrf
      <input type="hidden" name="month" value="{{ \Illuminate\Support\Carbon::parse($month)->startOfMonth()->toDateString() }}">
      <input type="hidden" name="agency_id" value="{{ $agencyId }}">
      <div class="col-md-10"><label class="form-label">Note de sauvegarde (optionnel)</label><input class="form-control" name="note" placeholder="Ex: bilan validé avec expert-comptable"></div>
      <div class="col-md-2 d-grid"><button class="btn btn-primary">Sauvegarder le bilan</button></div>
    </form>
  </div>
</div>

<div class="card shadow-sm">
  <div class="card-header">Bilans sauvegardés</div>
  <div class="table-responsive">
    <table class="table datatable mb-0 align-middle">
      <thead><tr><th>Date sauvegarde</th><th>Mois</th><th>Agence</th><th>Crédits</th><th>Débits</th><th>Résultat net</th><th>Action</th></tr></thead>
      <tbody>
      @forelse($savedReports as $report)
        <tr>
          <td>{{ $report->saved_at?->format('d/m/Y H:i') }}</td>
          <td>{{ $report->month?->format('m/Y') }}</td>
          <td>{{ $report->agency?->name ?? 'Global' }}</td>
          <td>{{ number_format($report->total_credits,0,',',' ') }} FCFA</td>
          <td>{{ number_format($report->total_debits,0,',',' ') }} FCFA</td>
          <td>{{ number_format($report->net_result,0,',',' ') }} FCFA</td>
          <td><a class="btn btn-sm btn-outline-primary" href="{{ route('owner.ui.accounting.reports.show', $report) }}">Détails</a></td>
        </tr>
      @empty
        <tr><td colspan="7" class="text-center text-muted">Aucun bilan sauvegardé.</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
