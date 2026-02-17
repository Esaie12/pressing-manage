@extends('layouts.app')
@section('title','Admin - Propriétaires')
@section('heading','Admin • Propriétaires')
@section('content')
<div class="row g-4">
  <div class="col-lg-5">
    <div class="card shadow-sm">
      <div class="card-header">Créer un propriétaire</div>
      <div class="card-body">
        <form method="POST" action="{{ route('admin.ui.owners.store') }}" class="vstack gap-2">
          @csrf
          <input class="form-control" name="name" placeholder="Nom" required>
          <input class="form-control" name="email" type="email" placeholder="Email" required>
          <input class="form-control" name="password" type="password" placeholder="Mot de passe" required>
          <input class="form-control" name="pressing_name" placeholder="Nom du pressing" required>
          <input class="form-control" name="phone" placeholder="Téléphone">
          <input class="form-control" name="address" placeholder="Adresse">
          <button class="btn btn-primary mt-2" type="submit">Créer</button>
        </form>
      </div>
    </div>
  </div>
  <div class="col-lg-7">
    <div class="card shadow-sm">
      <div class="card-header d-flex justify-content-between"><span>Liste</span><span class="text-muted">{{ $owners->count() }}</span></div>
      <div class="table-responsive">
        <table class="table mb-0 datatable">
          <thead><tr><th>Nom</th><th>Email</th><th>Pressing</th><th>Action</th></tr></thead>
          <tbody>
            @forelse($owners as $owner)
              <tr>
                <td>{{ $owner->name }}</td>
                <td>{{ $owner->email }}</td>
                <td>{{ $owner->pressing?->name ?? '-' }}</td>
                <td>
                  @if($owner->pressing_id)
                    <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.ui.owners.stats', $owner) }}">Voir stats</a>
                  @else
                    <span class="text-muted">-</span>
                  @endif
                </td>
              </tr>
            @empty
              <tr><td colspan="4" class="text-center text-muted">Aucun propriétaire</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
