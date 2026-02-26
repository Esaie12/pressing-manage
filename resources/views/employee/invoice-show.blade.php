@extends('layouts.app')
@section('title','Employé - Facture')
@section('heading','Employé • Détail facture')
@section('content')
@php
    $invoiceSetting = $invoice->pressing?->invoiceSetting;
    $template = $invoiceSetting?->invoice_template ?? 'classic';
    $color = $invoiceSetting?->invoice_primary_color ?? '#0d6efd';
    $welcome = $invoiceSetting?->invoice_welcome_message ?: 'Merci pour votre confiance.';
@endphp

<div class="mb-3 d-flex gap-2 no-print"><button class="btn btn-outline-dark btn-sm" onclick="window.print()">🧾 Imprimer ticket</button></div>
<style>
@media print{
  body{background:#fff !important;}
  .navbar, .no-print, .toast-container{display:none !important;}
  main.container-xl{max-width:80mm !important; padding:0 !important;}
  .card, .p-4{box-shadow:none !important; border:0 !important;}
}
</style>

@if($template === 'classic')
<div class="card shadow-sm">
  <div class="card-header text-white" style="background: {{ $color }}">Facture {{ $invoice->invoice_number }} — Classique</div>
  <div class="card-body">
    @if($invoiceSetting?->invoice_logo_path)<img src="{{ asset('storage/'.$invoiceSetting->invoice_logo_path) }}" alt="Logo" style="max-height:60px" class="mb-2">@endif
    <p><strong>Pressing:</strong> {{ $invoice->pressing?->name }}</p>
    <p><strong>Client:</strong> {{ $invoice->order?->client?->name }}</p>
    <p><strong>Date:</strong> {{ optional($invoice->issued_at)->format('d/m/Y') }}</p>
    <div class="table-responsive"><table class="table table-sm"><thead><tr><th>Service</th><th>Qté</th><th>P.U</th><th>Total</th></tr></thead><tbody>@foreach($invoice->order->items as $item)<tr><td>{{ $item->service?->name }}</td><td>{{ $item->quantity }}</td><td>{{ number_format($item->unit_price,0,',',' ') }}</td><td>{{ number_format($item->line_total,0,',',' ') }}</td></tr>@endforeach</tbody></table></div>
    <p class="fw-bold">Montant facture: {{ number_format($invoice->amount,0,',',' ') }} FCFA</p>
    <p class="text-muted">{{ $welcome }}</p>
  </div>
</div>
@elseif($template === 'modern')
<div class="card shadow border-0" style="border-top: 6px solid {{ $color }} !important;">
  <div class="card-body">
    <div class="d-flex justify-content-between align-items-start mb-3">
      <div>@if($invoiceSetting?->invoice_logo_path)<img src="{{ asset('storage/'.$invoiceSetting->invoice_logo_path) }}" alt="Logo" style="max-height:50px" class="mb-2">@endif<h4 class="mb-0">{{ $invoice->pressing?->name }}</h4><small class="text-muted">FACTURE MODERNE</small></div>
      <span class="badge" style="background: {{ $color }}">{{ $invoice->invoice_number }}</span>
    </div>
    <div class="row mb-3"><div class="col-md-6"><strong>Client:</strong> {{ $invoice->order?->client?->name }}</div><div class="col-md-6 text-md-end"><strong>Date:</strong> {{ optional($invoice->issued_at)->format('d/m/Y') }}</div></div>
    <ul class="list-group mb-3">@foreach($invoice->order->items as $item)<li class="list-group-item d-flex justify-content-between"><span>{{ $item->service?->name }} x {{ $item->quantity }}</span><strong>{{ number_format($item->line_total,0,',',' ') }} FCFA</strong></li>@endforeach</ul>
    <div class="d-flex justify-content-between align-items-center"><span class="text-muted">{{ $welcome }}</span><h5 class="mb-0" style="color: {{ $color }}">TOTAL {{ number_format($invoice->amount,0,',',' ') }} FCFA</h5></div>
  </div>
</div>
@else
<div class="p-4 bg-white border rounded-3">
  <div class="mb-3" style="height:2px;background:{{ $color }}"></div>
  @if($invoiceSetting?->invoice_logo_path)<img src="{{ asset('storage/'.$invoiceSetting->invoice_logo_path) }}" alt="Logo" style="max-height:45px" class="mb-2">@endif
  <h5 class="mb-1">Facture {{ $invoice->invoice_number }} (Minimal)</h5>
  <p class="text-muted mb-3">{{ $invoice->pressing?->name }} — {{ optional($invoice->issued_at)->format('d/m/Y') }}</p>
  @foreach($invoice->order->items as $item)
    <div class="d-flex justify-content-between border-bottom py-2">
      <span>{{ $item->service?->name }} (x{{ $item->quantity }})</span>
      <span>{{ number_format($item->line_total,0,',',' ') }} FCFA</span>
    </div>
  @endforeach
  <div class="d-flex justify-content-between pt-3"><strong>Total</strong><strong>{{ number_format($invoice->amount,0,',',' ') }} FCFA</strong></div>
  <p class="small text-muted mt-3 mb-0">{{ $welcome }}</p>
</div>
@endif
@endsection
