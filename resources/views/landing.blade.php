<!doctype html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Pressing Platform</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background:#f7f9fc; color:#1e293b; }
    .hero { background:linear-gradient(135deg,#0d6efd,#20c997); color:#fff; padding:90px 0 70px; position:relative; overflow:hidden; }
    .hero::after { content:''; position:absolute; inset:auto -120px -120px auto; width:360px; height:360px; border-radius:50%; background:rgba(255,255,255,.16); }
    .feature-card, .showcase-card { border:0; border-radius:1rem; }
    .hero-img { border-radius:1rem; box-shadow:0 14px 40px rgba(0,0,0,.2); }
    .section-title { font-weight:700; }
    .gallery-img { border-radius: .9rem; width:100%; height:220px; object-fit:cover; }
  </style>
</head>
<body>
  <section class="hero">
    <div class="container position-relative">
      <div class="row align-items-center g-4">
        <div class="col-lg-6">
          <h1 class="display-4 fw-bold">Digitalisez votre pressing, simplement.</h1>
          <p class="lead mt-3">Gérez vos commandes, employés, factures personnalisées, statistiques et dépenses sur une seule plateforme moderne.</p>
          <div class="d-flex flex-wrap gap-2 mt-4">
            <a href="/login" class="btn btn-light btn-lg">Se connecter</a>
            <a href="#features" class="btn btn-outline-light btn-lg">Découvrir</a>
          </div>
        </div>
        <div class="col-lg-6">
          <img class="img-fluid hero-img" src="https://images.unsplash.com/photo-1517430816045-df4b7de11d1d?auto=format&fit=crop&w=1200&q=80" alt="Pressing dashboard">
        </div>
      </div>
    </div>
  </section>

  <section id="features" class="py-5">
    <div class="container">
      <h2 class="section-title text-center mb-4">Fonctionnalités clés</h2>
      <div class="row g-3">
        <div class="col-md-4"><div class="card feature-card h-100 shadow-sm"><div class="card-body"><h5>📦 Commandes & Livraison</h5><p class="mb-0">Prise de commande multi-items, suivi des statuts, options de livraison et paiement d'avance.</p></div></div></div>
        <div class="col-md-4"><div class="card feature-card h-100 shadow-sm"><div class="card-body"><h5>📊 Statistiques intelligentes</h5><p class="mb-0">CA hebdo/mensuel, performances employé, comparaison dépenses et graphiques clairs.</p></div></div></div>
        <div class="col-md-4"><div class="card feature-card h-100 shadow-sm"><div class="card-body"><h5>🧾 Factures personnalisables</h5><p class="mb-0">Templates, couleurs, logo du pressing et aperçu instantané.</p></div></div></div>
      </div>
    </div>
  </section>

  <section class="py-5 bg-white border-top border-bottom">
    <div class="container">
      <div class="row g-3">
        <div class="col-md-4"><img class="gallery-img" src="https://images.unsplash.com/photo-1521656693074-0ef32e80a5d5?auto=format&fit=crop&w=900&q=80" alt="Equipe pressing"></div>
        <div class="col-md-4"><img class="gallery-img" src="https://images.unsplash.com/photo-1604335399105-a0c585fd81a1?auto=format&fit=crop&w=900&q=80" alt="Blanchisserie moderne"></div>
        <div class="col-md-4"><img class="gallery-img" src="https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?auto=format&fit=crop&w=900&q=80" alt="Gestion pressing"></div>
      </div>
    </div>
  </section>

  <section class="py-5">
    <div class="container">
      <div class="card showcase-card shadow-sm">
        <div class="card-body p-4 p-lg-5 text-center">
          <h3 class="mb-3">Prêt à accélérer votre pressing ?</h3>
          <p class="text-muted mb-4">Offrez à votre équipe un outil centralisé pour gérer, suivre et développer votre activité en toute simplicité.</p>
          <a href="/login" class="btn btn-primary btn-lg">Commencer maintenant</a>
        </div>
      </div>
    </div>
  </section>
</body>
</html>
