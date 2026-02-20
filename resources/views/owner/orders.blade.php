@extends('layouts.app')
@section('title','Owner - Commandes')
@section('heading','Owner • Commandes')
@section('content')
@php
  $statusMap = $orderStatuses->keyBy('code');
@endphp
<div class="row g-4">
  <div class="col-12">
    <div class="card shadow-sm"><div class="card-body">
      <form method="GET" action="{{ route('owner.ui.orders') }}" class="row g-2 align-items-end">
        <div class="col-md-3"><label class="form-label">Date arrivée</label><input type="date" class="form-control" name="arrival_date" value="{{ $filters['arrival_date'] ?? '' }}"></div>
        <div class="col-md-3"><label class="form-label">Date retrait</label><input type="date" class="form-control" name="pickup_date" value="{{ $filters['pickup_date'] ?? '' }}"></div>
        <div class="col-md-2"><label class="form-label">Statut</label><select class="form-select" name="status"><option value="">Tous</option>@foreach($orderStatuses as $status)<option value="{{ $status->code }}" @selected(($filters['status'] ?? '')===$status->code)>{{ $status->label }}</option>@endforeach</select></div>
        <div class="col-md-2"><div class="form-check mt-4"><input class="form-check-input" type="checkbox" name="show_deleted" value="1" id="show_deleted" @checked($filters['show_deleted'] ?? false)><label class="form-check-label" for="show_deleted">Inclure supprimées</label></div></div>
        <div class="col-md-2 d-flex gap-2"><button class="btn btn-primary">Filtrer</button><a class="btn btn-outline-secondary" href="{{ route('owner.ui.orders') }}">Reset</a></div>
      </form>
    </div></div>
  </div>

  <div class="col-lg-5">
    <div class="card shadow-sm">
      <div class="card-header">Créer une commande</div>
      <div class="card-body">
        <form method="POST" action="{{ route('owner.ui.orders.store') }}" class="vstack gap-2">
          @csrf
          <select class="form-select js-select2" name="agency_id" required><option value="">-- Agence --</option>@foreach($agencies as $agency)<option value="{{ $agency->id }}">{{ $agency->name }}</option>@endforeach</select>
          <input class="form-control" name="client_name" placeholder="Nom client" required>
          <input class="form-control" name="client_phone" placeholder="Téléphone client">
          <input class="form-control" type="email" name="client_email" placeholder="Email client">
          <select class="form-select" name="status">@foreach($orderStatuses as $status)<option value="{{ $status->code }}" @selected($status->code==='pending')>{{ $status->label }}</option>@endforeach</select>
          <input class="form-control" type="number" min="0" step="0.01" name="discount_amount" placeholder="Réduction (optionnel)">

          <div class="border rounded p-2">
            <div class="d-flex justify-content-between align-items-center mb-2"><strong>Items</strong><button type="button" class="btn btn-sm btn-outline-primary" id="addItemBtn">+ Ajouter</button></div>
            <div id="itemsContainer" class="vstack gap-2"></div>
          </div>
          <div class="small text-muted">Total estimé: <strong id="totalAmount">0</strong> FCFA</div>

          <div class="form-check"><input class="form-check-input" type="checkbox" name="is_delivery" id="is_delivery" value="1"><label class="form-check-label" for="is_delivery">Livraison</label></div>
          <div id="deliveryFields" class="vstack gap-2 d-none">
            <input class="form-control" name="delivery_address" id="delivery_address" placeholder="Adresse de livraison">
            <input class="form-control" type="number" min="0" step="0.01" name="delivery_fee" id="delivery_fee" placeholder="Montant livraison">
          </div>

          <div class="form-check"><input class="form-check-input" type="checkbox" name="paid_advance" id="paid_advance" value="1"><label class="form-check-label" for="paid_advance">Payé d'avance</label></div>
          <div id="advanceFields" class="vstack gap-2 d-none">
            <input class="form-control" type="number" min="0" step="0.01" name="advance_amount" id="advance_amount" placeholder="Montant avancé">
            <select class="form-select" name="payment_method" id="payment_method"><option value="">-- Moyen de paiement --</option><option value="cash">Cash</option><option value="wave">Wave</option><option value="orange_money">Orange Money</option><option value="card">Carte bancaire</option></select>
          </div>

          <button class="btn btn-primary" type="submit">Créer commande</button>
        </form>
      </div>
    </div>
  </div>

  <div class="col-lg-7">
    <div class="card"><div class="card-header">Liste des commandes</div><div class="table-responsive">
      <table class="table mb-0 datatable align-middle">
        <thead><tr><th>Référence</th><th>Client</th><th>Total</th><th>Avance</th><th>Reste</th><th>Statut</th><th>Actions</th></tr></thead>
        <tbody>
        @forelse($orders as $order)
          @php $remaining=max(0,(float)$order->total-(float)$order->advance_amount); $status=$statusMap[$order->status] ?? null; @endphp
          <tr>
            <td>{{ $order->reference }}</td>
            <td>{{ $order->client?->name ?? '-' }}</td>
            <td>{{ number_format($order->total,0,',',' ') }}</td>
            <td>{{ number_format($order->advance_amount ?? 0,0,',',' ') }}</td>
            <td>{{ number_format($remaining,0,',',' ') }}</td>
            <td><span class="badge bg-{{ $status->badge_class ?? 'secondary' }}{{ ($status?->badge_class==='warning') ? ' text-dark' : '' }}">{{ $status->label ?? $order->status }}</span></td>
            <td class="d-flex gap-1 flex-wrap">
              @if($order->status === 'pending')
                <a class="btn btn-sm btn-outline-primary" href="{{ route('owner.ui.orders.edit', $order) }}">Modifier</a>
                @if($remaining > 0)
                  <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#payModal{{ $order->id }}">Paiement</button>
                @endif
                <form method="POST" action="{{ route('owner.ui.orders.ready', $order) }}">@csrf<button class="btn btn-sm btn-outline-primary">Prête</button></form>
                @if($remaining <= 0)
                  <form method="POST" action="{{ route('owner.ui.orders.picked', $order) }}">@csrf<button class="btn btn-sm btn-outline-success">Retirée</button></form>
                @endif
                <form method="POST" action="{{ route('owner.ui.orders.delete', $order) }}">@csrf<button class="btn btn-sm btn-outline-danger" type="submit">Supprimer</button></form>
              @endif
            </td>
          </tr>
        @empty
          <tr><td colspan="7" class="text-center text-muted">Aucune commande</td></tr>
        @endforelse
        </tbody>
      </table>
    </div></div>
  </div>
