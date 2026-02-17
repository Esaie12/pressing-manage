@extends('layouts.app')
@section('title','Employé - Commandes')
@section('heading','Employé • Commandes')
@section('content')
<div class="row g-4">
  <div class="col-12">
    <div class="card shadow-sm">
      <div class="card-body">
        <form method="GET" action="{{ route('employee.ui.orders') }}" class="row g-2 align-items-end">
          <div class="col-md-3"><label class="form-label">Date arrivée</label><input type="date" class="form-control" name="arrival_date" value="{{ $filters['arrival_date'] ?? '' }}"></div>
          <div class="col-md-3"><label class="form-label">Date retrait</label><input type="date" class="form-control" name="pickup_date" value="{{ $filters['pickup_date'] ?? '' }}"></div>
          <div class="col-md-3"><label class="form-label">Statut</label><select class="form-select" name="status"><option value="">Tous</option><option value="created" @selected(($filters['status'] ?? '')==='created')>Créée</option><option value="ready" @selected(($filters['status'] ?? '')==='ready')>Prête</option><option value="picked_up" @selected(($filters['status'] ?? '')==='picked_up')>Retirée</option></select></div>
          <div class="col-md-3 d-flex gap-2"><button class="btn btn-primary">Filtrer</button><a class="btn btn-outline-secondary" href="{{ route('employee.ui.orders') }}">Reset</a></div>
        </form>
      </div>
    </div>
  </div>

  <div class="col-lg-5">
    <div class="card shadow-sm">
      <div class="card-header">Enregistrer une commande</div>
      <div class="card-body">
        <form method="POST" action="{{ route('employee.ui.orders.store') }}" id="empOrderForm" class="vstack gap-2">
          @csrf
          <input class="form-control" name="client_name" placeholder="Nom client" required>
          <input class="form-control" name="client_phone" placeholder="Téléphone client">
          <input class="form-control" type="email" name="client_email" placeholder="Email client">

          <div class="border rounded p-2">
            <div class="d-flex justify-content-between align-items-center mb-2"><strong>Items de la commande</strong><button type="button" class="btn btn-sm btn-outline-primary" id="empAddItemBtn">+ Ajouter item</button></div>
            <div id="empItemsContainer" class="vstack gap-2"></div>
          </div>

          <div class="small text-muted">Total estimé: <strong id="empTotalAmount">0</strong> FCFA</div>

          <div class="form-check"><input class="form-check-input" type="checkbox" name="is_delivery" id="emp_is_delivery" value="1"><label class="form-check-label" for="emp_is_delivery">Livraison</label></div>
          <div id="empDeliveryFields" class="vstack gap-2 d-none">
            <input class="form-control" name="delivery_address" id="emp_delivery_address" placeholder="Adresse de livraison">
            <input class="form-control" type="number" min="0" step="0.01" name="delivery_fee" id="emp_delivery_fee" placeholder="Montant livraison">
          </div>

          <div class="form-check"><input class="form-check-input" type="checkbox" name="paid_advance" id="emp_paid_advance" value="1"><label class="form-check-label" for="emp_paid_advance">Payé d'avance</label></div>
          <div id="empAdvanceFields" class="vstack gap-2 d-none">
            <input class="form-control" type="number" min="0" step="0.01" name="advance_amount" id="emp_advance_amount" placeholder="Montant avancé">
            <select class="form-select" name="payment_method" id="emp_payment_method">
              <option value="">-- Moyen de paiement --</option>
              <option value="cash">Cash</option><option value="wave">Wave</option><option value="orange_money">Orange Money</option><option value="card">Carte bancaire</option>
            </select>
            <div class="small text-muted">Reste à payer: <strong id="empRemainingAmount">0</strong> FCFA</div>
          </div>

          <button class="btn btn-primary" type="submit">Créer commande</button>
        </form>
      </div>
    </div>
  </div>

  <div class="col-lg-7">
    <div class="card"><div class="card-header">Commandes de mon agence</div><div class="table-responsive"><table class="table mb-0 datatable"><thead><tr><th>Référence</th><th>Client</th><th>Total</th><th>Livraison</th><th>Payé</th><th>Reste</th><th>Statut</th><th>Actions</th></tr></thead><tbody>@forelse($orders as $order)@php $remaining=max(0,(float)$order->total-(float)$order->advance_amount); @endphp<tr><td>{{ $order->reference }}</td><td>{{ $order->client?->name ?? '-' }}</td><td>{{ number_format($order->total,0,',',' ') }} FCFA</td><td>{!! $order->is_delivery ? 'Oui ('.number_format($order->delivery_fee,0,',',' ').')' : 'Non' !!}</td><td>{{ number_format($order->advance_amount ?? 0,0,',',' ') }} FCFA</td><td>{{ number_format($remaining,0,',',' ') }} FCFA</td><td>{{ $order->status }}</td><td class="d-flex gap-1 flex-wrap">@if($remaining>0)<button class="btn btn-sm btn-outline-warning" data-bs-toggle="collapse" data-bs-target="#pay{{ $order->id }}">Paiement</button>@endif<button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editOrder{{ $order->id }}">Modifier</button><form method="POST" action="{{ route('employee.ui.orders.ready', $order) }}">@csrf<button class="btn btn-sm btn-outline-primary" type="submit">Prête</button></form><form method="POST" action="{{ route('employee.ui.orders.picked', $order) }}">@csrf<button class="btn btn-sm btn-outline-success" type="submit">Retirée</button></form></td></tr><div class="modal fade" id="editOrder{{ $order->id }}" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Modifier commande</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><form method="POST" action="{{ route('employee.ui.orders.update', $order) }}" class="vstack gap-2">@csrf<select class="form-select" name="status" required><option value="created" @selected($order->status==='created')>created</option><option value="ready" @selected($order->status==='ready')>ready</option><option value="picked_up" @selected($order->status==='picked_up')>picked_up</option></select><button class="btn btn-primary">Enregistrer</button></form></div></div></div></div>@if($remaining>0)<tr id="pay{{ $order->id }}" class="collapse"><td colspan="8"><form method="POST" action="{{ route('employee.ui.orders.payments.store', $order) }}" class="row g-2">@csrf<div class="col-md-4"><input class="form-control" type="number" min="1" max="{{ $remaining }}" step="0.01" name="amount" placeholder="Montant" required></div><div class="col-md-4"><select class="form-select" name="payment_method"><option value="">Moyen de paiement</option><option value="cash">Cash</option><option value="wave">Wave</option><option value="orange_money">Orange Money</option><option value="card">Carte bancaire</option></select></div><div class="col-md-4 d-grid"><button class="btn btn-warning">Ajouter paiement</button></div></form></td></tr>@endif @empty<tr><td colspan="8" class="text-center text-muted">Aucune commande</td></tr>@endforelse</tbody></table></div></div>
  </div>
