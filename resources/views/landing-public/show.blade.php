@php
    $templateView = match ($landing->template_key) {
        'minimal_business' => 'landing-public.templates.minimal_business',
        'minimal_modern' => 'landing-public.templates.minimal_modern',
        default => 'landing-public.templates.minimal_clean',
    };
@endphp

@include($templateView, ['landing' => $landing, 'pressing' => $pressing, 'sections' => $sections])
