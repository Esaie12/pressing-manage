@extends('layouts.app')
@section('title','Employé Dashboard')
@section('heading','Employé')
@section('content')
<div class="alert alert-primary d-flex justify-content-between align-items-center">
  <div><strong>{{ $greeting }} {{ auth()->user()->name }}</strong></div>
  @if($closingAlert)<span class="small">{{ $closingAlert }}</span>@endif
</div>

<div class="card shadow-sm mb-3">
  <div class="card-body">
    <form class="row g-2 align-items-end" method="GET" action="{{ route('employee.ui.dashboard') }}">
      <div class="col-md-4"><label class="form-label">Du</label><input type="date" class="form-control" name="from" value="{{ $from }}"></div>
      <div class="col-md-4"><label class="form-label">Au</label><input type="date" class="form-control" name="to" value="{{ $to }}"></div>
      <div class="col-md-4 d-flex gap-2"><button class="btn btn-primary mt-4">Filtrer</button><a class="btn btn-outline-secondary mt-4" href="{{ route('employee.ui.dashboard') }}">Aujourd'hui</a></div>
    </form>
  </div>
</div>

<div class="row g-3 mb-3">
  <div class="col-md-3"><div class="card"><div class="card-body"><div class="text-muted">En cours</div><div class="display-6">{{ $inProgress }}</div></div></div></div>
  <div class="col-md-3"><div class="card"><div class="card-body"><div class="text-muted">Retirées</div><div class="display-6 text-success">{{ $pickedUp }}</div></div></div></div>
  <div class="col-md-3"><div class="card"><div class="card-body"><div class="text-muted">CA aujourd'hui</div><div class="display-6 text-primary">{{ number_format($todayRevenue,0,',',' ') }}</div></div></div></div>
  <div class="col-md-3"><div class="card"><div class="card-body"><div class="text-muted">CA période</div><div class="display-6">{{ number_format($periodRevenue,0,',',' ') }}</div><div class="small text-muted">{{ $periodOrders }} commandes</div></div></div></div>
</div>

<div class="row g-3 mb-3">
  <div class="col-lg-6"><div class="card shadow-sm"><div class="card-header bg-white">Colis réalisés (7 derniers jours)</div><div class="card-body"><canvas id="empCountChart" height="120"></canvas></div></div></div>
  <div class="col-lg-6"><div class="card shadow-sm"><div class="card-header bg-white">Chiffre d'affaire (7 derniers jours)</div><div class="card-body"><canvas id="empRevenueChart" height="120"></canvas></div></div></div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
  const labels = @json($last7Labels);
  const counts = @json($last7Count);
  const revenue = @json($last7Revenue);

  new Chart(document.getElementById('empCountChart'), {
    type: 'bar',
    data: { labels, datasets: [{ label: 'Nb colis', data: counts, backgroundColor: '#0d6efd' }] },
    options: { responsive: true, maintainAspectRatio: false }
  });

  new Chart(document.getElementById('empRevenueChart'), {
    type: 'line',
    data: { labels, datasets: [{ label: 'CA', data: revenue, borderColor: '#198754', backgroundColor: 'rgba(25,135,84,.15)', fill: true, tension: 0.3 }] },
    options: { responsive: true, maintainAspectRatio: false }
  });
</script>
@endsection
