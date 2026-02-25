@extends('layouts.app')
@section('title','Owner - Demandes employés')
@section('heading','Owner - Demandes employés')
@section('content')
<div class="card shadow-sm">
  <div class="card-header bg-white">Demandes envoyées par les employés</div>
  <div class="table-responsive">
    @if(count($requests ) >0)
    <table class="table table-striped datatable mb-0">
      <thead><tr><th>Date</th><th>Employé</th><th>Agence</th><th>Sujet</th><th>Statut</th><th>Action</th></tr></thead>
      <tbody>
        @forelse($requests as $requestItem)
          <tr>
            <td>{{ $requestItem->created_at?->format('d/m/Y H:i') }}</td>
            <td>{{ $requestItem->employee?->name ?: '-' }}</td>
            <td>{{ $requestItem->agency?->name ?: '-' }}</td>
            <td>{{ $requestItem->subject }}</td>
            <td>
              @if($requestItem->status === 'read')<span class="badge bg-success">Lu</span>
              @else<span class="badge bg-warning text-dark">En attente</span>@endif
            </td>
            <td class="d-flex gap-1 flex-wrap">
              <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#ownerReqModal{{ $requestItem->id }}">Voir</button>
              @if($requestItem->status !== 'read')
                <form method="POST" action="{{ route('owner.ui.requests.read', $requestItem) }}">
                  @csrf
                  <button class="btn btn-sm btn-outline-primary">Marquer Lu</button>
                </form>
              @else
                <span class="text-muted small">Déjà lu</span>
              @endif
            </td>
          </tr>

          <div class="modal fade" id="ownerReqModal{{ $requestItem->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">{{ $requestItem->subject }}</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><p class="mb-0">{{ $requestItem->message }}</p></div></div></div>
          </div>
        @empty
          <tr><td colspan="6" class="text-center text-muted">Aucune demande employé.</td></tr>
        @endforelse
      </tbody>
    </table>
    @else 
    <div class="text-center py-4 text-danger">
                Aucune demande employé.
              </div>
    @endif
  </div>
</div>
@endsection
