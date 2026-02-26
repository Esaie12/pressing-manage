# Variables disponibles dans les templates Landing

Les templates Blade de landing reçoivent **3 variables principales**:

## 1) `$landing`
Instance de `App\Models\Landing` (configuration du mini-site).

Champs courants utilisés dans les templates:
- `name` : nom affiché de la landing.
- `slug` : slug de l'URL publique.
- `tagline` : accroche.
- `primary_color` : couleur principale.
- `secondary_color` : couleur secondaire.
- `whatsapp_number` : numéro WhatsApp (format libre, nettoyé côté affichage lien).
- `contact_email` : email de contact.
- `template_key` : template sélectionné (`minimal_clean`, `minimal_business`, `minimal_modern`).
- `status` : état (`draft`, `published`).
- `meta_title` : titre SEO.
- `meta_description` : description SEO.
- `hero_title` : titre bloc Hero.
- `hero_subtitle` : sous-titre Hero.
- `about_title` : titre section À propos.
- `about_body` : contenu section À propos.
- `contact_title` : titre section Contact.
- `footer_text` : texte de pied de page.

## 2) `$pressing`
Instance de `App\Models\Pressing` (données opérationnelles du pressing).

Champs fréquemment utilisés:
- `name`
- `phone`
- `address`

## 3) `$sections`
Tableau des clés de sections visibles (ordonnées) issues de `landing_sections`.

Exemples:
- `hero`
- `about`
- `services`
- `gallery`
- `testimonials`
- `faq`
- `contact`
- `map`
- `footer`

Utilisation type dans Blade:
```blade
@if(in_array('hero', $sections, true))
  ...
@endif
```

## Mapping template_key -> fichier
- `minimal_clean` → `resources/views/landing-public/templates/minimal_clean.blade.php`
- `minimal_business` → `resources/views/landing-public/templates/minimal_business.blade.php`
- `minimal_modern` → `resources/views/landing-public/templates/minimal_modern.blade.php`
