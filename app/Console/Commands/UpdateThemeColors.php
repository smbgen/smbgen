<?php

namespace App\Console\Commands;

use App\Models\CmsCompanyColors;
use Illuminate\Console\Command;

class UpdateThemeColors extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cms:update-theme-colors {--force : Force update even if colors exist}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update CMS theme colors with proper body_background_color values from presets';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $colors = CmsCompanyColors::first();

        if (! $colors) {
            $this->info('No theme colors found. Creating default theme...');
            $colors = CmsCompanyColors::getSettings();
        }

        $currentPreset = $colors->theme_preset ?? 'default';
        $presets = CmsCompanyColors::getThemePresets();

        if (! isset($presets[$currentPreset])) {
            $this->warn("Current preset '{$currentPreset}' not found. Using 'default'.");
            $currentPreset = 'default';
        }

        $preset = $presets[$currentPreset];

        $this->info("Updating theme colors to '{$preset['name']}' preset...");

        $colors->update([
            'primary_color' => $preset['primary'],
            'secondary_color' => $preset['secondary'],
            'background_color' => $preset['background'],
            'body_background_color' => $preset['body_background'],
            'text_color' => $preset['text'],
            'accent_color' => $preset['accent'],
            'theme_preset' => $currentPreset,
        ]);

        $this->newLine();
        $this->line('<fg=green>✓</> Theme colors updated successfully!');
        $this->newLine();
        $this->table(
            ['Setting', 'Value'],
            [
                ['Theme Preset', $preset['name']],
                ['Primary Color', $preset['primary']],
                ['Secondary Color', $preset['secondary']],
                ['Background Color (Navbar)', $preset['background']],
                ['Body Background Color', $preset['body_background']],
                ['Text Color', $preset['text']],
                ['Accent Color', $preset['accent']],
            ]
        );

        return Command::SUCCESS;
    }
}
