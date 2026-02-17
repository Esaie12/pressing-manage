@extends('layouts.app')
@section('title','Admin Dashboard')
@section('heading','Administration')
@section('content')
<div class="row g-3 mb-4">
  <div class="col-6 col-lg-3"><div class="card stat-card shadow-sm"><div class="card-body"><div class="text-muted">Propriétaires</div><div class="display-6 fw-bold">{{ $ownersCount }}</div></div></div></div>
  <div class="col-6 col-lg-3"><div class="card stat-card shadow-sm"><div class="card-body"><div class="text-muted">Agences</div><div class="display-6 fw-bold">{{ $agenciesCount }}</div></div></div></div>
  <div class="col-6 col-lg-3"><div class="card stat-card shadow-sm"><div class="card-body"><div class="text-muted">Abonnements actifs</div><div class="display-6 fw-bold text-success">{{ $activeSubscriptions }}</div></div></div></div>
  <div class="col-6 col-lg-3"><div class="card stat-card shadow-sm"><div class="card-body"><div class="text-muted">Plans</div><div class="display-6 fw-bold text-primary">{{ $plansCount }}</div></div></div></div>
</div>
<div class="list-group">
  <a class="list-group-item list-group-item-action" href="{{ route('admin.ui.owners') }}">Créer/Lister les propriétaires</a>
  <a class="list-group-item list-group-item-action" href="{{ route('admin.ui.agencies') }}">Lister les agences</a>
  <a class="list-group-item list-group-item-action" href="{{ route('admin.ui.subscriptions') }}">Créer/Lister les abonnements</a>
  <a class="list-group-item list-group-item-action" href="{{ route('admin.ui.pricing') }}">Voir le pricing</a>
</div>
@endsection
