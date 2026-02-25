@extends('layouts.app')
@section('title','Owner - Employés')
@section('heading','Owner • Employés')
@section('content')
<div class="row g-4">
  <div class="col-lg-4">
    <div class="card shadow-sm">
      <div class="card-header">Ajouter un employé</div>
      <div class="card-body">
        <form method="POST" action="{{ route('owner.ui.employees.store') }}" class="vstack gap-2">
          @csrf
          <input class="form-control" name="name" placeholder="Nom" required>
          <input class="form-control" name="email" type="email" placeholder="Email" required>
          <input class="form-control" name="password" type="password" placeholder="Mot de passe" required>
          <select class="form-select" name="gender"><option value="">-- Sexe --</option><option value="homme">Homme</option><option value="femme">Femme</option><option value="autre">Autre</option></select>
          <input class="form-control" name="phone" placeholder="Téléphone">
          <input class="form-control" name="address" placeholder="Adresse">
          <select class="form-select" name="agency_id" required><option value="">-- Agence --</option>@foreach($agencies as $agency)<option value="{{ $agency->id }}">{{ $agency->name }}</option>@endforeach</select>
          <button class="btn btn-primary" type="submit">Ajouter</button>
        </form>
      </div>
    </div>
  </div>

  <div class="col-lg-8">
    <div class="card">
      <div class="card-header">Mes employés</div>
      <div class="table-responsive">
         @if(count($employees) >0)
        <table class="table mb-0 datatable align-middle">
          <thead><tr><th>Nom</th><th>Email</th><th>Agence</th><th>Statut</th><th>Action</th></tr></thead>
          <tbody>
            @foreach($employees as $employee)
              <tr>
                <td>{{ $employee->name }}</td>
                <td>{{ $employee->email }}</td>
                <td>{{ $employee->agency?->name ?? '-' }}</td>
                <td>{!! $employee->is_active ? '<span class="badge text-bg-success">Actif</span>' : '<span class="badge text-bg-secondary">Bloqué</span>' !!}</td>
                <td class="d-flex gap-1 flex-wrap">
                  <form method="POST" action="{{ route('owner.ui.employees.toggle', $employee) }}">@csrf<button class="btn btn-sm btn-outline-warning" type="submit">{{ $employee->is_active ? 'Bloquer' : 'Réactiver' }}</button></form>
                  <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#pwd{{ $employee->id }}">Nouveau mot de passe</button>
                </td>
              </tr>
             @endforeach
          </tbody>
        </table>
        @else 
        <div class="text-center py-4 text-danger">
                 Aucun employé
                  </div>
        @endif
      </div>
    </div>
  </div>
</div>

@foreach($employees as $employee)
  <div class="modal fade" id="pwd{{ $employee->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Réinitialiser mot de passe</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
          <form method="POST" action="{{ route('owner.ui.employees.password', $employee) }}" class="vstack gap-2">
            @csrf
            <input class="form-control" type="password" name="password" minlength="8" placeholder="Nouveau mot de passe" required>
            <input class="form-control" type="password" name="password_confirmation" minlength="8" placeholder="Confirmation" required>
            <button class="btn btn-primary">Enregistrer</button>
          </form>
        </div>
      </div>
    </div>
  </div>
@endforeach
@endsection
