<!doctype html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $landing->meta_title ?: ($landing->name ?: $pressing->name) }}</title>
  <meta name="description" content="{{ $landing->meta_description ?: ($landing->tagline ?: 'Pressing professionnel') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;1,300&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary: {{ $landing->primary_color ?: '#2563eb' }};
      --secondary: {{ $landing->secondary_color ?: '#7c3aed' }};
      --accent: #f59e0b;
      --bg: #f0f4ff;
      --white: #ffffff;
      --dark: #0f172a;
      --muted: #64748b;
      --border: #e2e8f0;
    }
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    html { scroll-behavior: smooth; }
    body { background: var(--bg); color: var(--dark); font-family: 'DM Sans', sans-serif; font-weight: 300; font-size: 16px; line-height: 1.65; overflow-x: hidden; }
    nav { position: sticky; top: 0; z-index: 100; background: var(--white); border-bottom: 2px solid var(--dark); display: flex; align-items: center; justify-content: space-between; padding: 0 48px; height: 68px; }
    .nav-brand { font-family: 'Syne', sans-serif; font-size: 20px; font-weight: 800; letter-spacing: -.02em; color: var(--dark); text-decoration: none; }
    .nav-brand span { color: var(--primary); }
    .nav-links { display: flex; gap: 32px; align-items: center; }
    .nav-links a { font-size: 14px; font-weight: 400; color: var(--muted); text-decoration: none; transition: color .15s; }
    .nav-links a:hover { color: var(--dark); }
    .nav-btn { background: var(--primary); color: white; font-family: 'Syne', sans-serif; font-size: 13px; font-weight: 700; letter-spacing: .03em; text-decoration: none; padding: 10px 22px; border-radius: 100px; transition: transform .15s, box-shadow .15s; box-shadow: 0 4px 0 rgba(37,99,235,.4); }
    .nav-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 0 rgba(37,99,235,.4); }
    .hero { padding: 80px 48px; display: grid; grid-template-columns: 1fr auto; gap: 40px; align-items: center; min-height: calc(100vh - 68px); position: relative; overflow: hidden; }
    .hero-blob { position: absolute; right: -100px; top: 50%; transform: translateY(-50%); width: 600px; height: 600px; background: radial-gradient(circle, var(--primary) 0%, var(--secondary) 50%, transparent 70%); opacity: .08; border-radius: 50%; pointer-events: none; }
    .hero-badge { display: inline-flex; align-items: center; gap: 8px; background: var(--white); border: 2px solid var(--dark); padding: 8px 16px; border-radius: 100px; font-family: 'Syne', sans-serif; font-size: 12px; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; margin-bottom: 28px; }
    .hero-badge-dot { width: 8px; height: 8px; border-radius: 50%; background: #22c55e; }
    .hero h1 { font-family: 'Syne', sans-serif; font-size: clamp(44px, 7vw, 92px); font-weight: 800; line-height: 1.0; letter-spacing: -.03em; }
    .hero h1 .highlight { display: inline-block; position: relative; z-index: 1; color: var(--primary); }
    .hero h1 .highlight::after { content: ''; position: absolute; left: -4px; right: -4px; bottom: 2px; z-index: -1; height: 40%; background: var(--accent); opacity: .35; }
    .hero-sub { margin-top: 24px; max-width: 480px; color: var(--muted); font-size: 17px; line-height: 1.7; }
    .hero-actions { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 40px; }
    .btn-big { display: inline-flex; align-items: center; gap: 10px; padding: 16px 32px; border-radius: 100px; font-family: 'Syne', sans-serif; font-size: 15px; font-weight: 700; text-decoration: none; }
    .btn-big-primary { background: var(--dark); color: white; }
    .btn-big-outline { background: transparent; color: var(--dark); border: 2px solid var(--dark); }
    .hero-visual { display: flex; flex-direction: column; gap: 12px; }
    .hero-card-float { background: var(--white); border: 2px solid var(--dark); border-radius: 20px; padding: 20px 24px; min-width: 200px; box-shadow: 6px 6px 0 var(--dark); }
    .hero-card-float:nth-child(2) { transform: translateX(20px); }
    .hcf-price { font-family: 'Syne', sans-serif; font-size: 22px; font-weight: 800; color: var(--primary); margin-top: 4px; }
    .features-strip { background: var(--dark); color: white; padding: 20px 48px; display: flex; gap: 60px; align-items: center; overflow-x: auto; white-space: nowrap; }
    .feature-item { font-family: 'Syne', sans-serif; font-size: 13px; font-weight: 700; text-transform: uppercase; }
    .about,.services,.testimonials,.contact { padding: 90px 48px; }
    .section-tag { display:inline-block; background: var(--primary); color:#fff; font-family:'Syne',sans-serif; font-size:11px; font-weight:700; text-transform:uppercase; padding:6px 14px; border-radius:4px; margin-bottom:20px; }
    .services-grid,.testi-grid,.contact-grid { display:grid; gap:20px; grid-template-columns: repeat(auto-fit,minmax(220px,1fr)); }
    .service-card,.contact-card,.testi-card { background:#fff; border:2px solid var(--border); border-radius:20px; padding:28px; }
    .testimonials { background: var(--dark); }
    .testimonials h2,.testi-text,.testi-author { color:#fff; }
    .contact-whatsapp { background:#25D366; border:2px solid var(--dark); border-radius:20px; padding:28px; }
    footer { background: var(--dark); color: rgba(255,255,255,.6); padding: 24px 48px; display:flex; justify-content:space-between; }
    @media (max-width: 768px) { nav{padding:0 20px;} .nav-links{display:none;} .hero{grid-template-columns:1fr;padding:48px 20px;} .about,.services,.testimonials,.contact{padding:60px 20px;} footer{flex-direction:column;gap:8px;text-align:center;padding:20px;} }
  </style>
</head>
<body>
<nav>
  <a href="#" class="nav-brand">{{ $landing->name ?: $pressing->name }}<span>.</span></a>
  <div class="nav-links">
    <a href="#services">Services</a>
    <a href="#contact">Contact</a>
    @if($landing->whatsapp_number)
      <a class="nav-btn" href="https://wa.me/{{ preg_replace('/\D+/', '', $landing->whatsapp_number) }}" target="_blank">WhatsApp</a>
    @endif
  </div>
</nav>

@if(in_array('hero', $sections, true))
<section class="hero">
  <div class="hero-blob"></div>
  <div>
    <div class="hero-badge"><span class="hero-badge-dot"></span>Pressing professionnel · Ouvert maintenant</div>
    <h1>{{ $landing->hero_title ?: ($landing->name ?: $pressing->name) }}<br><span class="highlight">{{ $landing->hero_subtitle ?: ($landing->tagline ?: 'Qualité & Rapidité') }}</span></h1>
    <p class="hero-sub">Confiez-nous vos vêtements. Nous les traitons avec les meilleurs produits pour les rendre comme neufs.</p>
    <div class="hero-actions">
      @if($landing->whatsapp_number)
      <a class="btn-big btn-big-primary" href="https://wa.me/{{ preg_replace('/\D+/', '', $landing->whatsapp_number) }}" target="_blank">💬 Contacter sur WhatsApp</a>
      @endif
      <a href="#services" class="btn-big btn-big-outline">Voir les tarifs</a>
    </div>
  </div>
  <div class="hero-visual">
    <div class="hero-card-float"><div>👔</div><div>Chemises</div><div class="hcf-price">1 500 FCFA</div></div>
    <div class="hero-card-float"><div>🥼</div><div>Costumes</div><div class="hcf-price">5 000 FCFA</div></div>
  </div>
</section>
@endif

<div class="features-strip"><span class="feature-item">✨ Résultats garantis</span><span class="feature-item">⚡ Délai 48h</span><span class="feature-item">🚚 Livraison possible</span></div>

@if(in_array('about', $sections, true))
<section class="about">
  <span class="section-tag">{{ $landing->about_title ?: 'À propos' }}</span>
  <h2>Votre pressing de confiance dans le quartier</h2>
  <p>{{ $landing->about_body ?: 'Pressing local professionnel. Qualité, rapidité et service client irréprochable.' }}</p>
</section>
@endif

@if(in_array('services', $sections, true))
<section class="services" id="services">
  <span class="section-tag">Nos prestations</span>
  <h2>Services vedettes</h2>
  <div class="services-grid">
    <div class="service-card"><h5>👔 Chemises</h5><div>À partir de 1 500 FCFA</div></div>
    <div class="service-card"><h5>🥼 Costumes</h5><div>À partir de 5 000 FCFA</div></div>
    <div class="service-card"><h5>🛌 Couettes</h5><div>À partir de 8 000 FCFA</div></div>
  </div>
</section>
@endif

@if(in_array('testimonials', $sections, true))
<section class="testimonials">
  <span class="section-tag">Avis clients</span>
  <h2>Ce qu'ils en pensent</h2>
  <div class="testi-grid">
    <div class="testi-card"><p class="testi-text">« Service impeccable et rapide. »</p><div class="testi-author">— Client satisfait</div></div>
    <div class="testi-card"><p class="testi-text">« Très bon accueil. »</p><div class="testi-author">— Cliente fidèle</div></div>
  </div>
</section>
@endif

@if(in_array('contact', $sections, true))
<section class="contact" id="contact">
  <span class="section-tag">{{ $landing->contact_title ?: 'Contact' }}</span>
  <h2>Venez nous voir !</h2>
  <div class="contact-grid">
    <div class="contact-card"><div>📍</div><div>{{ $pressing->address }}</div></div>
    <div class="contact-card"><div>📞</div><div>{{ $pressing->phone }}</div></div>
    @if($landing->contact_email)
      <div class="contact-card"><div>✉️</div><div>{{ $landing->contact_email }}</div></div>
    @endif
    @if($landing->whatsapp_number)
      <div class="contact-whatsapp"><a href="https://wa.me/{{ preg_replace('/\D+/', '', $landing->whatsapp_number) }}" target="_blank">💬 WhatsApp</a></div>
    @endif
  </div>
</section>
@endif

<footer>
  <div>{{ $landing->name ?: $pressing->name }}.</div>
  <div>{{ $landing->footer_text ?: ('© '.date('Y').' '.($landing->name ?: $pressing->name)) }}</div>
</footer>
</body>
</html>
