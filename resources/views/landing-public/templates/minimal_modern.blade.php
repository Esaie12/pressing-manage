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
