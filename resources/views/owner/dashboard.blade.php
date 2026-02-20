@extends('layouts.app')
@section('title','Owner Dashboard')
@section('heading','Propriétaire')
@section('content')
<div class="alert alert-primary d-flex justify-content-between align-items-center">
  <div><strong>{{ $greeting }} {{ auth()->user()->name }}</strong></div>
  @if($closingAlert)<span class="small">{{ $closingAlert }}</span>@endif
</div>

<div class="card shadow-sm mb-4">
  <div class="card-body">
    <form method="GET" class="row g-2 align-items-end">
      <div class="col-md-4">
        <label class="form-label">Agence</label>
        <select class="form-select" name="agency_id" onchange="this.form.submit()">
          <option value="">Toutes les agences</option>
          @foreach($agencies as $agency)
            <option value="{{ $agency->id }}" @selected((string)$selectedAgencyId === (string)$agency->id)>{{ $agency->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2"><button class="btn btn-outline-secondary">Appliquer</button></div>
    </form>
  </div>
</div>

<div class="row g-3 mb-4">
  <div class="col-6 col-lg-3"><div class="card stat-card shadow-sm"><div class="card-body"><div class="text-muted">🏢 Agences</div><div class="h2 mb-0">{{ $agenciesCount }}</div></div></div></div>
  <div class="col-6 col-lg-3"><div class="card stat-card shadow-sm"><div class="card-body"><div class="text-muted">👥 Employés</div><div class="h2 mb-0">{{ $employeesCount }}</div></div></div></div>
  <div class="col-6 col-lg-3"><div class="card stat-card shadow-sm"><div class="card-body"><div class="text-muted">📦 Commandes reçues</div><div class="h2 mb-0">{{ $ordersCount }}</div></div></div></div>
  <div class="col-6 col-lg-3"><div class="card stat-card shadow-sm"><div class="card-body"><div class="text-muted">💰 Caisse aujourd'hui</div><div class="h2 mb-0">{{ number_format($todayCash,0,',',' ') }} FCFA</div></div></div></div>
</div>

<div class="row g-3">
  <div class="col-lg-6">
    <div class="card shadow-sm h-100">
      <div class="card-body d-flex flex-column gap-2">
        <h5 class="mb-0">4) Clôture de caisse journalière (par agence/employé)</h5>
        <div class="text-muted small">Activez ce module pour afficher le menu de clôture et gérer les clôtures de caisse.</div>
        <div class="mt-2 d-flex gap-2 align-items-center">
          <form method="POST" action="{{ route('owner.ui.modules.cash-closure.toggle') }}">
            @csrf
            <button class="btn btn-{{ $pressing?->module_cash_closure_enabled ? 'outline-danger' : 'primary' }}">{{ $pressing?->module_cash_closure_enabled ? 'Désactiver' : 'Activer' }}</button>
          </form>
          @if($pressing?->module_cash_closure_enabled)
            <a href="{{ route('owner.ui.cash-closures') }}" class="btn btn-outline-primary">Ouvrir la clôture</a>
          @endif
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-6">
    <div class="card shadow-sm h-100 border-warning-subtle">
      <div class="card-body d-flex flex-column gap-2">
        <h5 class="mb-0">1) Notifications client automatiques (SMS / WhatsApp)</h5>
        <div class="text-muted">Bientôt disponible.</div>
      </div>
    </div>
  </div>
</div>
@endsection
