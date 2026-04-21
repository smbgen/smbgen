<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessSetting;
use App\Models\CmsCompanyColors;
use App\Models\CmsPage;
use App\Services\AI\ClaudeAIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SetupWizardController extends Controller
{
    /**
     * Display the setup wizard.
     */
    public function index()
    {
        $wizardProgress = $this->getWizardProgress();
        $currentStep = $wizardProgress['current_step'];

        return view('admin.setup-wizard.index', compact('wizardProgress', 'currentStep'));
    }

    /**
     * Show a specific step of the wizard.
     */
    public function show(string $step)
    {
        $validSteps = ['business', 'theme', 'first-page', 'navigation', 'integrations', 'complete'];

        if (! in_array($step, $validSteps)) {
            abort(404);
        }

        $wizardProgress = $this->getWizardProgress();

        return view("admin.setup-wizard.steps.{$step}", compact('wizardProgress'));
    }

    /**
     * Save business information step.
     */
    public function saveBusiness(Request $request)
    {
        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'industry' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            DB::transaction(function () use ($validated) {
                BusinessSetting::set('company_name', $validated['company_name'], 'string');
                BusinessSetting::set('industry', $validated['industry'] ?? '', 'string');
                BusinessSetting::set('company_description', $validated['description'] ?? '', 'text');
                BusinessSetting::set('setup_wizard_business', true, 'boolean');
            });

            return response()->json([
                'success' => true,
                'message' => 'Business information saved successfully',
                'next_step' => 'theme',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save business information: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Save theme configuration step.
     */
    public function saveTheme(Request $request)
    {
        $validated = $request->validate([
            'primary_color' => ['required', 'string', 'regex:/^#[0-9A-F]{6}$/i'],
            'secondary_color' => ['nullable', 'string', 'regex:/^#[0-9A-F]{6}$/i'],
            'preset' => ['nullable', 'string', 'in:default,smbgen,modern,nature,corporate'],
        ]);

        try {
            $colors = CmsCompanyColors::firstOrNew([]);
            $colors->primary_color = $validated['primary_color'];
            $colors->secondary_color = $validated['secondary_color'] ?? $validated['primary_color'];

            if ($validated['preset'] ?? null) {
                $colors->applyPreset($validated['preset']);
            }

            $colors->save();

            BusinessSetting::set('setup_wizard_theme', true, 'boolean');

            return response()->json([
                'success' => true,
                'message' => 'Theme configured successfully',
                'next_step' => 'first-page',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save theme: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate first CMS page using AI.
     */
    public function generateFirstPage(Request $request)
    {
        $validated = $request->validate([
            'page_type' => ['required', 'string', 'in:home,landing,about,services'],
            'use_ai' => ['boolean'],
            'custom_content' => ['nullable', 'string'],
        ]);

        try {
            $content = $validated['custom_content'] ?? '';

            // If AI is enabled and requested, generate content
            if (($validated['use_ai'] ?? false) && config('ai.enabled')) {
                $companyName = BusinessSetting::get('company_name', config('app.company_name'));
                $industry = BusinessSetting::get('industry', '');

                $prompt = "Create a {$validated['page_type']} page for {$companyName}";
                if ($industry) {
                    $prompt .= ", a company in the {$industry} industry";
                }

                $aiService = app(ClaudeAIService::class);
                $content = $aiService->generateLandingPage($prompt);
            }

            // Create the first CMS page
            $page = CmsPage::create([
                'title' => ucfirst($validated['page_type']).' Page',
                'slug' => $validated['page_type'] === 'home' ? 'home' : $validated['page_type'],
                'content' => $content,
                'is_published' => true,
                'show_navbar' => true,
                'show_footer' => true,
            ]);

            BusinessSetting::set('setup_wizard_first_page', true, 'boolean');

            return response()->json([
                'success' => true,
                'message' => 'First page created successfully',
                'page_id' => $page->id,
                'next_step' => 'navigation',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create page: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Skip a specific step.
     */
    public function skipStep(Request $request)
    {
        $validated = $request->validate([
            'step' => ['required', 'string'],
        ]);

        BusinessSetting::set("setup_wizard_{$validated['step']}", 'skipped', 'string');

        return response()->json([
            'success' => true,
            'message' => 'Step skipped',
        ]);
    }

    /**
     * Mark wizard as complete and dismiss it.
     */
    public function complete(Request $request)
    {
        BusinessSetting::set('setup_wizard_completed', true, 'boolean');
        BusinessSetting::set('setup_wizard_completed_at', now()->toDateTimeString(), 'string');

        return response()->json([
            'success' => true,
            'message' => 'Setup wizard completed!',
            'redirect' => route('admin.dashboard'),
        ]);
    }

    /**
     * Dismiss the setup wizard without completing.
     */
    public function dismiss(Request $request)
    {
        BusinessSetting::set('setup_wizard_dismissed', true, 'boolean');
        BusinessSetting::set('setup_wizard_dismissed_at', now()->toDateTimeString(), 'string');

        return redirect()->route('admin.dashboard')->with('info', 'Setup wizard dismissed. You can access setup options from Settings.');
    }

    /**
     * Get wizard progress data.
     */
    private function getWizardProgress(): array
    {
        $steps = [
            'business' => BusinessSetting::get('setup_wizard_business', false),
            'theme' => BusinessSetting::get('setup_wizard_theme', false),
            'first_page' => BusinessSetting::get('setup_wizard_first_page', false),
            'navigation' => BusinessSetting::get('setup_wizard_navigation', false),
            'integrations' => BusinessSetting::get('setup_wizard_integrations', false),
        ];

        $completed = array_filter($steps, fn ($val) => $val === true);
        $total = count($steps);
        $completedCount = count($completed);
        $percentage = $total > 0 ? round(($completedCount / $total) * 100) : 0;

        // Determine current step (first incomplete or last if all done)
        $currentStep = 'business';
        foreach ($steps as $step => $done) {
            if (! $done) {
                $currentStep = $step;
                break;
            }
        }

        return [
            'steps' => $steps,
            'completed_count' => $completedCount,
            'total_count' => $total,
            'percentage' => $percentage,
            'current_step' => $currentStep,
            'is_completed' => BusinessSetting::get('setup_wizard_completed', false),
            'is_dismissed' => BusinessSetting::get('setup_wizard_dismissed', false),
        ];
    }
}
