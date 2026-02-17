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
@endsection
