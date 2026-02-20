@extends('layouts.app')
@section('title','Owner - Modifier mouvement stock')
@section('heading','Owner • Modifier mouvement stock')
@section('content')
<div class="card shadow-sm">
  <div class="card-header d-flex justify-content-between align-items-center">
    <span>Modifier mouvement #{{ $movement->id }}</span>
    <a href="{{ route('owner.ui.stocks', ['section' => 'mouvements']) }}" class="btn btn-sm btn-outline-secondary">Retour</a>
  </div>
  <div class="card-body">
    <form method="POST" action="{{ route('owner.ui.stocks.movements.update', $movement) }}" class="row g-3">
      @csrf
      <div class="col-md-4"><label class="form-label">Article</label><select class="form-select" name="stock_item_id" required>@foreach($items as $item)<option value="{{ $item->id }}" @selected($movement->stock_item_id === $item->id)>{{ $item->name }} ({{ $item->unit }})</option>@endforeach</select></div>
      <div class="col-md-3"><label class="form-label">Type</label><select class="form-select" name="movement_type" required><option value="entree" @selected($movement->movement_type==='entree')>Entrée</option><option value="sortie" @selected($movement->movement_type==='sortie')>Sortie</option><option value="transfert" @selected($movement->movement_type==='transfert')>Transfert</option><option value="perte_casse" @selected($movement->movement_type==='perte_casse')>Perte / casse</option></select></div>
      <div class="col-md-2"><label class="form-label">Qté</label><input class="form-control" type="number" step="0.01" min="0.01" name="quantity" value="{{ $movement->quantity }}" required></div>
      <div class="col-md-3"><label class="form-label">Date</label><input class="form-control" type="date" name="movement_date" value="{{ optional($movement->movement_date)->format('Y-m-d') }}" required></div>
      <div class="col-md-6"><label class="form-label">Source</label><select class="form-select" name="source_agency_id"><option value="">Magasin central</option>@foreach($agencies as $agency)<option value="{{ $agency->id }}" @selected($movement->movement_type === 'transfert' ? $movement->source_agency_id === $agency->id : $movement->agency_id === $agency->id)>{{ $agency->name }}</option>@endforeach</select></div>
      <div class="col-md-6"><label class="form-label">Destination</label><select class="form-select" name="target_agency_id"><option value="">Magasin central</option>@foreach($agencies as $agency)<option value="{{ $agency->id }}" @selected($movement->movement_type === 'transfert' ? $movement->target_agency_id === $agency->id : $movement->agency_id === $agency->id)>{{ $agency->name }}</option>@endforeach</select></div>
      <div class="col-12"><label class="form-label">Note</label><input class="form-control" name="note" value="{{ $movement->note }}"></div>
      <div class="col-12 d-flex gap-2"><button class="btn btn-primary">Enregistrer</button><a href="{{ route('owner.ui.stocks', ['section' => 'mouvements']) }}" class="btn btn-outline-secondary">Annuler</a></div>
    </form>
  </div>
</div>
@endsection
