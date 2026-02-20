@extends('layouts.app')
@section('title','Admin - Pricing')
@section('heading','Admin • Pricing')
@section('content')
<div class="row g-4">
  <div class="col-lg-4">
    <div class="card shadow-sm">
      <div class="card-header bg-white">Créer un pack</div>
      <div class="card-body">
        <form method="POST" action="{{ route('admin.ui.pricing.store') }}" class="vstack gap-2">
          @csrf
          <input class="form-control" name="name" placeholder="Nom du pack" required>
          <input class="form-control" type="number" min="0" step="0.01" name="monthly_price" placeholder="Prix mensuel" required>
          <input class="form-control" type="number" min="0" step="0.01" name="annual_price" placeholder="Prix annuel" required>
          <input class="form-control" type="number" min="1" name="max_agencies" placeholder="Agences max" required>
          <input class="form-control" type="number" min="1" name="max_employees" placeholder="Employés max" required>
          <div class="form-check"><input class="form-check-input" type="checkbox" name="allow_customization" value="1" id="create_allow_customization" checked><label class="form-check-label" for="create_allow_customization">Autoriser personnalisation</label></div>
          <div class="form-check"><input class="form-check-input" type="checkbox" name="allow_cash_closure_module" value="1" id="create_allow_cash" checked><label class="form-check-label" for="create_allow_cash">Autoriser module clôture de caisse</label></div>
          <div class="form-check"><input class="form-check-input" type="checkbox" name="allow_accounting_module" value="1" id="create_allow_accounting" checked><label class="form-check-label" for="create_allow_accounting">Autoriser module comptabilité</label></div>
          <div class="form-check"><input class="form-check-input" type="checkbox" name="allow_stock_module" value="1" id="create_allow_stock" checked><label class="form-check-label" for="create_allow_stock">Autoriser module stock</label></div>
          <button class="btn btn-primary">Créer pack</button>
        </form>
      </div>
    </div>
  </div>
  <div class="col-lg-8">
    <div class="row g-3">
      @foreach($plans as $plan)
      <div class="col-12">
        <div class="card shadow-sm">
          <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <strong>{{ $plan->name }}</strong>
            <span class="text-muted small">Pack #{{ $plan->id }}</span>
          </div>
          <div class="card-body">
            <form method="POST" action="{{ route('admin.ui.pricing.update', $plan) }}" class="row g-2 align-items-end">
              @csrf
              <div class="col-md-4"><label class="form-label">Nom</label><input class="form-control" name="name" value="{{ $plan->name }}" required></div>
              <div class="col-md-4"><label class="form-label">Mensuel</label><input class="form-control" type="number" min="0" step="0.01" name="monthly_price" value="{{ $plan->monthly_price }}" required></div>
              <div class="col-md-4"><label class="form-label">Annuel</label><input class="form-control" type="number" min="0" step="0.01" name="annual_price" value="{{ $plan->annual_price }}" required></div>
              <div class="col-md-4"><label class="form-label">Agences max</label><input class="form-control" type="number" min="1" name="max_agencies" value="{{ $plan->max_agencies }}" required></div>
              <div class="col-md-4"><label class="form-label">Employés max</label><input class="form-control" type="number" min="1" name="max_employees" value="{{ $plan->max_employees }}" required></div>
              <div class="col-md-12">
                <div class="d-flex flex-wrap gap-3">
                  <div class="form-check"><input class="form-check-input" type="checkbox" name="allow_customization" value="1" id="allow_customization_{{ $plan->id }}" @checked($plan->allow_customization)><label class="form-check-label" for="allow_customization_{{ $plan->id }}">Personnalisation</label></div>
                  <div class="form-check"><input class="form-check-input" type="checkbox" name="allow_cash_closure_module" value="1" id="allow_cash_{{ $plan->id }}" @checked($plan->allow_cash_closure_module)><label class="form-check-label" for="allow_cash_{{ $plan->id }}">Clôture caisse</label></div>
                  <div class="form-check"><input class="form-check-input" type="checkbox" name="allow_accounting_module" value="1" id="allow_accounting_{{ $plan->id }}" @checked($plan->allow_accounting_module)><label class="form-check-label" for="allow_accounting_{{ $plan->id }}">Comptabilité</label></div>
                  <div class="form-check"><input class="form-check-input" type="checkbox" name="allow_stock_module" value="1" id="allow_stock_{{ $plan->id }}" @checked($plan->allow_stock_module)><label class="form-check-label" for="allow_stock_{{ $plan->id }}">Stock</label></div>
                </div>
              </div>
              <div class="col-md-4 d-grid"><button class="btn btn-outline-primary">Mettre à jour</button></div>
            </form>
          </div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</div>