</div>

@foreach($orders as $order)
@php $remaining=max(0,(float)$order->total-(float)$order->advance_amount); @endphp
@if($remaining > 0)
<div class="modal fade" id="payModal{{ $order->id }}" tabindex="-1" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Paiement {{ $order->reference }}</h5><button class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><form method="POST" action="{{ route('owner.ui.orders.payments.store', $order) }}" class="vstack gap-2">@csrf<input class="form-control" type="number" min="1" step="0.01" name="amount" placeholder="Montant" required><select class="form-select" name="payment_method"><option value="">Moyen de paiement</option><option value="cash">Cash</option><option value="wave">Wave</option><option value="orange_money">Orange Money</option><option value="card">Carte</option></select><button class="btn btn-warning">Valider paiement</button></form></div></div></div></div>
@endif

@endforeach

<template id="itemTemplate"><div class="row g-2 align-items-center order-item-row"><div class="col-7"><select class="form-select item-service js-select2" name="items[__INDEX__][service_id]" required><option value="">-- Service --</option>@foreach($services as $service)<option value="{{ $service->id }}" data-price="{{ $service->price }}">{{ $service->name }}</option>@endforeach</select></div><div class="col-3"><input class="form-control item-qty" type="number" min="1" name="items[__INDEX__][quantity]" value="1" required></div><div class="col-2 d-grid"><button type="button" class="btn btn-outline-danger remove-item">X</button></div></div></template>
<script>
(function(){
  const c=document.getElementById('itemsContainer'),t=document.getElementById('itemTemplate').innerHTML,add=document.getElementById('addItemBtn');
  const totalEl=document.getElementById('totalAmount'),advIn=document.getElementById('advance_amount');
  const paid=document.getElementById('paid_advance'),advFields=document.getElementById('advanceFields');
  const delivery=document.getElementById('is_delivery'),deliveryFields=document.getElementById('deliveryFields'),deliveryFee=document.getElementById('delivery_fee');
  let i=0; const n=v=>isNaN(parseFloat(v))?0:parseFloat(v);
  function calc(){ let total=0; c.querySelectorAll('.order-item-row').forEach(r=>{const s=r.querySelector('.item-service'); total += n(s.options[s.selectedIndex]?.dataset?.price||0)*n(r.querySelector('.item-qty').value||0);}); if(delivery.checked){total += n(deliveryFee.value||0);} totalEl.textContent=Math.round(total).toLocaleString('fr-FR'); if(n(advIn.value)>total) advIn.value=total; }
  function addItem(){const html=t.replaceAll('__INDEX__',i++); const w=document.createElement('div'); w.innerHTML=html; const r=w.firstElementChild; r.querySelector('.remove-item').onclick=()=>{ if(c.querySelectorAll('.order-item-row').length<=1){alert('Au moins une ligne item est requise.');return;} r.remove();calc();}; r.querySelector('.item-service').onchange=calc; r.querySelector('.item-qty').oninput=calc; c.appendChild(r); if(window.initSelect2){window.initSelect2(r.querySelectorAll('.js-select2'));} calc();}
  add.onclick=addItem; paid.onchange=()=>advFields.classList.toggle('d-none',!paid.checked); delivery.onchange=()=>{deliveryFields.classList.toggle('d-none',!delivery.checked); calc();}; deliveryFee.oninput=calc; advIn.oninput=calc; addItem();
})();
</script>
@endsection
