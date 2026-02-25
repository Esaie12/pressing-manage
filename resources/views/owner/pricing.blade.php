@extends('layouts.app')
@section('title','Owner - Mon abonnement')
@section('heading','Owner • Mon abonnement')
@section('content')
<div class="row g-4 mb-4">
  <div class="col-lg-5">
    <div class="card shadow-sm h-100">
      <div class="card-header">Abonnement actuel</div>
      <div class="card-body">
        @if($currentSubscription)
          <p class="mb-2">Plan: <strong>{{ $currentSubscription->plan?->name ?? '-' }}</strong></p>
          <p class="mb-2">Cycle: <strong>{{ strtoupper($currentSubscription->billing_cycle) }}</strong></p>
          <p class="mb-2">Début: <strong>{{ optional($currentSubscription->starts_at)->format('d/m/Y') }}</strong></p>
          <p class="mb-2">Expire le: <strong>{{ optional($currentSubscription->ends_at)->format('d/m/Y') }}</strong></p>
          <p class="mb-0">Statut:
            @if($currentSubscription->is_active)
              <span class="badge text-bg-success">Actif</span>
            @else
              <span class="badge text-bg-secondary">Inactif</span>
            @endif
          </p>
        @else
          <p class="text-muted mb-0">Aucun abonnement actif trouvé pour votre pressing.</p>
        @endif
      </div>
    </div>
  </div>

  <div class="col-lg-7">
    <div class="card shadow-sm h-100">
      <div class="card-header">Souscrire à un pack</div>
      <div class="card-body">
        <div class="row g-3">
          @foreach($plans as $plan)
            <div class="col-md-6">
              <div class="border rounded p-3 h-100">
                <h6 class="mb-2">{{ $plan->name }}</h6>
                <div class="small text-muted">Mensuel</div>
                <div class="fw-bold">{{ number_format($plan->monthly_price,0,',',' ') }} FCFA</div>
                <div class="small text-muted mt-2">Annuel</div>
                <div class="fw-bold">{{ number_format($plan->annual_price,0,',',' ') }} FCFA</div>
                <hr>
                <div class="small">Agences max: <strong>{{ $plan->max_agencies }}</strong></div>
                <div class="small">Employés max: <strong>{{ $plan->max_employees }}</strong></div>
                <div class="small mt-2">Personnalisation: {!! $plan->allow_customization ? '<span class="badge text-bg-success">Oui</span>' : '<span class="badge text-bg-secondary">Non</span>' !!}</div>
                <div class="small">Module Stock: {!! $plan->allow_stock_module ? '<span class="badge text-bg-success">Oui</span>' : '<span class="badge text-bg-secondary">Non</span>' !!}</div>
                <div class="small">Module Comptabilité: {!! $plan->allow_accounting_module ? '<span class="badge text-bg-success">Oui</span>' : '<span class="badge text-bg-secondary">Non</span>' !!}</div>
                <div class="small">Module Clôture caisse: {!! $plan->allow_cash_closure_module ? '<span class="badge text-bg-success">Oui</span>' : '<span class="badge text-bg-secondary">Non</span>' !!}</div>
                <div class="small">Module Abonnements clients: {!! $plan->allow_subscription_module ? '<span class="badge text-bg-success">Oui</span>' : '<span class="badge text-bg-secondary">Non</span>' !!}</div>
                <form method="POST" action="{{ route('owner.ui.pricing.subscribe') }}" class="mt-3 vstack gap-2">
                  @csrf
                  <input type="hidden" name="subscription_plan_id" value="{{ $plan->id }}">
                  <select class="form-select form-select-sm" name="billing_cycle">
                    <option value="monthly">Mensuel</option>
                    <option value="annual">Annuel</option>
                  </select>
                  <button class="btn btn-sm btn-primary" type="submit">Souscrire</button>
                </form>
              </div>
            </div>
          @endforeach

          <div class="col-md-6">
            <div class="border rounded p-3 h-100 bg-light">
              <div class="d-flex align-items-center gap-2 mb-2">
                <img src="https://cdn-icons-png.flaticon.com/512/1828/1828919.png" alt="Personnalisé" width="28" height="28">
                <h6 class="mb-0">Pack PERSONNALISÉ</h6>
              </div>
              <p class="small text-muted">Composez votre pack: modules, nombre d'agences et d'employés. Le prix s'affiche automatiquement selon vos choix.</p>
              <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#customPackModal">Personnaliser mon pack</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="customPackModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Créer mon pack personnalisé</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <form method="POST" action="{{ route('owner.ui.pricing.custom-request.store') }}" class="row g-2" id="customPackForm">
          @csrf
          <div class="col-md-4"><label class="form-label">Agences souhaitées</label><input id="cp_agencies" class="form-control" type="number" min="1" name="requested_agencies" value="1" required></div>
          <div class="col-md-4"><label class="form-label">Employés souhaités</label><input id="cp_employees" class="form-control" type="number" min="1" name="requested_employees" value="5" required></div>
          <div class="col-md-4"><label class="form-label">Cycle</label><select class="form-select" name="billing_cycle"><option value="monthly">Mensuel</option><option value="annual">Annuel</option></select></div>
          <div class="col-12 d-flex flex-wrap gap-3">
            <div class="form-check"><input id="cp_stock" class="form-check-input" type="checkbox" name="want_stock_module" value="1"><label class="form-check-label" for="cp_stock">Module Stock</label></div>
            <div class="form-check"><input id="cp_acc" class="form-check-input" type="checkbox" name="want_accounting_module" value="1"><label class="form-check-label" for="cp_acc">Module Comptabilité</label></div>
            <div class="form-check"><input id="cp_cash" class="form-check-input" type="checkbox" name="want_cash_closure_module" value="1"><label class="form-check-label" for="cp_cash">Module Clôture caisse</label></div>
            <div class="form-check"><input id="cp_custom" class="form-check-input" type="checkbox" name="want_customization" value="1"><label class="form-check-label" for="cp_custom">Personnalisation</label></div>
            <div class="form-check"><input id="cp_subscription" class="form-check-input" type="checkbox" name="want_subscription_module" value="1"><label class="form-check-label" for="cp_subscription">Module Abonnements clients</label></div>
          </div>
          <div class="col-12"><label class="form-label">Note</label><input class="form-control" name="note" placeholder="Ajoutez une précision (optionnel)"></div>
          <div class="col-12 alert alert-info mb-0">Prix estimé en temps réel: <strong id="cp_price">0 FCFA</strong></div>
          <div class="col-12 d-flex gap-2"><button class="btn btn-primary">Activer mon pack personnalisé</button><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button></div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
