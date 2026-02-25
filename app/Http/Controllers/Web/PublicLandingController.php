<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Landing;

class PublicLandingController extends Controller
{
    public function show(string $slug)
    {
        $landing = Landing::where('slug', $slug)
            ->where('status', 'published')
            ->whereHas('pressing', fn ($q) => $q->where('module_landing_enabled', true))
            ->with('pressing')
            ->firstOrFail();

        $sections = $landing->sections()
            ->where('is_visible', true)
            ->orderBy('position')
            ->get()
            ->pluck('section_key')
            ->all();

        return view('landing-public.show', [
            'pressing' => $landing->pressing,
            'landing' => $landing,
            'sections' => $sections,
        ]);
    }
}
