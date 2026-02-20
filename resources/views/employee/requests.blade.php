@extends('layouts.app')
@section('title','Employé - Demandes')
@section('heading','Employé • Demandes')
@section('content')
<div class="card shadow-sm mb-4">
  <div class="card-header bg-white">Créer une demande</div>
  <div class="card-body">
    <form method="POST" action="{{ route('employee.ui.requests.store') }}" class="row g-2">
      @csrf
      <div class="col-md-4"><input class="form-control" name="subject" placeholder="Objet" required></div>
      <div class="col-md-6"><input class="form-control" name="message" placeholder="Message" required></div>
      <div class="col-md-2 d-grid"><button class="btn btn-primary">Envoyer</button></div>
    </form>
  </div>
</div>

<div class="card shadow-sm">
  <div class="card-header bg-white">Mes demandes</div>
  <div class="table-responsive">
    <table class="table mb-0 datatable align-middle">
      <thead><tr><th>Date</th><th>Objet</th><th>Statut</th><th>Actions</th></tr></thead>
      <tbody>
        @foreach($myRequests as $req)
          <tr>
            <td>{{ $req->created_at?->format('d/m/Y H:i') }}</td>
            <td>{{ $req->subject }}</td>
            <td>{!! $req->status === 'read' ? '<span class="badge bg-success">Lu</span>' : '<span class="badge bg-warning text-dark">En attente</span>' !!}</td>
            <td class="d-flex gap-1 flex-wrap">
              <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#viewReq{{ $req->id }}">Voir</button>
              @if($req->status !== 'read')
                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editReq{{ $req->id }}">Modifier</button>
                <form method="POST" action="{{ route('employee.ui.requests.delete', $req) }}" onsubmit="return confirm('Supprimer cette demande ?')">@csrf<button class="btn btn-sm btn-outline-danger">Supprimer</button></form>
              @endif
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

@foreach($myRequests as $req)
  <div class="modal fade" id="viewReq{{ $req->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">{{ $req->subject }}</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><p class="mb-0">{{ $req->message }}</p></div></div></div>
  </div>

  @if($req->status !== 'read')
  <div class="modal fade" id="editReq{{ $req->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Modifier demande</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body">
      <form method="POST" action="{{ route('employee.ui.requests.update', $req) }}" class="row g-2">
        @csrf
        <div class="col-md-4"><input class="form-control" name="subject" value="{{ $req->subject }}" required></div>
        <div class="col-md-6"><input class="form-control" name="message" value="{{ $req->message }}" required></div>
        <div class="col-md-2 d-grid"><button class="btn btn-success">Enregistrer</button></div>
      </form>
    </div></div></div>
  </div>
  @endif
@endforeach
@endsection