(function(){
  const pricing = {
    base: {{ (float)($customPricing->base_price ?? 0) }},
    modStock: {{ (float)($customPricing->price_module_stock ?? 0) }},
    modAcc: {{ (float)($customPricing->price_module_accounting ?? 0) }},
    modCash: {{ (float)($customPricing->price_module_cash_closure ?? 0) }},
    custom: {{ (float)($customPricing->price_customization ?? 0) }},
    ag14: {{ (float)($customPricing->price_agencies_1_4 ?? 0) }},
    ag510: {{ (float)($customPricing->price_agencies_5_10 ?? 0) }},
    ag11: {{ (float)($customPricing->price_agencies_11_plus ?? 0) }},
    em15: {{ (float)($customPricing->price_employees_1_5 ?? 0) }},
    em620: {{ (float)($customPricing->price_employees_6_20 ?? 0) }},
    em21: {{ (float)($customPricing->price_employees_21_plus ?? 0) }},
  };
  const f = document.getElementById('customPackForm');
  if(!f) return;
  const priceEl = document.getElementById('cp_price');
  const calc = () => {
    const ag = Math.max(parseInt(document.getElementById('cp_agencies').value || '1', 10), 1);
    const em = Math.max(parseInt(document.getElementById('cp_employees').value || '1', 10), 1);
    let total = pricing.base;
    total += ag <= 4 ? pricing.ag14 : (ag <= 10 ? pricing.ag510 : pricing.ag11);
    total += em <= 5 ? pricing.em15 : (em <= 20 ? pricing.em620 : pricing.em21);
    if (document.getElementById('cp_stock').checked) total += pricing.modStock;
    if (document.getElementById('cp_acc').checked) total += pricing.modAcc;
    if (document.getElementById('cp_cash').checked) total += pricing.modCash;
    if (document.getElementById('cp_custom').checked) total += pricing.custom;
    priceEl.textContent = new Intl.NumberFormat('fr-FR').format(Math.round(total)) + ' FCFA';
  };
  ['cp_agencies','cp_employees','cp_stock','cp_acc','cp_cash','cp_custom','cp_subscription'].forEach((id)=>{
    const el = document.getElementById(id);
    if(el) el.addEventListener('input', calc);
    if(el) el.addEventListener('change', calc);
  });
  calc();
})();
</script>
@endsection
