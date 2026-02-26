@extends('layouts.app')
@section('title','Owner - Paramètres Pressing')
@section('heading','Owner • Paramètres Pressing')
@section('content')
<div class="card shadow-sm">
  <div class="card-header">Modifier les informations du pressing</div>
  <div class="card-body">
    <form method="POST" action="{{ route('owner.ui.settings.update') }}" class="row g-3" id="settingsForm" enctype="multipart/form-data">
      @csrf
      <div class="col-md-6"><label class="form-label">Nom</label><input class="form-control" name="name" value="{{ old('name', $pressing->name) }}" required></div>
      <div class="col-md-6"><label class="form-label">Téléphone</label><input class="form-control" name="phone" value="{{ old('phone', $pressing->phone) }}"></div>
      <div class="col-md-12"><label class="form-label">Adresse</label><input class="form-control" name="address" value="{{ old('address', $pressing->address) }}"></div>
      <div class="col-md-6"><label class="form-label">Heure d'ouverture</label><input type="time" class="form-control" name="opening_time" value="{{ old('opening_time', $pressing->opening_time) }}"></div>
      <div class="col-md-6"><label class="form-label">Heure de fermeture</label><input type="time" class="form-control" name="closing_time" value="{{ old('closing_time', $pressing->closing_time) }}"></div>

      <div class="col-12">
        <label class="form-label">Choix du modèle de facture</label>
        <div class="row g-3">
          @php $selectedTemplate = old('invoice_template', $invoiceSetting->invoice_template ?? 'classic'); @endphp
          @foreach(['classic' => 'Classique', 'modern' => 'Moderne', 'minimal' => 'Minimal'] as $key => $label)
            <div class="col-md-4">
              <label class="w-100" style="cursor:pointer">
                <input class="d-none template-radio" type="radio" name="invoice_template" value="{{ $key }}" @checked($selectedTemplate === $key)>
                <div class="border rounded p-3 template-card {{ $selectedTemplate === $key ? 'border-primary border-2' : '' }}" data-template="{{ $key }}">
                  <div class="fw-semibold mb-2">{{ $label }}</div>
                  @if($key === 'classic')
                    <div class="small text-muted">Header standard + tableau détaillé.</div>
                  @elseif($key === 'modern')
                    <div class="small text-muted">Header coloré + badges + style moderne.</div>
                  @else
                    <div class="small text-muted">Style minimal et épuré.</div>
                  @endif
                </div>
              </label>
            </div>
          @endforeach
        </div>
      </div>


      <div class="col-md-6"><label class="form-label">Logo du pressing (facture)</label><input type="file" class="form-control" name="invoice_logo" accept="image/*"></div>
      <div class="col-md-6">@if($invoiceSetting->invoice_logo_path)<div class="small text-muted mb-2">Logo actuel</div><img src="{{ asset('storage/'.$invoiceSetting->invoice_logo_path) }}" alt="logo" style="height:60px;max-width:160px;object-fit:contain">@endif</div>

      <div class="col-md-4"><label class="form-label">Couleur principale</label><input type="color" class="form-control form-control-color" name="invoice_primary_color" value="{{ old('invoice_primary_color', $invoiceSetting->invoice_primary_color ?? '#0d6efd') }}"></div>
      <div class="col-md-12"><label class="form-label">Message de bienvenue facture</label><input class="form-control" name="invoice_welcome_message" value="{{ old('invoice_welcome_message', $invoiceSetting->invoice_welcome_message) }}"></div>


      @php
        $referenceMode = old('invoice_reference_mode', $invoiceSetting->invoice_reference_mode ?? 'random');
        $referenceSeparator = old('invoice_reference_separator', $invoiceSetting->invoice_reference_separator ?? '-');
        $referenceParts = old('invoice_reference_parts', $invoiceSetting->invoice_reference_parts ?? ['ANNEE', 'JOUR', 'MOIS']);
        $orderPrefix = old('invoice_order_reference_prefix', $invoiceSetting->invoice_order_reference_prefix ?? 'CMD');
        $invoicePrefix = old('invoice_invoice_reference_prefix', $invoiceSetting->invoice_invoice_reference_prefix ?? 'FAC');
      @endphp

      <div class="col-12"><hr class="my-1"></div>
      <div class="col-12">
        <h6 class="mb-2">Référence des factures</h6>
      </div>
      <div class="col-md-4">
        <label class="form-label">Mode de référence</label>
        <select class="form-select" name="invoice_reference_mode" id="invoice_reference_mode">
          <option value="random" @selected($referenceMode === 'random')>Aléatoire</option>
          <option value="custom" @selected($referenceMode === 'custom')>Personnalisé</option>
        </select>
      </div>
      <div class="col-md-4 reference-custom-fields">
        <label class="form-label">Séparateur</label>
        <div class="btn-group w-100" role="group">
          <input type="radio" class="btn-check" name="invoice_reference_separator" id="sep_dash" value="-" @checked($referenceSeparator === '-')>
          <label class="btn btn-outline-secondary" for="sep_dash">-</label>
          <input type="radio" class="btn-check" name="invoice_reference_separator" id="sep_slash" value="/" @checked($referenceSeparator === '/')>
          <label class="btn btn-outline-secondary" for="sep_slash">/</label>
        </div>
      </div>

      <div class="col-md-6 reference-custom-fields">
        <label class="form-label">Préfixe des commandes (3 caractères)</label>
        <input class="form-control text-uppercase" name="invoice_order_reference_prefix" maxlength="3" value="{{ $orderPrefix }}">
      </div>
      <div class="col-md-6 reference-custom-fields">
        <label class="form-label">Préfixe des factures (3 caractères)</label>
        <input class="form-control text-uppercase" name="invoice_invoice_reference_prefix" maxlength="3" value="{{ $invoicePrefix }}">
      </div>

      <div class="col-md-12 reference-custom-fields">
        <label class="form-label">Ordre des éléments</label>
        <div class="d-flex flex-wrap gap-2 mb-2" id="referenceTokenButtons">
          @foreach(['ID', 'ANNEE', 'MOIS', 'JOUR'] as $token)
            <button type="button" class="btn btn-outline-primary reference-token-btn" data-token="{{ $token }}">{{ $token }}</button>
          @endforeach
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
          <span class="small text-muted">Ordre sélectionné :</span>
          <span class="badge text-bg-light border" id="referenceOrderPreview">{{ implode(($referenceSeparator ?? '-'), array_filter((array) $referenceParts)) }}</span>
          <button type="button" class="btn btn-sm btn-outline-danger" id="referenceResetBtn">Réinitialiser</button>
        </div>

        <div id="referenceHiddenInputs">
          @for($i = 0; $i < 3; $i++)
            <input type="hidden" name="invoice_reference_parts[]" value="{{ $referenceParts[$i] ?? '' }}">
          @endfor
        </div>

        <div class="form-text">Cliquez sur 3 boutons dans l'ordre voulu (ex: ANNEE puis JOUR puis MOIS).</div>
      </div>
      <div class="col-12"><hr class="my-1"></div>
      <div class="col-12">
        <h6 class="mb-2">Annulation des transactions</h6>
      </div>
      <div class="col-md-6">
        <div class="form-check form-switch mt-2">
          <input class="form-check-input" type="checkbox" role="switch" id="allow_transaction_cancellation" name="allow_transaction_cancellation" value="1" @checked(old('allow_transaction_cancellation', $pressing->allow_transaction_cancellation))>
          <label class="form-check-label" for="allow_transaction_cancellation">Autoriser l'annulation des transactions</label>
        </div>
      </div>
      <div class="col-md-6" id="cancellationWindowWrapper">
        <label class="form-label">Fenêtre d'annulation (minutes)</label>
        <input class="form-control" type="number" min="1" max="1440" name="transaction_cancellation_window_minutes" value="{{ old('transaction_cancellation_window_minutes', $pressing->transaction_cancellation_window_minutes) }}" placeholder="Ex: 30">
      </div>
      <div class="col-12"><button class="btn btn-primary">Enregistrer</button></div>
    </form>
  </div>
