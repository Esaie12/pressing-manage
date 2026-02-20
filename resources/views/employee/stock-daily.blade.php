@extends('layouts.app')
@section('title','Employé - Bilan stock du jour')
@section('heading','Employé • Bilan stock du jour')
@section('content')
<div class="card shadow-sm mb-3">
  <div class="card-body">
    <form method="GET" class="row g-2 align-items-end">
      <div class="col-md-4">
        <label class="form-label">Date</label>
        <input type="date" class="form-control" name="movement_date" value="{{ $movementDate }}">
      </div>
      <div class="col-md-3"><button class="btn btn-primary">Afficher</button></div>
      <div class="col-md-5 text-md-end"><span class="badge text-bg-secondary">Total sorties: {{ number_format($totalOutgoing,2,',',' ') }}</span></div>
    </form>
  </div>
</div>

<div class="card shadow-sm">
  <div class="card-header">Sorties effectuées par moi</div>
  <div class="table-responsive">
    <table class="table mb-0">
      <thead><tr><th>Heure</th><th>Article</th><th>Type</th><th>Détail</th><th class="text-end">Qté</th></tr></thead>
      <tbody>
      @forelse($outgoing as $row)
        <tr>
          <td>{{ optional($row->created_at)->format('H:i') }}</td>
          <td>{{ $row->item?->name }}</td>
          <td>{{ ucfirst(str_replace('_',' / ', $row->movement_type)) }}</td>
          <td>
            @if($row->movement_type === 'transfert')
              {{ $row->sourceAgency?->name ?? 'Magasin central' }} → {{ $row->targetAgency?->name ?? 'Magasin central' }}
            @else
              {{ $row->agency?->name ?? 'Magasin central' }}
            @endif
            @if($row->note)<div class="small text-muted">{{ $row->note }}</div>@endif
          </td>
          <td class="text-end">{{ number_format((float)$row->quantity,2,',',' ') }}</td>
        </tr>
      @empty
        <tr><td colspan="5" class="text-center text-muted py-3">Aucune sortie enregistrée pour cette date.</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
