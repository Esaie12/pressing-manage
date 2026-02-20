@extends('layouts.app')
@section('title','Owner - Comptabilité Paramètres')
@section('heading','Owner • Comptabilité • Paramètres')
@section('content')
<div class="card shadow-sm mb-3">
  <div class="card-body">
    <form method="GET" class="row g-2 align-items-end">
      <div class="col-md-4">
        <label class="form-label">Agence</label>
        <select class="form-select" name="agency_id" onchange="this.form.submit()">
          <option value="">Global (tout le pressing)</option>
          @foreach($agencies as $agency)
            <option value="{{ $agency->id }}" @selected((string)$selectedAgencyId === (string)$agency->id)>{{ $agency->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2"><button class="btn btn-outline-secondary">Appliquer</button></div>
    </form>
  </div>
</div>

<div class="card shadow-sm">
  <div class="card-header">Paramètres comptables (Capital et autres)</div>
  <div class="card-body">
    <form method="POST" action="{{ route('owner.ui.accounting.settings.save') }}" class="row g-2">
      @csrf
      <input type="hidden" name="agency_id" value="{{ $selectedAgencyId }}">
      <div class="col-md-4"><label class="form-label">Capital</label><input class="form-control" type="number" step="0.01" min="0" name="capital" value="{{ old('capital', $setup?->capital ?? 0) }}"></div>
      <div class="col-md-4"><label class="form-label">Réserves</label><input class="form-control" type="number" step="0.01" min="0" name="reserves" value="{{ old('reserves', $setup?->reserves ?? 0) }}"></div>
      <div class="col-md-4"><label class="form-label">Report à nouveau</label><input class="form-control" type="number" step="0.01" name="retained_earnings" value="{{ old('retained_earnings', $setup?->retained_earnings ?? 0) }}"></div>

      <div class="col-md-3"><label class="form-label">Immobilisations incorporelles</label><input class="form-control" type="number" step="0.01" min="0" name="intangible_assets" value="{{ old('intangible_assets', $setup?->intangible_assets ?? 0) }}"></div>
      <div class="col-md-3"><label class="form-label">Immobilisations corporelles</label><input class="form-control" type="number" step="0.01" min="0" name="tangible_assets" value="{{ old('tangible_assets', $setup?->tangible_assets ?? 0) }}"></div>
      <div class="col-md-3"><label class="form-label">Immobilisations financières</label><input class="form-control" type="number" step="0.01" min="0" name="financial_assets" value="{{ old('financial_assets', $setup?->financial_assets ?? 0) }}"></div>
      <div class="col-md-3"><label class="form-label">Stocks</label><input class="form-control" type="number" step="0.01" min="0" name="stocks" value="{{ old('stocks', $setup?->stocks ?? 0) }}"></div>

      <div class="col-md-4"><label class="form-label">Créances</label><input class="form-control" type="number" step="0.01" min="0" name="receivables" value="{{ old('receivables', $setup?->receivables ?? 0) }}"></div>
      <div class="col-md-4"><label class="form-label">Disponibilités</label><input class="form-control" type="number" step="0.01" min="0" name="treasury" value="{{ old('treasury', $setup?->treasury ?? 0) }}"></div>
      <div class="col-md-4"><label class="form-label">Dettes financières</label><input class="form-control" type="number" step="0.01" min="0" name="financial_debts" value="{{ old('financial_debts', $setup?->financial_debts ?? 0) }}"></div>

      <div class="col-md-4"><label class="form-label">Dettes d'exploitation</label><input class="form-control" type="number" step="0.01" min="0" name="operating_debts" value="{{ old('operating_debts', $setup?->operating_debts ?? 0) }}"></div>
      <div class="col-md-4"><label class="form-label">Dettes sur immobilisations</label><input class="form-control" type="number" step="0.01" min="0" name="fixed_asset_debts" value="{{ old('fixed_asset_debts', $setup?->fixed_asset_debts ?? 0) }}"></div>
      <div class="col-md-4"><label class="form-label">Autres dettes</label><input class="form-control" type="number" step="0.01" min="0" name="other_debts" value="{{ old('other_debts', $setup?->other_debts ?? 0) }}"></div>

      <div class="col-12"><label class="form-label">Notes</label><textarea class="form-control" name="notes" rows="3">{{ old('notes', $setup?->notes) }}</textarea></div>
      <div class="col-12"><button class="btn btn-primary">Enregistrer paramètres</button></div>
    </form>
  </div>
</div>
@endsection
