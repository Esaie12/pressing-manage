@extends('layouts.app')
@section('title','Employé - Commandes')
@section('heading','Employé • Commandes')
@section('content')
@php
  $statusMap = $orderStatuses->keyBy('code');
@endphp
<div class="row g-3 mb-3">
  <div class="col-md-3"><input form="filterForm" class="form-control" type="date" name="arrival_date" value="{{ $filters['arrival_date'] ?? '' }}"></div>
  <div class="col-md-3"><input form="filterForm" class="form-control" type="date" name="pickup_date" value="{{ $filters['pickup_date'] ?? '' }}"></div>
  <div class="col-md-3"><select form="filterForm" class="form-select" name="status"><option value="">-- Statut --</option>@foreach($orderStatuses as $status)<option value="{{ $status->code }}" @selected(($filters['status'] ?? '')===$status->code)>{{ $status->label }}</option>@endforeach</select></div>
  <div class="col-md-3"><form id="filterForm" method="GET"><button class="btn btn-outline-primary w-100">Filtrer</button></form></div>
</div>

<div class="row g-4">
  <div class="col-lg-5">
    <div class="card shadow-sm">
      <div class="card-header">Créer une commande</div>
      <div class="card-body">
        <form method="POST" action="{{ route('employee.ui.orders.store') }}" class="vstack gap-2">
          @csrf
          <input class="form-control" name="client_name" placeholder="Nom client" required>
          <input class="form-control" name="client_phone" placeholder="Téléphone client">
          <input class="form-control" name="client_email" type="email" placeholder="Email client">
          <select class="form-select" name="status">@foreach($orderStatuses as $status)<option value="{{ $status->code }}" @selected($status->code==='pending')>{{ $status->label }}</option>@endforeach</select>
          <div class="border rounded p-2"><div class="d-flex justify-content-between align-items-center mb-2"><strong>Articles</strong><button type="button" class="btn btn-sm btn-outline-secondary" id="empAddItemBtn">+ Ajouter</button></div><div id="empItemsContainer" class="vstack gap-2"></div></div>
          <div class="small text-muted">Total: <strong id="empTotalAmount">0</strong> FCFA</div>
          <div class="form-check"><input class="form-check-input" type="checkbox" name="is_delivery" id="emp_is_delivery" value="1"><label class="form-check-label" for="emp_is_delivery">Livraison</label></div>
          <div id="empDeliveryFields" class="vstack gap-2 d-none"><input class="form-control" name="delivery_address" id="emp_delivery_address" placeholder="Adresse de livraison"><input class="form-control" type="number" min="0" step="0.01" name="delivery_fee" id="emp_delivery_fee" placeholder="Montant livraison"></div>
          <div class="form-check"><input class="form-check-input" type="checkbox" name="paid_advance" id="emp_paid_advance" value="1"><label class="form-check-label" for="emp_paid_advance">Payé d'avance</label></div>
          <div id="empAdvanceFields" class="vstack gap-2 d-none"><input class="form-control" type="number" min="0" step="0.01" name="advance_amount" id="emp_advance_amount" placeholder="Montant avancé"><select class="form-select" name="payment_method" id="emp_payment_method"><option value="">-- Moyen de paiement --</option><option value="cash">Cash</option><option value="wave">Wave</option><option value="orange_money">Orange Money</option><option value="card">Carte bancaire</option></select></div>
          <button class="btn btn-primary" type="submit">Créer commande</button>
        </form>
      </div>
    </div>
  </div>

  <div class="col-lg-7">
    <div class="card"><div class="card-header">Commandes de mon agence</div><div class="table-responsive"><table class="table mb-0 datatable align-middle"><thead><tr><th>Référence</th><th>Client</th><th>Total</th><th>Payé</th><th>Reste</th><th>Statut</th><th>Actions</th></tr></thead><tbody>@forelse($orders as $order)@php $remaining=max(0,(float)$order->total-(float)$order->advance_amount); $status=$statusMap[$order->status] ?? null; @endphp<tr><td>{{ $order->reference }}</td><td>{{ $order->client?->name ?? '-' }}</td><td>{{ number_format($order->total,0,',',' ') }}</td><td>{{ number_format($order->advance_amount ?? 0,0,',',' ') }}</td><td>{{ number_format($remaining,0,',',' ') }}</td><td><span class="badge bg-{{ $status->badge_class ?? 'secondary' }}{{ ($status?->badge_class==='warning') ? ' text-dark' : '' }}">{{ $status->label ?? $order->status }}</span></td><td class="d-flex gap-1 flex-wrap"><a class="btn btn-sm btn-outline-secondary" href="{{ route('employee.ui.orders.edit', $order) }}">Modifier</a><button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#payModal{{ $order->id }}">Paiement</button><button class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#discountModal{{ $order->id }}">Réduction</button><form method="POST" action="{{ route('employee.ui.orders.ready', $order) }}">@csrf<button class="btn btn-sm btn-outline-primary">Prête</button></form><form method="POST" action="{{ route('employee.ui.orders.picked', $order) }}">@csrf<button class="btn btn-sm btn-outline-success">Retirée</button></form></td></tr>@empty<tr><td colspan="7" class="text-center text-muted">Aucune commande</td></tr>@endforelse</tbody></table></div></div>
  </div>
