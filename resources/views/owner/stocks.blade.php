@extends('layouts.app')
@section('title','Owner - Stock')
@section('heading','Owner • Stock')
@section('content')
<div class="alert alert-info">
  Module stock actif en mode: <strong>{{ $pressing->stock_mode === 'central' ? 'Magasin central → Agences' : 'Stock par agence (indépendant)' }}</strong>
</div>

<div class="row g-3 mb-3">
  <div class="col-lg-4">
    <div class="card shadow-sm h-100">
      <div class="card-header">Nouvel article stock</div>
      <div class="card-body">
        <form method="POST" action="{{ route('owner.ui.stocks.items.store') }}" class="row g-2">
          @csrf
          <div class="col-12"><label class="form-label">Nom</label><input class="form-control" name="name" required></div>
          <div class="col-6"><label class="form-label">SKU</label><input class="form-control" name="sku"></div>
          <div class="col-6"><label class="form-label">Unité</label><input class="form-control" name="unit" value="unité"></div>
          <div class="col-12"><button class="btn btn-primary">Ajouter</button></div>
        </form>
      </div>
    </div>
  </div>

  <div class="col-lg-8">
    <div class="card shadow-sm h-100">
      <div class="card-header">Nouveau mouvement</div>
      <div class="card-body">
        <form method="POST" action="{{ route('owner.ui.stocks.movements.store') }}" class="row g-2">
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
          <div class="col-md-3">
            <label class="form-label">Type</label>
            <select class="form-select" name="movement_type" required>
              <option value="entree">Entrée</option>
              <option value="sortie">Sortie</option>
              <option value="transfert">Transfert</option>
              <option value="ajustement">Ajustement</option>
              <option value="perte_casse">Perte / casse</option>
            </select>
          </div>
          <div class="col-md-2"><label class="form-label">Qté</label><input class="form-control" type="number" step="0.01" min="0.01" name="quantity" required></div>
          <div class="col-md-3"><label class="form-label">Date</label><input class="form-control" type="date" name="movement_date" value="{{ $selectedDate }}" required></div>

          <div class="col-md-4"><label class="form-label">Agence (hors transfert)</label><select class="form-select" name="agency_id"><option value="">Magasin central</option>@foreach($agencies as $agency)<option value="{{ $agency->id }}">{{ $agency->name }}</option>@endforeach</select></div>
          <div class="col-md-4"><label class="form-label">Source (transfert)</label><select class="form-select" name="source_agency_id"><option value="">Magasin central</option>@foreach($agencies as $agency)<option value="{{ $agency->id }}">{{ $agency->name }}</option>@endforeach</select></div>
          <div class="col-md-4"><label class="form-label">Destination (transfert)</label><select class="form-select" name="target_agency_id"><option value="">Magasin central</option>@foreach($agencies as $agency)<option value="{{ $agency->id }}">{{ $agency->name }}</option>@endforeach</select></div>

          <div class="col-md-3"><label class="form-label">Sens ajustement</label><select class="form-select" name="adjustment_sign"><option value="plus">+</option><option value="minus">-</option></select></div>
          <div class="col-md-9"><label class="form-label">Note</label><input class="form-control" name="note"></div>
          <div class="col-12"><button class="btn btn-success">Enregistrer mouvement</button></div>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="card shadow-sm mb-3">
  <div class="card-header">Soldes de stock</div>
  <div class="table-responsive">
    <table class="table mb-0">
      <thead><tr><th>Article</th><th>Emplacement</th><th class="text-end">Quantité</th></tr></thead>
      <tbody>
      @forelse($balances as $balance)
        <tr>
          <td>{{ $balance->item?->name }}</td>
          <td>{{ $balance->agency?->name ?? 'Magasin central' }}</td>
          <td class="text-end">{{ number_format((float)$balance->quantity,2,',',' ') }}</td>
        </tr>
      @empty
        <tr><td colspan="3" class="text-center text-muted py-3">Aucun solde de stock.</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
</div>

<div class="card shadow-sm">
  <div class="card-header">Derniers mouvements</div>
  <div class="table-responsive">
    <table class="table mb-0">
      <thead><tr><th>Date</th><th>Article</th><th>Type</th><th>Détail</th><th class="text-end">Qté</th><th>Saisi par</th></tr></thead>
      <tbody>
      @forelse($movements as $m)
        <tr>
          <td>{{ optional($m->movement_date)->format('d/m/Y') }}</td>
          <td>{{ $m->item?->name }}</td>
          <td>{{ ucfirst(str_replace('_',' / ', $m->movement_type)) }}</td>
          <td>
            @if($m->movement_type === 'transfert')
              {{ $m->sourceAgency?->name ?? 'Magasin central' }} → {{ $m->targetAgency?->name ?? 'Magasin central' }}
            @else
              {{ $m->agency?->name ?? 'Magasin central' }}
            @endif
            @if($m->note)<div class="small text-muted">{{ $m->note }}</div>@endif
          </td>
          <td class="text-end">{{ number_format((float)$m->quantity,2,',',' ') }}</td>
          <td>{{ $m->user?->name ?? '-' }}</td>
        </tr>
      @empty
        <tr><td colspan="6" class="text-center text-muted py-3">Aucun mouvement.</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
