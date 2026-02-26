<!doctype html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $landing->meta_title ?: ($landing->name ?: $pressing->name) }}</title>
  <meta name="description" content="{{ $landing->meta_description ?: ($landing->tagline ?: 'Landing pressing') }}">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    :root{--primary: {{ $landing->primary_color ?: '#0d6efd' }};--secondary: {{ $landing->secondary_color ?: '#20c997' }};}
    body{background:#f8f9fb;color:#1f2937}
    .hero{padding:90px 0;background:linear-gradient(135deg,var(--primary),var(--secondary));color:#fff}
    .card-lite{border:0;border-radius:1rem;box-shadow:0 8px 24px rgba(15,23,42,.08)}
  </style>
</head>
<body>
@if(in_array('hero', $sections, true))
<section class="hero text-center"><div class="container"><h1 class="display-5 fw-bold">{{ $landing->hero_title ?: ($landing->name ?: $pressing->name) }}</h1><p class="lead">{{ $landing->hero_subtitle ?: $landing->tagline }}</p></div></section>
@endif
@if(in_array('services', $sections, true))
<section class="py-5"><div class="container"><h2>Services vedettes</h2><div class="row g-3"><div class="col-md-4"><div class="card card-lite"><div class="card-body">Chemises</div></div></div><div class="col-md-4"><div class="card card-lite"><div class="card-body">Costumes</div></div></div><div class="col-md-4"><div class="card card-lite"><div class="card-body">Couettes</div></div></div></div></div></section>
@endif
<footer class="py-4 text-center text-muted small">{{ $landing->footer_text ?: ('© '.date('Y').' '.($landing->name ?: $pressing->name)) }}</footer>
</body>
</html>
