@php $template = $landing->template_key; @endphp

@if($template === 'minimal_business')
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

@elseif($template === 'minimal_modern')
<!doctype html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $landing->meta_title ?: ($landing->name ?: $pressing->name) }}</title>
  <meta name="description" content="{{ $landing->meta_description ?: ($landing->tagline ?: 'Pressing professionnel') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;1,300&family=Manrope:wght@300;500;700&display=swap" rel="stylesheet">
  <style>
    :root { --primary: {{ $landing->primary_color ?: '#c8a96e' }}; --secondary: {{ $landing->secondary_color ?: '#9b7e50' }}; --bg:#0c0c0e; --surface:#141416; --border:rgba(200,169,110,.15); --text:#e8e0d4; --muted:#7a7268; }
    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0} html{scroll-behavior:smooth}
    body{background:var(--bg);color:var(--text);font-family:'Manrope',sans-serif;font-weight:300;font-size:15px;line-height:1.7;overflow-x:hidden}
    nav{position:fixed;top:0;left:0;right:0;z-index:100;display:flex;align-items:center;justify-content:space-between;padding:24px 60px;background:linear-gradient(to bottom, rgba(12,12,14,.9), transparent);backdrop-filter: blur(4px)}
    .nav-brand{font-family:'Cormorant Garamond',serif;font-size:22px;font-weight:300;letter-spacing:.15em;color:var(--primary);text-decoration:none;text-transform:uppercase}
    .nav-cta{font-size:11px;letter-spacing:.2em;text-transform:uppercase;color:var(--primary);text-decoration:none;font-weight:500;border-bottom:1px solid var(--primary);padding-bottom:2px}
    .hero{min-height:100vh;display:flex;flex-direction:column;justify-content:flex-end;padding:0 60px 80px;position:relative;overflow:hidden}
    .hero-bg{position:absolute;inset:0;background:radial-gradient(ellipse 80% 60% at 60% 40%, rgba(200,169,110,.07), transparent),radial-gradient(ellipse 40% 40% at 80% 70%, rgba(200,169,110,.04), transparent)}
    .hero-eyebrow{font-size:10px;letter-spacing:.35em;text-transform:uppercase;color:var(--primary);font-weight:500;margin-bottom:24px}
    .hero h1{font-family:'Cormorant Garamond',serif;font-size:clamp(52px,8vw,110px);font-weight:300;line-height:1.0;max-width:800px}
    .hero h1 em{font-style:italic;color:var(--primary)}
    .hero-sub{margin-top:28px;max-width:440px;color:var(--muted);font-size:14px;line-height:1.8}
    .hero-actions{display:flex;align-items:center;gap:40px;margin-top:48px}
    .btn-primary-gold{display:inline-flex;align-items:center;background:var(--primary);color:#0c0c0e;font-size:11px;letter-spacing:.2em;text-transform:uppercase;font-weight:700;text-decoration:none;padding:16px 36px}
    .btn-ghost-gold{font-size:11px;letter-spacing:.2em;text-transform:uppercase;font-weight:500;color:var(--muted);text-decoration:none}
    .gold-rule{height:1px;background:var(--border);margin:0 60px}
    .about,.services,.testimonials,.contact{padding:100px 60px}
    .section-label{font-size:10px;letter-spacing:.35em;text-transform:uppercase;color:var(--primary);font-weight:500;margin-bottom:12px}
    .services-list{display:flex;flex-direction:column}
    .service-row{display:grid;grid-template-columns:80px 1fr auto;align-items:center;gap:40px;padding:32px 0;border-bottom:1px solid var(--border)}
    .service-name{font-family:'Cormorant Garamond',serif;font-size:28px}
    .service-price{font-size:13px;color:var(--primary)}
    .testimonials{background:var(--surface)}
    .testimonials-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:12px}
    .testimonial-card{background:var(--bg);padding:40px;border:1px solid var(--border)}
    .contact{display:grid;grid-template-columns:1fr 1fr;gap:80px}
    .contact-cta-block{background:var(--surface);padding:60px 48px;border:1px solid var(--border)}
    footer{padding:32px 60px;border-top:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;font-size:11px;color:var(--muted)}
    @media (max-width:768px){nav,.hero{padding-left:24px;padding-right:24px}.about,.contact,.services,.testimonials{padding:60px 24px}.contact{grid-template-columns:1fr}.gold-rule{margin:0 24px}footer{flex-direction:column;gap:8px;text-align:center;padding:24px}}
  </style>
