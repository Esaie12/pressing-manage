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
          <select class="form-select" name="agency_id" required><option value="">-- Agence --</option>@foreach($agencies as $agency)<option value="{{ $agency->id }}">{{ $agency->name }}</option>@endforeach</select>
          <button class="btn btn-primary" type="submit">Ajouter</button>
        </form>
      </div>
    </div>
  </div>
  <div class="col-lg-8">
    <div class="card">
      <div class="card-header">Mes employés</div>
      <div class="table-responsive"><table class="table mb-0 datatable"><thead><tr><th>Nom</th><th>Email</th><th>Agence</th><th>Statut</th><th>Action</th></tr></thead><tbody>@forelse($employees as $employee)<tr><td>{{ $employee->name }}</td><td>{{ $employee->email }}</td><td>{{ $employee->agency?->name ?? '-' }}</td><td>{!! $employee->is_active ? '<span class="badge text-bg-success">Actif</span>' : '<span class="badge text-bg-secondary">Bloqué</span>' !!}</td><td><form method="POST" action="{{ route('owner.ui.employees.toggle', $employee) }}">@csrf<button class="btn btn-sm btn-outline-warning" type="submit">{{ $employee->is_active ? 'Bloquer' : 'Réactiver' }}</button></form></td></tr>@empty<tr><td colspan="5" class="text-center text-muted">Aucun employé</td></tr>@endforelse</tbody></table></div>
    </div>
  </div>
</div>
@endsection
