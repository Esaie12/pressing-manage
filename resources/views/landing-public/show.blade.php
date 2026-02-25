<!doctype html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $landing->meta_title ?: $landing->name ?: $pressing->name }}</title>
  <meta name="description" content="{{ $landing->meta_description ?: ($landing->tagline ?: 'Landing pressing') }}">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    :root{--primary: {{ $landing->primary_color ?: '#0d6efd' }};--secondary: {{ $landing->secondary_color ?: '#20c997' }};}
    body{color:#1f2937}
    .card-lite{border:0;border-radius:1rem;box-shadow:0 8px 24px rgba(15,23,42,.08)}

    .layout-clean{background:#f8f9fb}
    .layout-clean .hero{padding:90px 0;background:linear-gradient(135deg,var(--primary),var(--secondary));color:#fff}

    .layout-business{background:#f4f6f8}
    .layout-business .hero{background:#0f172a;color:#fff;padding:70px 0}
    .layout-business .hero-box{background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.2);border-radius:1rem;padding:1.25rem}
    .layout-business .strip{background:#fff;border-top:1px solid #e5e7eb;border-bottom:1px solid #e5e7eb}

    .layout-modern{background:#0b1020;color:#e5e7eb}
    .layout-modern .hero{padding:110px 0;background:radial-gradient(circle at top right,var(--primary),transparent 42%), radial-gradient(circle at top left,var(--secondary),transparent 40%), #0b1020;}
    .layout-modern .panel{background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:1rem}
    .layout-modern .light-section{background:#fff;color:#1f2937;border-top-left-radius:2rem;border-top-right-radius:2rem;margin-top:1.5rem}
  </style>
</head>
@php $layout = $landing->template_key; @endphp
<body class="{{ $layout === 'minimal_business' ? 'layout-business' : ($layout === 'minimal_modern' ? 'layout-modern' : 'layout-clean') }}">

@if($layout === 'minimal_business')
  <section class="hero">
    <div class="container">
      <div class="row g-4 align-items-center">
        <div class="col-lg-7">
          <h1 class="display-5 fw-bold">{{ $landing->hero_title ?: ($landing->name ?: $pressing->name) }}</h1>
          <p class="lead mb-4">{{ $landing->hero_subtitle ?: $landing->tagline }}</p>
          @if($landing->whatsapp_number)
            <a class="btn btn-primary btn-lg" href="https://wa.me/{{ preg_replace('/\D+/', '', $landing->whatsapp_number) }}" target="_blank">Écrire sur WhatsApp</a>
          @endif
        </div>
        <div class="col-lg-5">
          <div class="hero-box">
            <div class="small text-uppercase opacity-75">Contact rapide</div>
            <div class="mt-2">📞 {{ $pressing->phone }}</div>
            <div>📍 {{ $pressing->address }}</div>
            @if($landing->contact_email)<div>✉️ {{ $landing->contact_email }}</div>@endif
          </div>
        </div>
      </div>
    </div>
  </section>

  @if(in_array('services', $sections, true))
  <section class="strip py-5">
    <div class="container">
      <h2 class="mb-3">Nos services vedettes</h2>
      <div class="row g-3">
        <div class="col-md-4"><div class="card card-lite"><div class="card-body"><h5>Chemises</h5><div class="text-muted">À partir de 1 500 FCFA</div></div></div></div>
        <div class="col-md-4"><div class="card card-lite"><div class="card-body"><h5>Costumes</h5><div class="text-muted">À partir de 5 000 FCFA</div></div></div></div>
        <div class="col-md-4"><div class="card card-lite"><div class="card-body"><h5>Couettes</h5><div class="text-muted">À partir de 8 000 FCFA</div></div></div></div>
      </div>
    </div>
  </section>
  @endif

@elseif($layout === 'minimal_modern')
  <section class="hero">
    <div class="container text-center">
      <h1 class="display-4 fw-bold">{{ $landing->hero_title ?: ($landing->name ?: $pressing->name) }}</h1>
      <p class="lead mb-4">{{ $landing->hero_subtitle ?: $landing->tagline }}</p>
      @if($landing->whatsapp_number)
        <a class="btn btn-light btn-lg" href="https://wa.me/{{ preg_replace('/\D+/', '', $landing->whatsapp_number) }}" target="_blank">WhatsApp</a>
      @endif
    </div>
  </section>

  <section class="container">
    <div class="light-section p-4 p-md-5">
      @if(in_array('about', $sections, true))
        <h2>{{ $landing->about_title ?: 'À propos' }}</h2>
        <p class="text-muted">{{ $landing->about_body ?: 'Pressing local professionnel. Qualité, rapidité et service client.' }}</p>
      @endif

      @if(in_array('services', $sections, true))
      <div class="row g-3 mt-1">
        <div class="col-md-4"><div class="panel p-3"><h5>Chemises</h5><div class="text-muted">À partir de 1 500 FCFA</div></div></div>
        <div class="col-md-4"><div class="panel p-3"><h5>Costumes</h5><div class="text-muted">À partir de 5 000 FCFA</div></div></div>
        <div class="col-md-4"><div class="panel p-3"><h5>Couettes</h5><div class="text-muted">À partir de 8 000 FCFA</div></div></div>
      </div>
      @endif
    </div>
  </section>

@else
  <section class="hero text-center">
    <div class="container">
      <h1 class="display-5 fw-bold">{{ $landing->hero_title ?: ($landing->name ?: $pressing->name) }}</h1>
      <p class="lead">{{ $landing->hero_subtitle ?: $landing->tagline }}</p>
      @if($landing->whatsapp_number)
        <a class="btn btn-light btn-lg" href="https://wa.me/{{ preg_replace('/\D+/', '', $landing->whatsapp_number) }}" target="_blank">WhatsApp</a>
      @endif
    </div>
  </section>
@endif

@if(in_array('testimonials', $sections, true))
<section class="py-5 {{ $layout === 'minimal_modern' ? 'text-white' : '' }}">
  <div class="container">
    <h2 class="mb-3">Témoignages</h2>
    <div class="row g-3">
      <div class="col-md-6"><div class="card card-lite"><div class="card-body">“Service impeccable et rapide.”</div></div></div>
      <div class="col-md-6"><div class="card card-lite"><div class="card-body">“Très bon accueil, je recommande.”</div></div></div>
    </div>
  </div>
</section>
@endif

@if(in_array('contact', $sections, true))
<section class="py-5 bg-white border-top">
  <div class="container">
    <h2>{{ $landing->contact_title ?: 'Contact' }}</h2>
    <p class="mb-1">📍 {{ $pressing->address }}</p>
    <p class="mb-1">📞 {{ $pressing->phone }}</p>
    @if($landing->contact_email)<p class="mb-1">✉️ {{ $landing->contact_email }}</p>@endif
  </div>
</section>
@endif

<footer class="py-4 text-center {{ $layout === 'minimal_modern' ? 'text-light' : 'text-muted' }} small">
  {{ $landing->footer_text ?: ('© '.date('Y').' '.($landing->name ?: $pressing->name)) }}
</footer>
</body>
</html>
