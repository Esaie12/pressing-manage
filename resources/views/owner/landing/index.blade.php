@extends('layouts.app')
@section('title','Owner - Landing Page')
@section('heading','Owner • Landing Page')
@section('content')
<div class="row g-3">
  <div class="col-lg-3">
    <div class="list-group shadow-sm">
      <span class="list-group-item active">CMS Landing</span>
      <span class="list-group-item">Paramètres généraux</span>
      <span class="list-group-item">Choix du template</span>
      <span class="list-group-item">Sections</span>
      <span class="list-group-item">SEO</span>
      <span class="list-group-item">Publication</span>
    </div>
    <div class="card shadow-sm mt-3">
      <div class="card-body small">
        <div><strong>URL publique</strong></div>
        <a href="{{ route('landing.public.show', ['slug' => $landing->slug]) }}" target="_blank">{{ route('landing.public.show', ['slug' => $landing->slug]) }}</a>
      </div>
    </div>
  </div>

  <div class="col-lg-9">
    <div class="card shadow-sm mb-3">
      <div class="card-header">Paramètres</div>
      <div class="card-body">
        <form method="POST" action="{{ route('owner.ui.landing.settings.update') }}" class="row g-3">
          @csrf
          <div class="col-md-6"><label class="form-label">Nom du pressing</label><input class="form-control" name="name" value="{{ old('name', $landing->name) }}" required></div>
          <div class="col-md-6"><label class="form-label">Slug URL</label><input class="form-control" name="slug" value="{{ old('slug', $landing->slug) }}" required></div>
          <div class="col-md-12"><label class="form-label">Accroche</label><input class="form-control" name="tagline" value="{{ old('tagline', $landing->tagline) }}"></div>
          <div class="col-md-6"><label class="form-label">Couleur principale</label><input type="color" class="form-control form-control-color" name="primary_color" value="{{ old('primary_color', $landing->primary_color ?? '#0d6efd') }}"></div>
          <div class="col-md-6"><label class="form-label">Couleur secondaire</label><input type="color" class="form-control form-control-color" name="secondary_color" value="{{ old('secondary_color', $landing->secondary_color ?? '#20c997') }}"></div>

          <div class="col-md-6"><label class="form-label">WhatsApp</label><input class="form-control" name="whatsapp_number" value="{{ old('whatsapp_number', $landing->whatsapp_number) }}"></div>
          <div class="col-md-6"><label class="form-label">Email contact</label><input type="email" class="form-control" name="contact_email" value="{{ old('contact_email', $landing->contact_email) }}"></div>

          <div class="col-md-6"><label class="form-label">Template</label>
            <select class="form-select" name="template_key" required>
              <option value="minimal_clean" @selected(old('template_key', $landing->template_key)==='minimal_clean')>Template A - minimal clean</option>
              <option value="minimal_business" @selected(old('template_key', $landing->template_key)==='minimal_business')>Template B - minimal business</option>
              <option value="minimal_modern" @selected(old('template_key', $landing->template_key)==='minimal_modern')>Template C - minimal modern</option>
            </select>
          </div>
          <div class="col-md-6"><label class="form-label">Publication</label>
            <select class="form-select" name="status" required>
              <option value="draft" @selected(old('status', $landing->status)==='draft')>Brouillon</option>
              <option value="published" @selected(old('status', $landing->status)==='published')>Publié</option>
            </select>
          </div>

          <div class="col-md-6"><label class="form-label">SEO title</label><input class="form-control" name="meta_title" value="{{ old('meta_title', $landing->meta_title) }}"></div>
          <div class="col-md-6"><label class="form-label">SEO description</label><input class="form-control" name="meta_description" value="{{ old('meta_description', $landing->meta_description) }}"></div>

          <div class="col-md-6"><label class="form-label">Hero title</label><input class="form-control" name="hero_title" value="{{ old('hero_title', $landing->hero_title) }}"></div>
          <div class="col-md-6"><label class="form-label">Hero subtitle</label><input class="form-control" name="hero_subtitle" value="{{ old('hero_subtitle', $landing->hero_subtitle) }}"></div>
          <div class="col-md-6"><label class="form-label">Titre À propos</label><input class="form-control" name="about_title" value="{{ old('about_title', $landing->about_title) }}"></div>
          <div class="col-md-6"><label class="form-label">Titre Contact</label><input class="form-control" name="contact_title" value="{{ old('contact_title', $landing->contact_title) }}"></div>
          <div class="col-md-12"><label class="form-label">Texte À propos</label><textarea class="form-control" rows="3" name="about_body">{{ old('about_body', $landing->about_body) }}</textarea></div>
          <div class="col-md-12"><label class="form-label">Footer</label><input class="form-control" name="footer_text" value="{{ old('footer_text', $landing->footer_text) }}"></div>

          <div class="col-12"><button class="btn btn-primary">Enregistrer</button></div>
        </form>
      </div>
    </div>

    <div class="card shadow-sm">
      <div class="card-header">Sections visibles et ordre</div>
      <div class="card-body">
        <form method="POST" action="{{ route('owner.ui.landing.sections.update') }}" class="row g-2">
          @csrf
          @foreach($sections as $index => $section)
            <input type="hidden" name="sections[{{ $index }}][section_key]" value="{{ $section->section_key }}">
            <div class="col-md-6">
              <div class="border rounded p-2 d-flex align-items-center justify-content-between">
                <div>
                  <strong>{{ ucfirst($section->section_key) }}</strong>
                  <div class="small text-muted">Réglez visibilité et position</div>
                </div>
                <div class="d-flex gap-2 align-items-center">
                  <input type="number" min="1" max="20" class="form-control" style="width:80px" name="sections[{{ $index }}][position]" value="{{ $section->position }}">
                  <div class="form-check form-switch m-0">
                    <input type="hidden" name="sections[{{ $index }}][is_visible]" value="0">
                    <input class="form-check-input" type="checkbox" name="sections[{{ $index }}][is_visible]" value="1" @checked($section->is_visible)>
                  </div>
                </div>
              </div>
            </div>
          @endforeach
          <div class="col-12 mt-2"><button class="btn btn-outline-primary">Mettre à jour sections</button></div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
