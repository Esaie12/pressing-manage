@extends('layouts.app')
@section('title','Owner - Détail bilan')
@section('heading','Owner • Comptabilité • Détail bilan')
@section('content')
@php
  $snapshot = $report->snapshot ?? [];
  $assets = $snapshot['assets'] ?? [];
  $liabilities = $snapshot['liabilities'] ?? [];

  $debitRows = [
    ['name' => 'Immobilisations incorporelles', 'amount' => (float)($assets['intangible_assets'] ?? 0)],
    ['name' => 'Immobilisations corporelles', 'amount' => (float)($assets['tangible_assets'] ?? 0)],
    ['name' => 'Immobilisations financières', 'amount' => (float)($assets['financial_assets'] ?? 0)],
    ['name' => 'Stocks', 'amount' => (float)($assets['stocks'] ?? 0)],
    ['name' => 'Créances', 'amount' => (float)($assets['receivables'] ?? 0)],
    ['name' => 'Disponibilités', 'amount' => (float)($assets['treasury'] ?? 0)],
  ];

  $creditRows = [
    ['name' => 'Capital', 'amount' => (float)($assets['capital'] ?? 0)],
    ['name' => 'Réserves', 'amount' => (float)($assets['reserves'] ?? 0)],
    ['name' => 'Report à nouveau', 'amount' => (float)($assets['retained_earnings'] ?? 0)],
    ['name' => 'Dettes financières', 'amount' => (float)($liabilities['financial_debts'] ?? 0)],
    ['name' => "Dettes d'exploitation", 'amount' => (float)($liabilities['operating_debts'] ?? 0)],
    ['name' => 'Dettes sur immobilisations', 'amount' => (float)($liabilities['fixed_asset_debts'] ?? 0)],
    ['name' => 'Autres dettes', 'amount' => (float)($liabilities['other_debts'] ?? 0)],
  ];

  $debitRows[] = ['name' => 'Achats / dépenses du mois', 'amount' => (float)($snapshot['debits_expenses'] ?? 0)];
  $debitRows[] = ['name' => 'Paiements (transactions)', 'amount' => (float)($snapshot['debits_transactions'] ?? 0)];
  $creditRows[] = ['name' => 'Ventes / encaissements', 'amount' => (float)($snapshot['credits'] ?? 0)];

  $totalDebit = collect($debitRows)->sum('amount');
  $totalCredit = collect($creditRows)->sum('amount');
  $difference = abs($totalDebit - $totalCredit);

  $entriesRows = $report->entries->map(function($entry){
    return [
      'name' => $entry->label ?: ($entry->order_reference ? 'Commande '.$entry->order_reference : 'Entrée transaction'),
      'debit' => $entry->entry_type === 'paiement' ? (float)$entry->amount : 0,
      'credit' => $entry->entry_type === 'encaissement' ? (float)$entry->amount : 0,
    ];
  })->values();
@endphp

<div class="card shadow-sm">
  <div class="card-body" style="background:#efefef;">
    <div class="text-center text-white fw-bold py-2" style="background:#f39200; line-height:1.2;">
      {{ config('app.name', 'Entreprise') }}<br>
      Balance de vérification finale<br>
      Au {{ optional($report->month)->endOfMonth()->translatedFormat('d F Y') }}
    </div>

    <div class="table-responsive">
      <table class="table mb-0 align-middle" style="background:#efefef;">
        <thead>
          <tr style="background:#f7d88a;">
            <th>Nom du compte</th>
            <th class="text-end">Débit</th>
            <th class="text-end">Crédit</th>
          </tr>
        </thead>
        <tbody>
          @foreach($debitRows as $row)
            @if($row['amount'] > 0)
              <tr>
                <td>{{ $row['name'] }}</td>
                <td class="text-end">{{ number_format($row['amount'],2,',',' ') }} €</td>
                <td></td>
              </tr>
            @endif
          @endforeach

          @foreach($creditRows as $row)
            @if($row['amount'] > 0)
              <tr>
                <td>{{ $row['name'] }}</td>
                <td></td>
                <td class="text-end">{{ number_format($row['amount'],2,',',' ') }} €</td>
              </tr>
            @endif
          @endforeach

          @foreach($entriesRows as $row)
            <tr>
              <td>{{ $row['name'] }}</td>
              <td class="text-end">{{ $row['debit'] > 0 ? number_format($row['debit'],2,',',' ').' €' : '' }}</td>
              <td class="text-end">{{ $row['credit'] > 0 ? number_format($row['credit'],2,',',' ').' €' : '' }}</td>
            </tr>
          @endforeach

          <tr style="border-top:3px solid #999;">
            <th style="background:#f39200;color:#fff;">Totaux</th>
            <th class="text-end">{{ number_format($totalDebit,2,',',' ') }} €</th>
            <th class="text-end">{{ number_format($totalCredit,2,',',' ') }} €</th>
          </tr>
          <tr>
            <th style="background:#f7d88a;">Différence</th>
            <th></th>
            <th class="text-end">{{ number_format($difference,2,',',' ') }} €</th>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="mt-3">
      <a class="btn btn-outline-secondary btn-sm" href="{{ route('owner.ui.accounting.reports') }}">Retour</a>
    </div>
  </div>
</div>
@endsection
