@extends('layouts.app')
@section('title','Owner - Modifier commande')
@section('heading','Owner • Modifier commande')
@section('content')
<div class="card shadow-sm">
  <div class="card-header">Modifier {{ $order->reference }}</div>
  <div class="card-body">
    <form method="POST" action="{{ route('owner.ui.orders.update', $order) }}" class="vstack gap-2" id="editOrderForm">
      @csrf
      <select class="form-select js-select2" name="agency_id" required>
        @foreach($agencies as $agency)
          <option value="{{ $agency->id }}" @selected($agency->id === $order->agency_id)>{{ $agency->name }}</option>
        @endforeach
      </select>
      <input class="form-control" name="client_name" value="{{ $order->client?->name }}" required>
      <input class="form-control" name="client_phone" value="{{ $order->client?->phone }}">
      <input class="form-control" type="email" name="client_email" value="{{ $order->client?->email }}">
      <input class="form-control" name="status" value="{{ $order->status }}">

      <div class="border rounded p-2">
        <div class="d-flex justify-content-between align-items-center mb-2"><strong>Items</strong><button type="button" class="btn btn-sm btn-outline-primary" id="editAddItem">+ Ajouter item</button></div>
        <div id="editItemsContainer" class="vstack gap-2">
          @foreach($order->items as $i => $item)
            <div class="row g-2 align-items-center edit-item-row">
              <div class="col-md-7">
                <select class="form-select edit-item-service js-select2" name="items[{{ $i }}][service_id]" required>
                  @foreach($services as $service)
                    <option value="{{ $service->id }}" data-price="{{ $service->price }}" @selected($service->id === $item->service_id)>{{ $service->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-3"><input class="form-control edit-item-qty" type="number" min="1" name="items[{{ $i }}][quantity]" value="{{ $item->quantity }}" required></div>
              <div class="col-md-2 d-grid"><button type="button" class="btn btn-outline-danger edit-remove-item">X</button></div>
            </div>
          @endforeach
        </div>
      </div>

      <div class="small text-muted">Total estimé: <strong id="editTotal">{{ number_format($order->total,0,',',' ') }}</strong> FCFA</div>
      <input class="form-control" type="number" min="0" step="0.01" name="discount_amount" value="{{ $order->discount_amount ?? 0 }}" placeholder="Réduction">

      <div class="form-check"><input class="form-check-input" type="checkbox" name="is_delivery" id="edit_is_delivery" value="1" @checked($order->is_delivery)><label class="form-check-label">Livraison</label></div>
      <div id="editDeliveryFields" class="vstack gap-2 {{ $order->is_delivery ? '' : 'd-none' }}">
        <input class="form-control" name="delivery_address" id="edit_delivery_address" value="{{ $order->delivery_address }}" placeholder="Adresse de livraison">
        <input class="form-control" type="number" min="0" step="0.01" name="delivery_fee" id="edit_delivery_fee" value="{{ $order->delivery_fee }}" placeholder="Montant livraison">
      </div>

      <div class="form-check"><input class="form-check-input" type="checkbox" name="paid_advance" id="edit_paid_advance" value="1" @checked($order->paid_advance)><label class="form-check-label">Payé d'avance</label></div>
      <div id="editAdvanceFields" class="vstack gap-2 {{ $order->paid_advance ? '' : 'd-none' }}">
        <input class="form-control" type="number" step="0.01" min="0" name="advance_amount" id="edit_advance_amount" value="{{ $order->advance_amount }}" placeholder="Montant avancé">
        <select class="form-select" name="payment_method" id="edit_payment_method">
          <option value="">-- Moyen de paiement --</option>
          <option value="cash" @selected($order->payment_method==='cash')>Cash</option>
          <option value="wave" @selected($order->payment_method==='wave')>Wave</option>
          <option value="orange_money" @selected($order->payment_method==='orange_money')>Orange Money</option>
          <option value="card" @selected($order->payment_method==='card')>Carte bancaire</option>
        </select>
      </div>

      <button class="btn btn-primary">Enregistrer</button>
      <a class="btn btn-outline-secondary" href="{{ route('owner.ui.orders') }}">Retour</a>
    </form>
  </div>
</div>

<template id="editItemTemplate">
  <div class="row g-2 align-items-center edit-item-row">
    <div class="col-md-7">
      <select class="form-select edit-item-service js-select2" name="items[__INDEX__][service_id]" required>
        @foreach($services as $service)
          <option value="{{ $service->id }}" data-price="{{ $service->price }}">{{ $service->name }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-3"><input class="form-control edit-item-qty" type="number" min="1" name="items[__INDEX__][quantity]" value="1" required></div>
    <div class="col-md-2 d-grid"><button type="button" class="btn btn-outline-danger edit-remove-item">X</button></div>
  </div>
</template>

<script>
(function(){
  const c=document.getElementById('editItemsContainer'), t=document.getElementById('editItemTemplate').innerHTML, add=document.getElementById('editAddItem');
  const totalEl=document.getElementById('editTotal');
  const advChk=document.getElementById('edit_paid_advance'), advFields=document.getElementById('editAdvanceFields');
  const delChk=document.getElementById('edit_is_delivery'), delFields=document.getElementById('editDeliveryFields');
  const delFee=document.getElementById('edit_delivery_fee');
  let idx={{ max($order->items->count(),1) }}; const n=v=>isNaN(parseFloat(v))?0:parseFloat(v);
  function wire(r){ r.querySelector('.edit-remove-item').onclick=()=>{ if(c.querySelectorAll('.edit-item-row').length<=1){alert('Au moins une ligne item est requise.');return;} r.remove();calc();}; r.querySelector('.edit-item-service').onchange=calc; r.querySelector('.edit-item-qty').oninput=calc; if(window.initSelect2){ window.initSelect2(r.querySelectorAll('.js-select2')); } }
  function calc(){ let total=0; c.querySelectorAll('.edit-item-row').forEach(r=>{const s=r.querySelector('.edit-item-service'); total += n(s.options[s.selectedIndex]?.dataset?.price||0)*n(r.querySelector('.edit-item-qty').value||0);}); if(delChk.checked){ total += n(delFee.value||0); } totalEl.textContent=Math.round(total).toLocaleString('fr-FR'); }
  c.querySelectorAll('.edit-item-row').forEach(wire);
  add.onclick=()=>{const html=t.replaceAll('__INDEX__',idx++); const w=document.createElement('div'); w.innerHTML=html; const r=w.firstElementChild; wire(r); c.appendChild(r); calc(); };
  advChk.onchange=()=>advFields.classList.toggle('d-none',!advChk.checked);
  delChk.onchange=()=>{delFields.classList.toggle('d-none',!delChk.checked); calc();}; delFee.oninput=calc;
  calc();
})();
</script>
@endsection
