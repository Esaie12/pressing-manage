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
    body{background:#f8f9fb;color:#1f2937}
    .hero{padding:90px 0;background:linear-gradient(135deg,var(--primary),var(--secondary));color:#fff}
    .template-minimal_business .hero{padding:80px 0;background:#111827}
    .template-minimal_modern .hero{padding:100px 0;background:linear-gradient(120deg,#0f172a,var(--primary));}
    .card-lite{border:0;border-radius:1rem;box-shadow:0 8px 24px rgba(15,23,42,.08)}
  </style>
</head>
<body class="template-{{ $landing->template_key }}">
  @if(in_array('hero', $sections, true))
  <section class="hero text-center">
    <div class="container">
      <h1 class="display-5 fw-bold">{{ $landing->hero_title ?: $landing->name ?: $pressing->name }}</h1>
      <p class="lead">{{ $landing->hero_subtitle ?: $landing->tagline }}</p>
      @if($landing->whatsapp_number)
        <a class="btn btn-light btn-lg" href="https://wa.me/{{ preg_replace('/\D+/', '', $landing->whatsapp_number) }}" target="_blank">WhatsApp</a>
      @endif
    </div>
  </section>
  @endif

  @if(in_array('about', $sections, true))
  <section class="py-5">
    <div class="container">
      <h2>{{ $landing->about_title ?: 'À propos' }}</h2>
      <p class="text-muted mb-0">{{ $landing->about_body ?: 'Pressing local professionnel. Qualité, rapidité et service client.' }}</p>
    </div>
  </section>
  @endif

  @if(in_array('services', $sections, true))
  <section class="pb-5">
    <div class="container">
      <h2 class="mb-3">Services vedettes</h2>
      <div class="row g-3">
        <div class="col-md-4"><div class="card card-lite"><div class="card-body"><h5>Chemises</h5><div class="text-muted">À partir de 1 500 FCFA</div></div></div></div>
        <div class="col-md-4"><div class="card card-lite"><div class="card-body"><h5>Costumes</h5><div class="text-muted">À partir de 5 000 FCFA</div></div></div></div>
        <div class="col-md-4"><div class="card card-lite"><div class="card-body"><h5>Couettes</h5><div class="text-muted">À partir de 8 000 FCFA</div></div></div></div>
      </div>
    </div>
  </section>
  @endif

  @if(in_array('testimonials', $sections, true))
  <section class="pb-5">
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

  <footer class="py-4 text-center text-muted small">
    {{ $landing->footer_text ?: ('© '.date('Y').' '.($landing->name ?: $pressing->name)) }}
  </footer>
</body>
</html>