</div>

@foreach($orders as $order)
<div class="modal fade" id="payModal{{ $order->id }}" tabindex="-1" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Paiement {{ $order->reference }}</h5><button class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><form method="POST" action="{{ route('employee.ui.orders.payments.store', $order) }}" class="vstack gap-2">@csrf<input class="form-control" type="number" min="1" step="0.01" name="amount" placeholder="Montant" required><select class="form-select" name="payment_method"><option value="">Moyen de paiement</option><option value="cash">Cash</option><option value="wave">Wave</option><option value="orange_money">Orange Money</option><option value="card">Carte</option></select><button class="btn btn-warning">Valider paiement</button></form></div></div></div></div>
<div class="modal fade" id="discountModal{{ $order->id }}" tabindex="-1" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Accorder réduction</h5><button class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><form method="POST" action="{{ route('employee.ui.orders.discount', $order) }}" class="vstack gap-2">@csrf<input class="form-control" type="number" min="1" step="0.01" name="discount_amount" placeholder="Montant réduction" required><button class="btn btn-info">Appliquer</button></form></div></div></div></div>
@endforeach

<template id="empItemTemplate"><div class="row g-2 align-items-center emp-item-row"><div class="col-7"><select class="form-select emp-item-service js-select2" name="items[__INDEX__][service_id]" required><option value="">-- Service --</option>@foreach($services as $service)<option value="{{ $service->id }}" data-price="{{ $service->price }}">{{ $service->name }}</option>@endforeach</select></div><div class="col-3"><input class="form-control emp-item-qty" type="number" min="1" name="items[__INDEX__][quantity]" value="1" required></div><div class="col-2 d-grid"><button type="button" class="btn btn-outline-danger emp-remove-item">X</button></div></div></template>
<script>
(function(){
  const c=document.getElementById('empItemsContainer'),t=document.getElementById('empItemTemplate').innerHTML,add=document.getElementById('empAddItemBtn');
  const totalEl=document.getElementById('empTotalAmount'),advIn=document.getElementById('emp_advance_amount');
  const paid=document.getElementById('emp_paid_advance'),advFields=document.getElementById('empAdvanceFields');
  const delivery=document.getElementById('emp_is_delivery'),deliveryFields=document.getElementById('empDeliveryFields'),deliveryFee=document.getElementById('emp_delivery_fee');
  let i=0; const n=v=>isNaN(parseFloat(v))?0:parseFloat(v);
  function calc(){let total=0; c.querySelectorAll('.emp-item-row').forEach(r=>{const s=r.querySelector('.emp-item-service'); total+=n(s.options[s.selectedIndex]?.dataset?.price||0)*n(r.querySelector('.emp-item-qty').value||0);}); if(delivery.checked){total += n(deliveryFee.value||0);} totalEl.textContent=Math.round(total).toLocaleString('fr-FR'); if(n(advIn.value)>total) advIn.value=total;}
  function addItem(){const html=t.replaceAll('__INDEX__',i++); const w=document.createElement('div'); w.innerHTML=html; const r=w.firstElementChild; r.querySelector('.emp-remove-item').onclick=()=>{ if(c.querySelectorAll('.emp-item-row').length<=1){alert('Au moins une ligne item est requise.');return;} r.remove();calc();}; r.querySelector('.emp-item-service').onchange=calc; r.querySelector('.emp-item-qty').oninput=calc; c.appendChild(r); if(window.initSelect2){window.initSelect2(r.querySelectorAll('.js-select2'));} calc();}
  add.onclick=addItem; paid.onchange=()=>advFields.classList.toggle('d-none',!paid.checked); delivery.onchange=()=>{deliveryFields.classList.toggle('d-none',!delivery.checked); calc();}; deliveryFee.oninput=calc; advIn.oninput=calc; addItem();
})();
</script>
@endsection
