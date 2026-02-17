@extends('layouts.app')
@section('title','Owner - Statistiques')
@section('heading','Owner • Statistiques')
@section('content')
<div class="card shadow-sm mb-3">
  <div class="card-body">
    <form method="GET" action="{{ route('owner.ui.stats') }}" class="row g-2 align-items-end">
      <div class="col-md-3"><label class="form-label">Agence</label><select class="form-select" name="agency_id"><option value="">Toutes</option>@foreach($agencies as $agency)<option value="{{ $agency->id }}" @selected((string)$selectedAgencyId === (string)$agency->id)>{{ $agency->name }}</option>@endforeach</select></div>
      <div class="col-md-3"><label class="form-label">Du</label><input type="date" class="form-control" name="from" value="{{ $from }}"></div>
      <div class="col-md-3"><label class="form-label">Au</label><input type="date" class="form-control" name="to" value="{{ $to }}"></div>
      <div class="col-md-3 d-flex gap-2"><button class="btn btn-primary mt-4" type="submit">Filtrer</button><a class="btn btn-outline-secondary mt-4" href="{{ route('owner.ui.stats') }}">Réinitialiser</a></div>
    </form>
  </div>
</div>

<div class="row g-3 mb-3">
  <div class="col-md-3"><div class="card"><div class="card-body"><div class="text-muted">Total commandes</div><div class="h2">{{ $totalOrders }}</div></div></div></div>
  <div class="col-md-3"><div class="card"><div class="card-body"><div class="text-muted">CA total</div><div class="h2">{{ number_format($totalRevenue,0,',',' ') }}</div></div></div></div>
  <div class="col-md-3"><div class="card"><div class="card-body"><div class="text-muted">Payées d'avance</div><div class="h2">{{ $advancePaidCount }}</div></div></div></div>
  <div class="col-md-3"><div class="card"><div class="card-body"><div class="text-muted">Dépenses</div><div class="h2">{{ number_format($totalExpenses,0,',',' ') }}</div></div></div></div>
</div>

<div class="row g-3">
  <div class="col-lg-6">
    <div class="card shadow-sm">
      <div class="card-header bg-white"><strong>Évolution du CA cette semaine</strong></div>
      <div class="card-body"><canvas id="weekRevenueChart" height="120"></canvas></div>
    </div>
  </div>
  <div class="col-lg-6">
    <div class="card shadow-sm">
      <div class="card-header bg-white"><strong>CA sur les 4 derniers mois</strong></div>
      <div class="card-body"><canvas id="monthRevenueChart" height="120"></canvas></div>
    </div>
  </div>
  <div class="col-lg-6">
    <div class="card shadow-sm">
      <div class="card-header bg-white"><strong>Comparatif CA vs Dépenses (4 mois)</strong></div>
      <div class="card-body"><canvas id="revVsExpenseChart" height="120"></canvas></div>
    </div>
  </div>
  <div class="col-lg-6">
    <div class="card shadow-sm">
      <div class="card-header bg-white"><strong>Répartition des commandes</strong></div>
      <div class="card-body"><canvas id="statusChart" height="120"></canvas></div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
  const weekLabels = @json($weekLabels);
  const weekRevenue = @json($weekRevenue);
  const monthLabels = @json($monthLabels);
  const monthRevenue = @json($monthRevenue);
  const monthExpenses = @json($monthExpenses);
  const statusDistribution = @json($statusDistribution);

  new Chart(document.getElementById('weekRevenueChart'), {
    type: 'line',
    data: { labels: weekLabels, datasets: [{ label: 'CA (Semaine)', data: weekRevenue, borderColor: '#0d6efd', backgroundColor: 'rgba(13,110,253,.2)', fill: true, tension: 0.3 }]},
    options: { responsive: true, maintainAspectRatio: false }
  });

  new Chart(document.getElementById('monthRevenueChart'), {
    type: 'bar',
    data: { labels: monthLabels, datasets: [{ label: 'CA mensuel', data: monthRevenue, backgroundColor: '#20c997' }]},
    options: { responsive: true, maintainAspectRatio: false }
  });

  new Chart(document.getElementById('revVsExpenseChart'), {
    type: 'bar',
    data: {
      labels: monthLabels,
      datasets: [
        { label: 'CA', data: monthRevenue, backgroundColor: '#198754' },
        { label: 'Dépenses', data: monthExpenses, backgroundColor: '#dc3545' },
      ]
    },
    options: { responsive: true, maintainAspectRatio: false }
  });

  new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
      labels: ['Créées', 'Prêtes', 'Retirées'],
      datasets: [{
        data: [statusDistribution.created, statusDistribution.ready, statusDistribution.picked_up],
        backgroundColor: ['#0d6efd', '#ffc107', '#198754']
      }]
    },
    options: { responsive: true, maintainAspectRatio: false }
  });
</script>
@endsection
