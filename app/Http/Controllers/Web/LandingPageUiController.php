<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Landing;
use App\Models\LandingSection;
use App\Models\OwnerSubscription;
use App\Models\Pressing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LandingPageUiController extends Controller
{
    private array $defaultSections = [
        'hero',
        'about',
        'services',
        'gallery',
        'testimonials',
        'faq',
        'contact',
        'map',
        'footer',
    ];

    private array $allowedTabs = ['general', 'template', 'sections', 'seo', 'publication'];

    public function index(Request $request, ?string $tab = null)
    {
        $pressing = Pressing::findOrFail(Auth::user()->pressing_id);
        abort_if(! $pressing->module_landing_enabled, 403, 'Module Landing non activé.');

        $landing = $this->getOrCreateLanding($pressing);
        $activeTab = $tab && in_array($tab, $this->allowedTabs, true) ? $tab : 'general';

        return view('owner.landing.index', [
            'pressing' => $pressing,
            'landing' => $landing,
            'sections' => $landing->sections,
            'activeTab' => $activeTab,
        ]);
    }

    public function toggle()
    {
        $pressing = Pressing::findOrFail(Auth::user()->pressing_id);

        if (! $this->planAllows($pressing->id, 'allow_landing_module')) {
            return redirect()->route('owner.ui.dashboard')->with('error', 'Votre pack ne permet pas le module Landing Page.');
        }

        $next = ! $pressing->module_landing_enabled;
        $pressing->update(['module_landing_enabled' => $next]);

        if ($next) {
            $this->getOrCreateLanding($pressing);
        }

        return redirect()->route('owner.ui.dashboard')->with('success', $next ? 'Module Landing activé.' : 'Module Landing désactivé.');
    }

    public function updateSettings(Request $request)
    {
        $pressing = Pressing::findOrFail(Auth::user()->pressing_id);
        abort_if(! $pressing->module_landing_enabled, 403);

        $landing = $this->getOrCreateLanding($pressing);
        $section = $request->input('form_section', 'general');

        $rules = match ($section) {
            'general' => [
                'name' => ['required', 'string', 'max:255'],
                'slug' => ['required', 'alpha_dash', 'max:100', 'unique:landings,slug,'.$landing->id],
                'tagline' => ['nullable', 'string', 'max:255'],
                'whatsapp_number' => ['nullable', 'string', 'max:30'],
                'contact_email' => ['nullable', 'email', 'max:255'],
            ],
            'template' => [
                'template_key' => ['required', 'in:minimal_clean,minimal_business,minimal_modern'],
                'primary_color' => ['nullable', 'string', 'max:20'],
                'secondary_color' => ['nullable', 'string', 'max:20'],
            ],
            'seo' => [
                'meta_title' => ['nullable', 'string', 'max:255'],
                'meta_description' => ['nullable', 'string', 'max:300'],
            ],
            'publication' => [
                'status' => ['required', 'in:draft,published'],
                'hero_title' => ['nullable', 'string', 'max:255'],
                'hero_subtitle' => ['nullable', 'string', 'max:255'],
                'about_title' => ['nullable', 'string', 'max:255'],
                'about_body' => ['nullable', 'string', 'max:1000'],
                'contact_title' => ['nullable', 'string', 'max:255'],
                'footer_text' => ['nullable', 'string', 'max:255'],
            ],
            default => [],
        };

        if ($rules === []) {
            return redirect()->route('owner.ui.landing.tab', ['tab' => 'general'])->with('error', 'Section invalide.');
        }

        $data = $request->validate($rules);

        if ($section === 'template') {
            $data['primary_color'] = $data['primary_color'] ?: '#0d6efd';
            $data['secondary_color'] = $data['secondary_color'] ?: '#20c997';
        }

        $landing->update($data);

        return redirect()->route('owner.ui.landing.tab', ['tab' => $section])->with('success', 'Landing page mise à jour.');
    }

    public function updateSections(Request $request)
    {
        $pressing = Pressing::findOrFail(Auth::user()->pressing_id);
        abort_if(! $pressing->module_landing_enabled, 403);

        $landing = $this->getOrCreateLanding($pressing);

        $rows = $request->validate([
            'sections' => ['required', 'array'],
            'sections.*.section_key' => ['required', 'string'],
            'sections.*.position' => ['required', 'integer', 'min:1', 'max:20'],
            'sections.*.is_visible' => ['nullable', 'boolean'],
        ])['sections'];

        foreach ($rows as $row) {
            if (! in_array($row['section_key'], $this->defaultSections, true)) {
                continue;
            }

            LandingSection::updateOrCreate(
                ['landing_id' => $landing->id, 'section_key' => $row['section_key']],
                ['position' => $row['position'], 'is_visible' => (bool) ($row['is_visible'] ?? false)]
            );
        }

        return redirect()->route('owner.ui.landing.tab', ['tab' => 'sections'])->with('success', 'Sections mises à jour.');
    }

    private function getOrCreateLanding(Pressing $pressing): Landing
    {
        $landing = Landing::firstOrCreate(
            ['pressing_id' => $pressing->id],
            [
                'slug' => $this->makeUniqueSlug($pressing->name),
                'name' => $pressing->name,
                'template_key' => 'minimal_clean',
                'status' => 'draft',
                'hero_title' => $pressing->name,
                'contact_title' => 'Contact',
            ]
        );

        foreach ($this->defaultSections as $index => $key) {
            LandingSection::firstOrCreate(
                ['landing_id' => $landing->id, 'section_key' => $key],
                ['position' => $index + 1, 'is_visible' => true]
            );
        }

        return $landing->load(['sections']);
    }

    private function makeUniqueSlug(string $name): string
    {
        $base = str($name)->slug()->limit(90, '')->toString() ?: 'pressing';
        $slug = $base;
        $i = 1;
        while (Landing::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i;
            $i++;
        }

        return $slug;
    }

    private function planAllows(int $pressingId, string $feature): bool
    {
        $subscription = OwnerSubscription::where('pressing_id', $pressingId)
            ->where('is_active', true)
            ->whereDate('ends_at', '>=', now()->toDateString())
            ->with('plan')
            ->latest('ends_at')
            ->first();

        $plan = $subscription?->plan;

        return (bool) ($plan->{$feature} ?? true);
    }
}