</div>

<script>
  document.querySelectorAll('.template-card').forEach(card => {
    card.addEventListener('click', () => {
      const radio = card.closest('label').querySelector('.template-radio');
      radio.checked = true;
      document.querySelectorAll('.template-card').forEach(c => c.classList.remove('border-primary','border-2'));
      card.classList.add('border-primary','border-2');
    });
  });

  const cancellationToggle = document.getElementById('allow_transaction_cancellation');
  const cancellationWindowWrapper = document.getElementById('cancellationWindowWrapper');
  const syncCancellationWindow = () => {
    if (!cancellationToggle || !cancellationWindowWrapper) return;
    cancellationWindowWrapper.classList.toggle('d-none', !cancellationToggle.checked);
  };
  if (cancellationToggle) {
    cancellationToggle.addEventListener('change', syncCancellationWindow);
    syncCancellationWindow();
  }


  const referenceMode = document.getElementById('invoice_reference_mode');
  const referenceCustomFields = document.querySelectorAll('.reference-custom-fields');
  const syncReferenceMode = () => {
    if (!referenceMode) return;
    const isCustom = referenceMode.value === 'custom';
    referenceCustomFields.forEach((el) => el.classList.toggle('d-none', !isCustom));
  };
  if (referenceMode) {
    referenceMode.addEventListener('change', syncReferenceMode);
    syncReferenceMode();
  }

  const tokenButtons = document.querySelectorAll('.reference-token-btn');
  const hiddenInputsContainer = document.getElementById('referenceHiddenInputs');
  const preview = document.getElementById('referenceOrderPreview');
  const resetBtn = document.getElementById('referenceResetBtn');

  const getHiddenInputs = () => hiddenInputsContainer ? Array.from(hiddenInputsContainer.querySelectorAll('input[name="invoice_reference_parts[]"]')) : [];
  const getCurrentParts = () => getHiddenInputs().map((input) => input.value).filter(Boolean);

  const setParts = (parts) => {
    const inputs = getHiddenInputs();
    for (let i = 0; i < 3; i++) {
      if (inputs[i]) {
        inputs[i].value = parts[i] || '';
      }
    }
  };

  const syncReferenceButtons = () => {
    const parts = getCurrentParts();
    tokenButtons.forEach((btn) => {
      const token = btn.dataset.token;
      const index = parts.indexOf(token);
      btn.classList.remove('btn-primary');
      btn.classList.add('btn-outline-primary');
      if (index !== -1) {
        btn.classList.remove('btn-outline-primary');
        btn.classList.add('btn-primary');
        btn.textContent = `${token} (${index + 1})`;
      } else {
        btn.textContent = token;
      }
    });

    const sepChecked = document.querySelector('input[name="invoice_reference_separator"]:checked');
    const sep = sepChecked ? sepChecked.value : '-';
    preview.textContent = parts.length ? parts.join(sep) : '-';
  };

  tokenButtons.forEach((btn) => {
    btn.addEventListener('click', () => {
      const token = btn.dataset.token;
      const parts = getCurrentParts();
      const existingIndex = parts.indexOf(token);

      if (existingIndex !== -1) {
        parts.splice(existingIndex, 1);
      } else if (parts.length < 3) {
        parts.push(token);
      }

      setParts(parts);
      syncReferenceButtons();
    });
  });

  if (resetBtn) {
    resetBtn.addEventListener('click', () => {
      setParts([]);
      syncReferenceButtons();
    });
  }

  document.querySelectorAll('input[name="invoice_reference_separator"]').forEach((input) => {
    input.addEventListener('change', syncReferenceButtons);
  });

  syncReferenceButtons();

</script>
@endsection
