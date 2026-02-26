<!doctype html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $landing->meta_title ?: ($landing->name ?: $pressing->name) }}</title>
  <meta name="description" content="{{ $landing->meta_description ?: ($landing->tagline ?: 'Startup Agency Bootstrap Template') }}">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    :root{--primary:{{ $landing->primary_color ?: '#2563eb' }};--secondary:{{ $landing->secondary_color ?: '#7c3aed' }}}
    body{background:#eef3ff;color:#0f172a}
    .hero{padding:88px 0;background:radial-gradient(circle at 10% 20%,rgba(37,99,235,.12),transparent 45%),#fff}
    .title{font-weight:800;font-size:clamp(2rem,4vw,3.4rem)}
    .mock-card{background:#fff;border-radius:18px;box-shadow:0 24px 40px rgba(37,99,235,.15);border:1px solid #dbeafe}
    .blue-strip{background:linear-gradient(90deg,var(--primary),#1d4ed8);color:#fff;border-radius:14px}
    .icon-dot{width:44px;height:44px;border-radius:50%;display:inline-grid;place-items:center;background:rgba(255,255,255,.2)}
  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg bg-white border-bottom">
  <div class="container">
    <a class="navbar-brand fw-bold fs-2" href="#" style="color:var(--primary)">{{ $landing->name ?: $pressing->name }}</a>
    <div class="ms-auto d-flex gap-2">
      <a href="#services" class="btn btn-outline-primary">Services</a>
      @if($landing->whatsapp_number)
        <a href="https://wa.me/{{ preg_replace('/\D+/', '', $landing->whatsapp_number) }}" target="_blank" class="btn btn-primary">Contact</a>
      @endif
    </div>
  </div>
</nav>

@if(in_array('hero', $sections, true))
<section class="hero">
  <div class="container">
    <div class="row align-items-center g-4">
      <div class="col-lg-6">
        <h1 class="title">{{ $landing->hero_title ?: ($landing->name ?: $pressing->name) }}</h1>
        <p class="lead text-secondary">{{ $landing->hero_subtitle ?: ($landing->tagline ?: 'Bootstrap 5 agency style, clean and modern') }}</p>
        <a href="#services" class="btn btn-primary btn-lg">Voir les services</a>
      </div>
      <div class="col-lg-6">
        <div class="mock-card p-3">
          <img class="img-fluid rounded-3" src="https://images.unsplash.com/photo-1461749280684-dccba630e2f6?auto=format&fit=crop&w=1200&q=80" alt="mockup">
        </div>
      </div>
    </div>
  </div>
</section>
@endif

@if(in_array('services', $sections, true))
<section id="services" class="py-5">
  <div class="container">
    <div class="blue-strip p-4 p-md-5 mb-4">
      <div class="row g-3 text-center text-md-start">
        <div class="col-md-4"><span class="icon-dot mb-2">🧺</span><h5>Chemises</h5><small>À partir de 1 500 FCFA</small></div>
        <div class="col-md-4"><span class="icon-dot mb-2">👔</span><h5>Costumes</h5><small>À partir de 5 000 FCFA</small></div>
        <div class="col-md-4"><span class="icon-dot mb-2">🛌</span><h5>Couettes</h5><small>À partir de 8 000 FCFA</small></div>
      </div>
    </div>
  </div>
</section>
@endif

@if(in_array('contact', $sections, true))
<section class="pb-5">
  <div class="container text-center text-secondary">
    <div>📍 {{ $pressing->address }}</div>
    <div>📞 {{ $pressing->phone }} @if($landing->contact_email) · ✉️ {{ $landing->contact_email }} @endif</div>
  </div>
</section>
@endif

<footer class="py-4 bg-white border-top text-center text-muted small">
  {{ $landing->footer_text ?: ('© '.date('Y').' '.($landing->name ?: $pressing->name)) }}
</footer>
</body>
</html>