</div>

<template id="empItemTemplate"><div class="row g-2 align-items-center emp-item-row"><div class="col-7"><select class="form-select emp-item-service js-select2" name="items[__INDEX__][service_id]" required><option value="">-- Service --</option>@foreach($services as $service)<option value="{{ $service->id }}" data-price="{{ $service->price }}">{{ $service->name }} ({{ number_format($service->price,0,',',' ') }} FCFA)</option>@endforeach</select></div><div class="col-3"><input class="form-control emp-item-qty" type="number" min="1" name="items[__INDEX__][quantity]" value="1" required></div><div class="col-2 d-grid"><button type="button" class="btn btn-outline-danger emp-remove-item">X</button></div></div></template>

<script>
(function(){
  const c=document.getElementById('empItemsContainer'),t=document.getElementById('empItemTemplate').innerHTML,add=document.getElementById('empAddItemBtn');
  const totalEl=document.getElementById('empTotalAmount'),advIn=document.getElementById('emp_advance_amount'),rem=document.getElementById('empRemainingAmount');
  const paid=document.getElementById('emp_paid_advance'),advFields=document.getElementById('empAdvanceFields');
  const delivery=document.getElementById('emp_is_delivery'),deliveryFields=document.getElementById('empDeliveryFields'),deliveryFee=document.getElementById('emp_delivery_fee');
  let i=0; const n=v=>isNaN(parseFloat(v))?0:parseFloat(v);
  function calc(){let total=0; c.querySelectorAll('.emp-item-row').forEach(r=>{const s=r.querySelector('.emp-item-service'); const q=n(r.querySelector('.emp-item-qty').value); const p=n(s.options[s.selectedIndex]?.dataset?.price||0); total+=p*q;}); if(delivery.checked){ total += n(deliveryFee.value||0); } totalEl.textContent=Math.round(total).toLocaleString('fr-FR'); if(n(advIn.value)>total) advIn.value=total; rem.textContent=Math.round(Math.max(0,total-n(advIn.value||0))).toLocaleString('fr-FR');}
  function addItem(){const html=t.replaceAll('__INDEX__',i++); const w=document.createElement('div'); w.innerHTML=html; const r=w.firstElementChild; r.querySelector('.emp-remove-item').onclick=()=>{ if(c.querySelectorAll('.emp-item-row').length<=1){alert('Au moins une ligne item est requise.');return;} r.remove();calc();}; r.querySelector('.emp-item-service').onchange=calc; r.querySelector('.emp-item-qty').oninput=calc; c.appendChild(r); if(window.initSelect2){ window.initSelect2(r.querySelectorAll('.js-select2')); } calc();}
  add.onclick=addItem; paid.onchange=()=>{advFields.classList.toggle('d-none',!paid.checked); if(!paid.checked){advIn.value=''; document.getElementById('emp_payment_method').value='';} calc();}; advIn.oninput=calc;
  delivery.onchange=()=>{deliveryFields.classList.toggle('d-none',!delivery.checked); if(!delivery.checked){deliveryFee.value=''; document.getElementById('emp_delivery_address').value='';} calc();}; deliveryFee.oninput=calc;
  addItem();
})();
</script>
@endsection