</head>
<body>
<nav>
  <a href="#" class="nav-brand">{{ $landing->name ?: $pressing->name }}</a>
  @if($landing->whatsapp_number)
  <a class="nav-cta" href="https://wa.me/{{ preg_replace('/\D+/', '', $landing->whatsapp_number) }}" target="_blank">Nous contacter</a>
  @endif
</nav>

@if(in_array('hero', $sections, true))
<section class="hero">
  <div class="hero-bg"></div>
  <div class="hero-eyebrow">Pressing professionnel · Excellence</div>
  <h1>{{ $landing->hero_title ?: ($landing->name ?: $pressing->name) }}<br><em>{{ $landing->hero_subtitle ?: $landing->tagline }}</em></h1>
  <p class="hero-sub">Confiez-nous vos vêtements. Nous leur rendons leur éclat avec le soin qu'ils méritent.</p>
  <div class="hero-actions">
    @if($landing->whatsapp_number)
      <a class="btn-primary-gold" href="https://wa.me/{{ preg_replace('/\D+/', '', $landing->whatsapp_number) }}" target="_blank">WhatsApp</a>
    @endif
    <a href="#services" class="btn-ghost-gold">Voir nos services</a>
  </div>
</section>
@endif
<div class="gold-rule"></div>

@if(in_array('about', $sections, true))
<section class="about">
  <div>
    <div class="section-label">{{ $landing->about_title ?: 'Notre histoire' }}</div>
    <h2>Un savoir-faire au service de l'élégance</h2>
    <p>{{ $landing->about_body ?: 'Pressing local professionnel. Qualité, rapidité et service client irréprochable.' }}</p>
  </div>
</section>
@endif
<div class="gold-rule"></div>

@if(in_array('services', $sections, true))
<section class="services" id="services">
  <div class="section-label">Nos prestations</div>
  <h2>Services vedettes</h2>
  <div class="services-list">
    <div class="service-row"><span>01</span><span class="service-name">Chemises</span><span class="service-price">À partir de 1 500 FCFA</span></div>
    <div class="service-row"><span>02</span><span class="service-name">Costumes</span><span class="service-price">À partir de 5 000 FCFA</span></div>
    <div class="service-row"><span>03</span><span class="service-name">Couettes</span><span class="service-price">À partir de 8 000 FCFA</span></div>
  </div>
</section>
@endif

@if(in_array('testimonials', $sections, true))
<section class="testimonials">
  <div class="section-label">Ce qu'ils disent</div>
  <h2>Témoignages</h2>
  <div class="testimonials-grid">
    <div class="testimonial-card">« Service impeccable et rapide. »</div>
    <div class="testimonial-card">« Très bon accueil, personnel attentionné. »</div>
  </div>
</section>
@endif

@if(in_array('contact', $sections, true))
<section class="contact" id="contact">
  <div>
    <div class="section-label">Nous trouver</div>
    <h2>{{ $landing->contact_title ?: 'Venez nous rendre visite' }}</h2>
    <p>📍 {{ $pressing->address }}</p>
    <p>📞 {{ $pressing->phone }}</p>
    @if($landing->contact_email)<p>✉️ {{ $landing->contact_email }}</p>@endif
  </div>
  <div class="contact-cta-block">
    <div class="section-label">Réservation rapide</div>
    <p>Contactez-nous directement via WhatsApp.</p>
    @if($landing->whatsapp_number)
      <a class="btn-primary-gold" href="https://wa.me/{{ preg_replace('/\D+/', '', $landing->whatsapp_number) }}" target="_blank">Écrire sur WhatsApp</a>
    @endif
  </div>
</section>
@endif

<div class="gold-rule"></div>
<footer>
  <span>{{ $landing->footer_text ?: ('© '.date('Y').' '.($landing->name ?: $pressing->name)) }}</span>
  <span style="color: var(--primary);">Excellence · Qualité · Confiance</span>
</footer>
</body>
</html>

@else
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
@endif
