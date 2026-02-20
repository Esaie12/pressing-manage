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
        </div>
      </div>
    </div>
  </div>
</div>


<div class="card shadow-sm">
  <div class="card-header">Demande de pack personnalisé</div>
  <div class="card-body">
    <form method="POST" action="{{ route('owner.ui.pricing.custom-request.store') }}" class="row g-2">
      @csrf
      <div class="col-md-3"><label class="form-label">Agences souhaitées</label><input class="form-control" type="number" min="1" name="requested_agencies" value="1" required></div>
      <div class="col-md-3"><label class="form-label">Employés souhaités</label><input class="form-control" type="number" min="1" name="requested_employees" value="5" required></div>
      <div class="col-md-6"><label class="form-label">Note</label><input class="form-control" name="note" placeholder="Décrivez vos besoins"></div>
      <div class="col-12 d-flex flex-wrap gap-3">
        <div class="form-check"><input class="form-check-input" type="checkbox" name="want_stock_module" value="1" id="want_stock"><label class="form-check-label" for="want_stock">Module Stock (+{{ number_format((float)($customPricing->price_module_stock ?? 0),0,',',' ') }} FCFA)</label></div>
        <div class="form-check"><input class="form-check-input" type="checkbox" name="want_accounting_module" value="1" id="want_acc"><label class="form-check-label" for="want_acc">Module Comptabilité (+{{ number_format((float)($customPricing->price_module_accounting ?? 0),0,',',' ') }} FCFA)</label></div>
        <div class="form-check"><input class="form-check-input" type="checkbox" name="want_cash_closure_module" value="1" id="want_cash"><label class="form-check-label" for="want_cash">Module Clôture caisse (+{{ number_format((float)($customPricing->price_module_cash_closure ?? 0),0,',',' ') }} FCFA)</label></div>
        <div class="form-check"><input class="form-check-input" type="checkbox" name="want_customization" value="1" id="want_custom"><label class="form-check-label" for="want_custom">Personnalisation (+{{ number_format((float)($customPricing->price_customization ?? 0),0,',',' ') }} FCFA)</label></div>
      </div>
      <div class="col-12 small text-muted">
        Prix de base: {{ number_format((float)($customPricing->base_price ?? 0),0,',',' ') }} FCFA (estimation finale calculée à l'envoi selon les tranches agences/employés).
      </div>
      <div class="col-12"><button class="btn btn-outline-primary">Envoyer ma demande personnalisée</button></div>
    </form>
  </div>
</div>

@endsection