@endsection


<div class="row g-4 mt-1">
  <div class="col-lg-5">
    <div class="card shadow-sm">
      <div class="card-header bg-white">Tarification pack personnalisé</div>
      <div class="card-body">
        <form method="POST" action="{{ route('admin.ui.pricing.custom-pack.save') }}" class="row g-2">
          @csrf
          <div class="col-md-6"><label class="form-label">Prix de base</label><input class="form-control" type="number" min="0" step="0.01" name="base_price" value="{{ $customPricing->base_price ?? 0 }}" required></div>
          <div class="col-md-6"><label class="form-label">Personnalisation</label><input class="form-control" type="number" min="0" step="0.01" name="price_customization" value="{{ $customPricing->price_customization ?? 0 }}" required></div>
          <div class="col-md-4"><label class="form-label">Module stock</label><input class="form-control" type="number" min="0" step="0.01" name="price_module_stock" value="{{ $customPricing->price_module_stock ?? 0 }}" required></div>
          <div class="col-md-4"><label class="form-label">Module compta</label><input class="form-control" type="number" min="0" step="0.01" name="price_module_accounting" value="{{ $customPricing->price_module_accounting ?? 0 }}" required></div>
          <div class="col-md-4"><label class="form-label">Module clôture</label><input class="form-control" type="number" min="0" step="0.01" name="price_module_cash_closure" value="{{ $customPricing->price_module_cash_closure ?? 0 }}" required></div>
          <div class="col-12"><div class="small fw-semibold">Tranches Agences</div></div>
          <div class="col-md-4"><label class="form-label">1 à 4</label><input class="form-control" type="number" min="0" step="0.01" name="price_agencies_1_4" value="{{ $customPricing->price_agencies_1_4 ?? 0 }}" required></div>
          <div class="col-md-4"><label class="form-label">5 à 10</label><input class="form-control" type="number" min="0" step="0.01" name="price_agencies_5_10" value="{{ $customPricing->price_agencies_5_10 ?? 0 }}" required></div>
          <div class="col-md-4"><label class="form-label">11+</label><input class="form-control" type="number" min="0" step="0.01" name="price_agencies_11_plus" value="{{ $customPricing->price_agencies_11_plus ?? 0 }}" required></div>
          <div class="col-12"><div class="small fw-semibold">Tranches Employés</div></div>
          <div class="col-md-4"><label class="form-label">1 à 5</label><input class="form-control" type="number" min="0" step="0.01" name="price_employees_1_5" value="{{ $customPricing->price_employees_1_5 ?? 0 }}" required></div>
          <div class="col-md-4"><label class="form-label">6 à 20</label><input class="form-control" type="number" min="0" step="0.01" name="price_employees_6_20" value="{{ $customPricing->price_employees_6_20 ?? 0 }}" required></div>
          <div class="col-md-4"><label class="form-label">21+</label><input class="form-control" type="number" min="0" step="0.01" name="price_employees_21_plus" value="{{ $customPricing->price_employees_21_plus ?? 0 }}" required></div>
          <div class="col-12"><button class="btn btn-primary">Enregistrer tarification</button></div>
        </form>
      </div>
    </div>
  </div>
  <div class="col-lg-7">
    <div class="card shadow-sm">
      <div class="card-header bg-white">Demandes de packs personnalisés</div>
      <div class="table-responsive">
        <table class="table mb-0">
          <thead><tr><th>Pressing</th><th>Besoins</th><th>Prix estimé</th><th>Statut</th></tr></thead>
          <tbody>
          @forelse($customRequests as $req)
            <tr>
              <td>{{ $req->pressing?->name ?? '-' }}</td>
              <td class="small">Agences: {{ $req->requested_agencies }} · Employés: {{ $req->requested_employees }}<br>Modules: {{ $req->want_stock_module ? 'Stock ' : '' }}{{ $req->want_accounting_module ? 'Compta ' : '' }}{{ $req->want_cash_closure_module ? 'Clôture ' : '' }}{{ $req->want_customization ? 'Personnalisation' : '' }}</td>
              <td>{{ number_format((float)$req->estimated_price,0,',',' ') }} FCFA</td>
              <td><span class="badge text-bg-secondary">{{ ucfirst($req->status) }}</span></td>
            </tr>
          @empty
            <tr><td colspan="4" class="text-center text-muted py-3">Aucune demande.</td></tr>
          @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
