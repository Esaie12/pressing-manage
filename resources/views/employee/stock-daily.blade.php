@extends('layouts.app')
@section('title','Employé - Bilan stock du jour')
@section('heading','Employé • Bilan stock du jour')
@section('content')
<div class="card shadow-sm mb-3">
  <div class="card-header">Déclarer une sortie de stock</div>
  <div class="card-body">
    <form method="POST" action="{{ route('employee.ui.stock.daily.outgoing.store') }}" class="row g-2 align-items-end">
      @csrf
      <div class="col-md-4">
        <label class="form-label">Article</label>
        <select class="form-select" name="stock_item_id" required>
          <option value="">Choisir...</option>
          @foreach($items as $item)
            <option value="{{ $item->id }}">{{ $item->name }} ({{ $item->unit }})</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2"><label class="form-label">Qté</label><input class="form-control" type="number" name="quantity" step="0.01" min="0.01" required></div>
      <div class="col-md-3"><label class="form-label">Date</label><input type="date" class="form-control" name="movement_date" value="{{ $movementDate }}" required></div>
      <div class="col-md-3"><label class="form-label">Note</label><input class="form-control" name="note"></div>
      <div class="col-12"><button class="btn btn-primary">Enregistrer ma sortie</button></div>
    </form>
  </div>
</div>

<div class="card shadow-sm mb-3">
  <div class="card-body">
    <form method="GET" class="row g-2 align-items-end">
      <div class="col-md-4">
        <label class="form-label">Date</label>
        <input type="date" class="form-control" name="movement_date" value="{{ $movementDate }}">
      </div>
      <div class="col-md-3"><button class="btn btn-outline-primary">Afficher</button></div>
      <div class="col-md-5 text-md-end"><span class="badge text-bg-secondary">Total sorties: {{ number_format($totalOutgoing,2,',',' ') }}</span></div>
    </form>
  </div>
</div>

<div class="card shadow-sm mb-3">
  <div class="card-header">Stock actuel dans mon agence</div>
  <div class="table-responsive">
    <table class="table mb-0">
      <thead><tr><th>Article</th><th class="text-end">Qté disponible</th></tr></thead>
      <tbody>
      @forelse($agencyBalances as $balance)
        <tr>
          <td>{{ $balance->item?->name }}</td>
          <td class="text-end">{{ number_format((float)$balance->quantity,2,',',' ') }}</td>
        </tr>
      @empty
        <tr><td colspan="2" class="text-center text-muted py-3">Aucun stock disponible dans votre agence.</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
</div>

<div class="card shadow-sm">
  <div class="card-header">Sorties effectuées par moi</div>
  <div class="table-responsive">
    <table class="table mb-0">
      <thead><tr><th>Heure</th><th>Article</th><th>Détail</th><th class="text-end">Qté</th></tr></thead>
      <tbody>
      @forelse($outgoing as $row)
        <tr>
          <td>{{ optional($row->created_at)->format('H:i') }}</td>
          <td>{{ $row->item?->name }}</td>
          <td>{{ $row->agency?->name ?? 'Magasin central' }} @if($row->note)<div class="small text-muted">{{ $row->note }}</div>@endif</td>
          <td class="text-end">{{ number_format((float)$row->quantity,2,',',' ') }}</td>
        </tr>
      @empty
        <tr><td colspan="4" class="text-center text-muted py-3">Aucune sortie enregistrée pour cette date.</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
