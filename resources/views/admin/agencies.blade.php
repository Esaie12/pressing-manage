@extends('layouts.app')
@section('title','Admin - Agences')
@section('heading','Admin • Agences')
@section('content')
<div class="card shadow-sm"><div class="card-header">Toutes les agences</div><div class="table-responsive"><table class="table mb-0 datatable"><thead><tr><th>Agence</th><th>Pressing</th><th>Téléphone</th><th>Adresse</th></tr></thead><tbody>@forelse($agencies as $agency)<tr><td>{{ $agency->name }}</td><td>{{ $agency->pressing?->name }}</td><td>{{ $agency->phone ?? '-' }}</td><td>{{ $agency->address ?? '-' }}</td></tr>@empty<tr><td colspan="4" class="text-center text-muted">Aucune agence</td></tr>@endforelse</tbody></table></div></div>
@endsection
