@extends('layouts.app')
@section('title','Profil utilisateur')
@section('heading','Mon profil')
@section('content')
<div class="row g-4">
  <div class="col-lg-6">
    <div class="card shadow-sm">
      <div class="card-header">Informations</div>
      <div class="card-body">
        <form method="POST" action="{{ route('profile.update') }}" class="vstack gap-2" enctype="multipart/form-data">@csrf
          <input class="form-control" name="name" value="{{ old('name', $user->name) }}" required>
          <input class="form-control" name="email" type="email" value="{{ old('email', $user->email) }}" required>
          <select class="form-select" name="gender"><option value="">-- Sexe --</option><option value="homme" @selected(old('gender', $user->gender)==='homme')>Homme</option><option value="femme" @selected(old('gender', $user->gender)==='femme')>Femme</option><option value="autre" @selected(old('gender', $user->gender)==='autre')>Autre</option></select>
          <input class="form-control" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="Téléphone">
          <input class="form-control" name="address" value="{{ old('address', $user->address) }}" placeholder="Adresse">
          <input class="form-control" type="file" name="photo" accept="image/*">
          @if($user->photo_path)
            <div>
              <img src="{{ asset('storage/'.$user->photo_path) }}" alt="Photo de profil" style="width:80px;height:80px;object-fit:cover;border-radius:50%;">
            </div>
            <div class="form-check"><input class="form-check-input" type="checkbox" value="1" name="remove_photo" id="remove_photo"><label class="form-check-label" for="remove_photo">Supprimer la photo actuelle</label></div>
          @endif

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
