@extends('layouts.app')
@section('title','Owner - Services')
@section('heading','Owner • Services')
@section('content')
<div class="row g-4">
  <div class="col-lg-4">
    <div class="card shadow-sm">
      <div class="card-header">Ajouter un service</div>
      <div class="card-body">
        <form method="POST" action="{{ route('owner.ui.services.store') }}" class="vstack gap-2">
          @csrf
          <select class="form-select" name="agency_id" required><option value="">-- Agence --</option>@foreach($agencies as $agency)<option value="{{ $agency->id }}">{{ $agency->name }}</option>@endforeach</select>
          <input class="form-control" name="name" placeholder="Nom du service" required>
          <input class="form-control" name="price" type="number" min="0" step="0.01" placeholder="Prix" required>
          <textarea class="form-control" name="description" placeholder="Description"></textarea>
          <button class="btn btn-primary" type="submit">Ajouter</button>
        </form>
      </div>
    </div>
  </div>
  <div class="col-lg-8">
    <div class="card"><div class="card-header">Services du pressing</div><div class="table-responsive"><table class="table mb-0 datatable"><thead><tr><th>Service</th><th>Agence</th><th>Prix</th></tr></thead><tbody>@forelse($services as $service)<tr><td>{{ $service->name }}</td><td>{{ $service->agency?->name ?? '-' }}</td><td>{{ number_format($service->price,0,',',' ') }} FCFA</td></tr>@empty<tr><td colspan="3" class="text-center text-muted">Aucun service</td></tr>@endforelse</tbody></table></div></div>
  </div>
</div>
@endsection
