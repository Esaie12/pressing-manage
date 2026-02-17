@extends('layouts.app')
@section('title','Owner - Dépenses')
@section('heading','Owner • Dépenses')
@section('content')
<div class="card shadow-sm mb-4">
  <div class="card-header bg-white"><strong>Ajouter une dépense</strong></div>
  <div class="card-body">
    <form method="POST" action="{{ route('owner.ui.expenses.store') }}" class="row g-3">
      @csrf
      <div class="col-md-4"><label class="form-label">Titre</label><input class="form-control" name="title" required></div>
      <div class="col-md-2"><label class="form-label">Montant</label><input class="form-control" type="number" step="0.01" name="amount" required></div>
      <div class="col-md-3"><label class="form-label">Catégorie</label><input class="form-control" name="category" placeholder="Loyer, eau, transport..."></div>
      <div class="col-md-3"><label class="form-label">Date</label><input class="form-control" type="date" name="expense_date" value="{{ now()->toDateString() }}" required></div>
      <div class="col-md-4"><label class="form-label">Agence (optionnel)</label><select class="form-select" name="agency_id"><option value="">Toutes / Générale</option>@foreach($agencies as $agency)<option value="{{ $agency->id }}">{{ $agency->name }}</option>@endforeach</select></div>
      <div class="col-md-8"><label class="form-label">Note</label><input class="form-control" name="notes"></div>
      <div class="col-12"><button class="btn btn-primary">Ajouter la dépense</button></div>
    </form>
  </div>
</div>

<div class="card shadow-sm">
  <div class="card-header bg-white"><strong>Historique des dépenses</strong></div>
  <div class="card-body table-responsive">
    <table class="table table-striped datatable align-middle">
      <thead>
        <tr>
          <th>Date</th>
          <th>Titre</th>
          <th>Catégorie</th>
          <th>Agence</th>
          <th>Montant</th>
          <th>Notes</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($expenses as $expense)
          <tr>
            <td>{{ optional($expense->expense_date)->format('d/m/Y') }}</td>
            <td>{{ $expense->title }}</td>
            <td>{{ $expense->category ?: '—' }}</td>
            <td>{{ $expense->agency?->name ?: 'Générale' }}</td>
            <td>{{ number_format($expense->amount, 0, ',', ' ') }}</td>
            <td>{{ $expense->notes ?: '—' }}</td>
            <td>
              <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#edit-expense-{{ $expense->id }}">Modifier</button>
              <form method="POST" action="{{ route('owner.ui.expenses.delete', $expense) }}" class="d-inline" onsubmit="return confirm('Supprimer cette dépense ?')">
                @csrf
                <button class="btn btn-sm btn-outline-danger">Supprimer</button>
              </form>
            </td>
          </tr>
          <tr class="collapse" id="edit-expense-{{ $expense->id }}">
            <td colspan="7">
              <form method="POST" action="{{ route('owner.ui.expenses.update', $expense) }}" class="row g-2">
                @csrf
                <div class="col-md-3"><input class="form-control" name="title" value="{{ $expense->title }}" required></div>
                <div class="col-md-2"><input class="form-control" type="number" step="0.01" name="amount" value="{{ $expense->amount }}" required></div>
                <div class="col-md-2"><input class="form-control" name="category" value="{{ $expense->category }}"></div>
                <div class="col-md-2"><input class="form-control" type="date" name="expense_date" value="{{ optional($expense->expense_date)->toDateString() }}" required></div>
                <div class="col-md-3"><select class="form-select" name="agency_id"><option value="">Générale</option>@foreach($agencies as $agency)<option value="{{ $agency->id }}" @selected($expense->agency_id === $agency->id)>{{ $agency->name }}</option>@endforeach</select></div>
                <div class="col-md-9"><input class="form-control" name="notes" value="{{ $expense->notes }}"></div>
                <div class="col-md-3"><button class="btn btn-success w-100">Enregistrer</button></div>
              </form>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection
