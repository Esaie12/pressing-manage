@extends('layouts.app')
@section('title','Profil utilisateur')
@section('heading','Mon profil')
@section('content')
<div class="row g-4">
  <div class="col-lg-6">
    <div class="card shadow-sm">
      <div class="card-header">Informations</div>
      <div class="card-body">
        <form method="POST" action="{{ route('profile.update') }}" class="vstack gap-2">@csrf
          <input class="form-control" name="name" value="{{ old('name', $user->name) }}" required>
          <input class="form-control" name="email" type="email" value="{{ old('email', $user->email) }}" required>
          <button class="btn btn-primary">Mettre à jour</button>
        </form>
      </div>
    </div>
  </div>
  <div class="col-lg-6">
    <div class="card shadow-sm">
      <div class="card-header">Mot de passe</div>
      <div class="card-body">
        <form method="POST" action="{{ route('profile.password') }}" class="vstack gap-2">@csrf
          <input class="form-control" type="password" name="current_password" placeholder="Mot de passe actuel" required>
          <input class="form-control" type="password" name="password" placeholder="Nouveau mot de passe" required>
          <input class="form-control" type="password" name="password_confirmation" placeholder="Confirmer" required>
          <button class="btn btn-primary">Modifier mot de passe</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
