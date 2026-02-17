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
@endsection
