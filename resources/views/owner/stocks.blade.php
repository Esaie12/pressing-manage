@extends('layouts.app')
@section('title','Owner - Stock')
@section('heading','Owner • Stock')
@section('content')
<div class="alert alert-info">
  Module stock actif en mode: <strong>{{ $pressing->stock_mode === 'central' ? 'Magasin central → Agences' : 'Stock par agence (indépendant)' }}</strong>
</div>

<div class="row g-3">
  <div class="col-lg-3">
    <div class="list-group shadow-sm">
      <a href="{{ route('owner.ui.stocks', ['section' => 'articles']) }}" class="list-group-item list-group-item-action {{ $section === 'articles' ? 'active' : '' }}">Articles</a>
      <a href="{{ route('owner.ui.stocks', ['section' => 'mouvements']) }}" class="list-group-item list-group-item-action {{ $section === 'mouvements' ? 'active' : '' }}">Mouvements</a>
      <a href="{{ route('owner.ui.stocks', ['section' => 'stock']) }}" class="list-group-item list-group-item-action {{ $section === 'stock' ? 'active' : '' }}">Stock</a>
    </div>
  </div>

  <div class="col-lg-9">
    @if($section === 'articles')
      <div class="card shadow-sm mb-3">
        <div class="card-header">Nouvel article stock</div>
        <div class="card-body">
          <form method="POST" action="{{ route('owner.ui.stocks.items.store') }}" class="row g-2">
            @csrf
            <div class="col-md-4"><label class="form-label">Nom</label><input class="form-control" name="name" required></div>
            <div class="col-md-3"><label class="form-label">SKU</label><input class="form-control" name="sku"></div>
            <div class="col-md-3"><label class="form-label">Unité</label>
              <select class="form-select" name="unit" required>
                @foreach(['unité','kg','litre','ml','paquet','carton','mètre'] as $unit)
                  <option value="{{ $unit }}">{{ $unit }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-2"><label class="form-label">Alerte stock {{ $pressing->stock_mode === 'central' ? '(magasin)' : '(agence)' }}</label><input type="number" step="0.01" min="0" class="form-control" name="alert_quantity" value="0"></div>
            <div class="col-12"><button class="btn btn-primary">Ajouter</button></div>
          </form>
        </div>
      </div>

      <div class="card shadow-sm">
        <div class="card-header">Liste des articles</div>
        <div class="table-responsive">
          <table class="table mb-0 align-middle">
            <thead><tr><th>Nom</th><th>SKU</th><th>Unité</th><th>Seuil alerte</th><th>Statut</th><th></th></tr></thead>
            <tbody>
            @forelse($items as $item)
              <tr>
                <td>
                  <form method="POST" action="{{ route('owner.ui.stocks.items.update', $item) }}" class="row g-2 align-items-center">
                    @csrf
                    <div class="col-md-4"><input class="form-control form-control-sm" name="name" value="{{ $item->name }}" required></div>
                    <div class="col-md-2"><input class="form-control form-control-sm" name="sku" value="{{ $item->sku }}"></div>
                    <div class="col-md-2">
                      <select class="form-select form-select-sm" name="unit" required>
                        @foreach(['unité','kg','litre','ml','paquet','carton','mètre'] as $unit)
                          <option value="{{ $unit }}" @selected($item->unit === $unit)>{{ $unit }}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="col-md-2"><input type="number" step="0.01" min="0" class="form-control form-control-sm" name="alert_quantity" value="{{ $pressing->stock_mode === 'central' ? ($item->alert_quantity_central ?? 0) : ($item->alert_quantity_agency ?? 0) }}"></div>
                    <div class="col-md-2"><button class="btn btn-sm btn-outline-primary">Modifier</button></div>
                  </form>
                </td>
                <td>{{ $item->sku ?: '-' }}</td>
                <td>{{ $item->unit }}</td>
                <td>{{ number_format((float)($pressing->stock_mode === 'central' ? ($item->alert_quantity_central ?? 0) : ($item->alert_quantity_agency ?? 0)),2,',',' ') }}</td>
                <td>{!! $item->is_active ? '<span class="badge text-bg-success">Actif</span>' : '<span class="badge text-bg-secondary">Inactif</span>' !!}</td>
                <td class="text-end">
                  <form method="POST" action="{{ route('owner.ui.stocks.items.delete', $item) }}" onsubmit="return confirm('Supprimer cet article ?')">
                    @csrf
                    <button class="btn btn-sm btn-outline-danger">Supprimer</button>
                  </form>
                </td>
              </tr>
            @empty
              <tr><td colspan="6" class="text-center text-muted py-3">Aucun article.</td></tr>
            @endforelse
            </tbody>
          </table>
        </div>
      </div>
    @elseif($section === 'mouvements')
      <div class="card shadow-sm mb-3">
        <div class="card-header">Nouveau mouvement</div>
        <div class="card-body">
          <form method="POST" action="{{ route('owner.ui.stocks.movements.store') }}" class="row g-2">
            @csrf
            <div class="col-md-4">
              <label class="form-label">Article</label>
              <select class="form-select" name="stock_item_id" required>
                <option value="">Choisir...</option>
                @foreach($activeItems as $item)
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
                <option value="perte_casse">Perte / casse</option>
              </select>
            </div>
            <div class="col-md-2"><label class="form-label">Qté</label><input class="form-control" type="number" step="0.01" min="0.01" name="quantity" required></div>
            <div class="col-md-3"><label class="form-label">Date</label><input class="form-control" type="date" name="movement_date" value="{{ $selectedDate }}" required></div>

            <div class="col-md-6"><label class="form-label">Source</label><select class="form-select" name="source_agency_id"><option value="">Magasin central</option>@foreach($agencies as $agency)<option value="{{ $agency->id }}">{{ $agency->name }}</option>@endforeach</select></div>
            <div class="col-md-6"><label class="form-label">Destination</label><select class="form-select" name="target_agency_id"><option value="">Magasin central</option>@foreach($agencies as $agency)<option value="{{ $agency->id }}">{{ $agency->name }}</option>@endforeach</select></div>
            <div class="col-12"><div class="small text-muted">Entrée: utiliser Destination. Sortie/Perte: utiliser Source. Transfert: Source + Destination.</div></div>

            <div class="col-md-12"><label class="form-label">Note</label><input class="form-control" name="note"></div>
            <div class="col-12"><button class="btn btn-success">Enregistrer mouvement</button></div>
          </form>
        </div>
      </div>

      <div class="card shadow-sm mb-3">
        <div class="card-body">
          <form method="GET" class="row g-2 align-items-end">
            <input type="hidden" name="section" value="mouvements">
            <div class="col-md-4">
              <label class="form-label">Filtre emplacement</label>
              <select class="form-select" name="scope">
                <option value="all" @selected($scope === 'all')>Tous</option>
                <option value="central" @selected($scope === 'central')>Magasin central</option>
                @foreach($agencies as $agency)
                  <option value="{{ $agency->id }}" @selected((string)$scope === (string)$agency->id)>{{ $agency->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-2"><button class="btn btn-outline-primary">Filtrer</button></div>
            <div class="col-md-6 text-md-end small text-muted">Modification/Suppression possible pendant {{ (int)($canEditWindowMinutes / 60) }}h après création.</div>
          </form>
        </div>
      </div>

      <div class="card shadow-sm">
        <div class="card-header">Derniers mouvements</div>
        <div class="table-responsive">
          <table class="table mb-0 align-middle">
            <thead><tr><th>Date</th><th>Article</th><th>Type</th><th>Détail</th><th class="text-end">Qté</th><th>Saisi par</th><th></th></tr></thead>
            <tbody>
            @forelse($movements as $m)
              @php
                $typeClass = match($m->movement_type) {
                  'entree' => 'text-bg-success',
                  'sortie' => 'text-bg-warning',
                  'transfert' => 'text-bg-primary',
                  'perte_casse' => 'text-bg-danger',
                  default => 'text-bg-secondary',
                };
                $canManage = $m->created_at && $m->created_at->gte(now()->subHours(3));
              @endphp
              <tr>
                <td>{{ optional($m->movement_date)->format('d/m/Y') }}</td>
                <td>{{ $m->item?->name }}</td>
                <td><span class="badge {{ $typeClass }}">{{ ucfirst(str_replace('_',' / ', $m->movement_type)) }}</span></td>
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
                <td class="text-end">
                  @if($canManage)
                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#edit-{{ $m->id }}">Modifier</button>
                    <form method="POST" action="{{ route('owner.ui.stocks.movements.delete', $m) }}" class="d-inline" onsubmit="return confirm('Supprimer ce mouvement ?')">
                      @csrf
                      <button class="btn btn-sm btn-outline-danger">Supprimer</button>
                    </form>
                  @else
                    <span class="badge text-bg-secondary">Verrouillé</span>
                  @endif
                </td>
              </tr>
              @if($canManage)
                <tr class="collapse" id="edit-{{ $m->id }}">
                  <td colspan="7" class="bg-light">
                    <form method="POST" action="{{ route('owner.ui.stocks.movements.update', $m) }}" class="row g-2 align-items-end">
                      @csrf
                      <div class="col-md-3"><label class="form-label">Article</label><select class="form-select form-select-sm" name="stock_item_id" required>@foreach($activeItems as $item)<option value="{{ $item->id }}" @selected($m->stock_item_id === $item->id)>{{ $item->name }}</option>@endforeach</select></div>
                      <div class="col-md-2"><label class="form-label">Type</label><select class="form-select form-select-sm" name="movement_type" required><option value="entree" @selected($m->movement_type==='entree')>Entrée</option><option value="sortie" @selected($m->movement_type==='sortie')>Sortie</option><option value="transfert" @selected($m->movement_type==='transfert')>Transfert</option><option value="perte_casse" @selected($m->movement_type==='perte_casse')>Perte / casse</option></select></div>
                      <div class="col-md-2"><label class="form-label">Qté</label><input class="form-control form-control-sm" type="number" step="0.01" min="0.01" name="quantity" value="{{ $m->quantity }}" required></div>
                      <div class="col-md-2"><label class="form-label">Date</label><input class="form-control form-control-sm" type="date" name="movement_date" value="{{ optional($m->movement_date)->format('Y-m-d') }}" required></div>
                      <div class="col-md-3"><label class="form-label">Note</label><input class="form-control form-control-sm" name="note" value="{{ $m->note }}"></div>
                      <div class="col-md-4"><label class="form-label">Source</label><select class="form-select form-select-sm" name="source_agency_id"><option value="">Magasin central</option>@foreach($agencies as $agency)<option value="{{ $agency->id }}" @selected($m->movement_type === 'transfert' ? $m->source_agency_id === $agency->id : $m->agency_id === $agency->id)>{{ $agency->name }}</option>@endforeach</select></div>
                      <div class="col-md-4"><label class="form-label">Destination</label><select class="form-select form-select-sm" name="target_agency_id"><option value="">Magasin central</option>@foreach($agencies as $agency)<option value="{{ $agency->id }}" @selected($m->movement_type === 'transfert' ? $m->target_agency_id === $agency->id : $m->agency_id === $agency->id)>{{ $agency->name }}</option>@endforeach</select></div>
                      <div class="col-md-4"><button class="btn btn-sm btn-primary">Enregistrer modif</button></div>
                    </form>
                  </td>
                </tr>
              @endif
            @empty
              <tr><td colspan="7" class="text-center text-muted py-3">Aucun mouvement.</td></tr>
            @endforelse
            </tbody>
          </table>
        </div>
      </div>
    @else
      <div class="card shadow-sm mb-3">
        <div class="card-body">
          <form method="GET" class="row g-2 align-items-end">
            <input type="hidden" name="section" value="stock">
            <div class="col-md-4">
              <label class="form-label">Afficher stock pour</label>
              <select class="form-select" name="scope">
                <option value="all" @selected($scope === 'all')>Tous les emplacements</option>
                <option value="central" @selected($scope === 'central')>Magasin central</option>
                @foreach($agencies as $agency)
                  <option value="{{ $agency->id }}" @selected((string)$scope === (string)$agency->id)>{{ $agency->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-2"><button class="btn btn-outline-primary">Filtrer</button></div>
          </form>
        </div>
      </div>

      <div class="card shadow-sm">
        <div class="card-header">Soldes de stock</div>
        <div class="table-responsive">
          <table class="table mb-0">
            <thead><tr><th>Article</th><th>Emplacement</th><th class="text-end">Quantité</th><th>Alerte</th></tr></thead>
            <tbody>
            @forelse($balances as $balance)
              @php
                $alert = $pressing->stock_mode === 'central' ? (float)($balance->item?->alert_quantity_central ?? 0) : (float)($balance->item?->alert_quantity_agency ?? 0);
                $isAlert = $alert > 0 && (float)$balance->quantity <= $alert;
              @endphp
              <tr class="{{ $isAlert ? 'table-danger' : '' }}">
                <td>{{ $balance->item?->name }}</td>
                <td>{{ $balance->agency?->name ?? 'Magasin central' }}</td>
                <td class="text-end fw-semibold">{{ number_format((float)$balance->quantity,2,',',' ') }}</td>
                <td>{{ $alert > 0 ? number_format($alert,2,',',' ') : '-' }}</td>
              </tr>
            @empty
              <tr><td colspan="4" class="text-center text-muted py-3">Aucun solde de stock.</td></tr>
            @endforelse
            </tbody>
          </table>
        </div>
      </div>
    @endif
  </div>
</div>
@endsection
