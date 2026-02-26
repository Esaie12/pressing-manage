<!doctype html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $landing->meta_title ?: ($landing->name ?: $pressing->name) }}</title>
  <meta name="description" content="{{ $landing->meta_description ?: ($landing->tagline ?: 'Pressing professionnel') }}">
  <style>
    :root{--primary:{{ $landing->primary_color ?: '#2563eb' }};--secondary:{{ $landing->secondary_color ?: '#a855f7' }};--text:#0f172a;--muted:#64748b;--soft:#f1f5f9}
    *{box-sizing:border-box}body{margin:0;font-family:Inter,system-ui,sans-serif;background:radial-gradient(circle at top left,#020617,#0b1029);padding:60px 20px;color:var(--text)}
    .shell{max-width:1060px;margin:0 auto;background:#fff;border-radius:14px;padding:26px}
    nav{display:flex;justify-content:space-between;align-items:center;padding:6px 8px 20px}
    .logo{font-weight:800;color:var(--primary);letter-spacing:.04em}
    .menu{display:flex;gap:22px;font-size:13px}.menu a{text-decoration:none;color:#111827}
    .btn{background:linear-gradient(90deg,var(--primary),var(--secondary));color:#fff;padding:9px 14px;border-radius:8px;text-decoration:none;font-size:12px}
    .hero{background:#f8fafc;border-radius:10px;padding:42px;display:grid;grid-template-columns:1.1fr 1fr;gap:30px;align-items:center}
    h1{font-size:48px;line-height:1.05;margin:0 0 16px;font-weight:800;max-width:520px}
    .lead{color:var(--muted);line-height:1.7}
    .quote{margin-top:28px;padding:16px 0;border-top:1px solid #e5e7eb;color:#475569;font-size:14px}
    .author{display:flex;gap:10px;align-items:center;margin-top:14px}.avatar{width:34px;height:34px;border-radius:999px;background:var(--primary)}
    .visual{position:relative;height:430px}
    .img1,.img2{position:absolute;border-radius:8px;object-fit:cover;box-shadow:0 18px 30px rgba(15,23,42,.2)}
    .img1{inset:0 0 80px 60px;width:calc(100% - 60px);height:350px}
    .img2{left:0;bottom:0;width:62%;height:170px}
    .section{padding:56px 6px}
    .cards{display:grid;grid-template-columns:repeat(3,1fr);gap:14px}
    .card{background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:20px}
    .card h3{margin:0 0 8px}.muted{color:var(--muted)}
    @media(max-width:900px){.hero{grid-template-columns:1fr}.visual{height:320px}.img1{left:20px;width:calc(100% - 20px);height:240px}.img2{width:58%;height:120px}.menu{display:none}.cards{grid-template-columns:1fr}}
  </style>
</head>
<body>
  <div class="shell">
    <nav>
      <div class="logo">{{ $landing->name ?: $pressing->name }}</div>
      <div class="menu">
        <a href="#services">Services</a><a href="#about">À propos</a><a href="#contact">Contact</a>
      </div>
      @if($landing->whatsapp_number)
        <a class="btn" href="https://wa.me/{{ preg_replace('/\D+/', '', $landing->whatsapp_number) }}" target="_blank">WhatsApp</a>
      @endif
    </nav>

    @if(in_array('hero', $sections, true))
    <section class="hero" id="about">
      <div>
        <h1>{{ $landing->hero_title ?: ($landing->name ?: $pressing->name) }}</h1>
        <p class="lead">{{ $landing->hero_subtitle ?: ($landing->tagline ?: 'Primary services & monthly support plans') }}</p>
        <div class="quote">{{ $landing->about_body ?: 'Service pressing premium, délais rapides et suivi professionnel.' }}
          <div class="author"><div class="avatar"></div><div><strong>{{ $pressing->name }}</strong><br><span class="muted">Fondateur</span></div></div>
        </div>
      </div>
      <div class="visual">
        <img class="img1" src="https://images.unsplash.com/photo-1552664730-d307ca884978?auto=format&fit=crop&w=1000&q=80" alt="team">
        <img class="img2" src="https://images.unsplash.com/photo-1521791055366-0d553872125f?auto=format&fit=crop&w=900&q=80" alt="work">
      </div>
    </section>
    @endif

    @if(in_array('services', $sections, true))
    <section class="section" id="services">
      <div class="cards">
        <article class="card"><h3>Chemises</h3><p class="muted">À partir de 1 500 FCFA</p></article>
        <article class="card"><h3>Costumes</h3><p class="muted">À partir de 5 000 FCFA</p></article>
        <article class="card"><h3>Couettes</h3><p class="muted">À partir de 8 000 FCFA</p></article>
      </div>
    </section>
    @endif

    @if(in_array('contact', $sections, true))
    <section class="section" id="contact">
      <p class="muted">📍 {{ $pressing->address }} · 📞 {{ $pressing->phone }} @if($landing->contact_email) · ✉️ {{ $landing->contact_email }} @endif</p>
    </section>
    @endif
  </div>
</body>
</html>
