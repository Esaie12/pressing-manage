@extends('layouts.app')
@section('title','Owner Dashboard')
@section('heading','Propriétaire')
@section('content')
<div class="alert alert-primary d-flex justify-content-between align-items-center">
  <div><strong>{{ $greeting }} {{ auth()->user()->name }}</strong></div>
  @if($closingAlert)<span class="small">{{ $closingAlert }}</span>@endif
</div>
<div class="row g-3 mb-4">
  <div class="col-6 col-lg-3"><div class="card stat-card shadow-sm"><div class="card-body"><div class="text-muted">Agences</div><div class="h2 mb-0">{{ $agenciesCount }}</div></div></div></div>
  <div class="col-6 col-lg-3"><div class="card stat-card shadow-sm"><div class="card-body"><div class="text-muted">Employés</div><div class="h2 mb-0">{{ $employeesCount }}</div></div></div></div>
  <div class="col-6 col-lg-3"><div class="card stat-card shadow-sm"><div class="card-body"><div class="text-muted">Commandes</div><div class="h2 mb-0">{{ $ordersCount }}</div></div></div></div>
  <div class="col-6 col-lg-3"><div class="card stat-card shadow-sm"><div class="card-body"><div class="text-muted">CA total</div><div class="h2 mb-0">{{ number_format($revenue,0,',',' ') }}</div></div></div></div>
</div>

<div class="row g-3">
  <div class="col-md-6 col-xl-4">
    <div class="card h-100 shadow-sm border-0">
      <div class="card-body d-flex align-items-start justify-content-between">
        <div>
          <h5 class="mb-1">🏢 Agences</h5>
          <p class="text-muted mb-0">Gestion de vos agences et états d'activation.</p>
        </div>
        <a class="btn btn-outline-primary btn-sm" href="{{ route('owner.ui.agencies') }}">Voir tout</a>
      </div>
    </div>
  </div>
  <div class="col-md-6 col-xl-4">
    <div class="card h-100 shadow-sm border-0">
      <div class="card-body d-flex align-items-start justify-content-between">
        <div>
          <h5 class="mb-1">👥 Employés</h5>
          <p class="text-muted mb-0">Ajouter, activer et suivre vos équipes.</p>
        </div>
        <a class="btn btn-outline-primary btn-sm" href="{{ route('owner.ui.employees') }}">Voir tout</a>
      </div>
    </div>
  </div>
  <div class="col-md-6 col-xl-4">
    <div class="card h-100 shadow-sm border-0">
      <div class="card-body d-flex align-items-start justify-content-between">
        <div>
          <h5 class="mb-1">🧺 Commandes</h5>
          <p class="text-muted mb-0">Prise de commande, suivi et édition rapide.</p>
        </div>
        <a class="btn btn-outline-primary btn-sm" href="{{ route('owner.ui.orders') }}">Voir tout</a>
      </div>
    </div>
  </div>
  <div class="col-md-6 col-xl-4">
    <div class="card h-100 shadow-sm border-0">
      <div class="card-body d-flex align-items-start justify-content-between">
        <div>
          <h5 class="mb-1">📊 Statistiques</h5>
          <p class="text-muted mb-0">Visualiser CA, volumes et tendances.</p>
        </div>
        <a class="btn btn-outline-primary btn-sm" href="{{ route('owner.ui.stats') }}">Voir tout</a>
      </div>
    </div>
  </div>
  <div class="col-md-6 col-xl-4">
    <div class="card h-100 shadow-sm border-0">
      <div class="card-body d-flex align-items-start justify-content-between">
        <div>
          <h5 class="mb-1">💸 Dépenses</h5>
          <p class="text-muted mb-0">Suivi des charges, édition et suppression soft.</p>
        </div>
        <a class="btn btn-outline-primary btn-sm" href="{{ route('owner.ui.expenses') }}">Voir tout</a>
      </div>
    </div>
  </div>
  <div class="col-md-6 col-xl-4">
    <div class="card h-100 shadow-sm border-0">
      <div class="card-body d-flex align-items-start justify-content-between">
        <div>
          <h5 class="mb-1">⚙️ Paramètres</h5>
          <p class="text-muted mb-0">Personnaliser votre pressing et les factures.</p>
        </div>
        <a class="btn btn-outline-primary btn-sm" href="{{ route('owner.ui.settings') }}">Voir tout</a>
      </div>
    </div>
  </div>
</div>


@endsection
