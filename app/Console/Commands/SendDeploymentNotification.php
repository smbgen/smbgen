<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendDeploymentNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deploy:notify {--commits=10 : Number of recent commits to include}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send deployment notification email with git commit history';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $adminEmail = config('business.contact.email') ?: config('mail.from.address');
        $commitCount = $this->option('commits');

        if (! $adminEmail) {
            $this->error('No admin email configured. Set business.contact.email or mail.from.address');

            return Command::FAILURE;
        }

        $this->info('Gathering deployment information...');

        // Get git information (if available)
        $gitAvailable = $this->isGitAvailable();
        $currentBranch = $gitAvailable ? trim(shell_exec('git rev-parse --abbrev-ref HEAD') ?: '') : '';
        $currentCommit = $gitAvailable ? trim(shell_exec('git rev-parse --short HEAD') ?: '') : '';
        $commitHistory = $gitAvailable ? $this->getCommitHistory($commitCount) : [];

        // Format deployment time with timezone
        $timezone = config('app.timezone', 'UTC');
        $deploymentTime = now($timezone)->format('M j, Y \a\t g:i A');
        $deploymentTimezone = now($timezone)->format('T (P)');
        $environment = config('app.env');

        $this->info('Sending notification to: '.$adminEmail);

        try {
            Mail::html(
                view('emails.deployment-notification', [
                    'branch' => $currentBranch,
                    'commit' => $currentCommit,
                    'commitHistory' => $commitHistory,
                    'deploymentTime' => $deploymentTime,
                    'deploymentTimezone' => $deploymentTimezone,
                    'environment' => $environment,
                    'appName' => config('app.name'),
                    'appUrl' => config('app.url'),
                    'gitAvailable' => $gitAvailable,
                ])->render(),
                function ($message) use ($adminEmail, $environment) {
                    $message->to($adminEmail)
                        ->subject('🚀 Deployed to '.strtoupper($environment));
                }
            );

            $this->info('✅ Deployment notification sent successfully!');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed to send notification: '.$e->getMessage());

            return Command::FAILURE;
        }
    }

    /**
     * Check if git is available
     */
    private function isGitAvailable(): bool
    {
        $output = shell_exec('git --version 2>&1');

        return $output && str_contains(strtolower($output), 'git version');
    }

    /**
     * Get git commit history
     */
    private function getCommitHistory(int $count): array
    {
        // Use double quotes for Windows compatibility
        $gitLog = shell_exec("git log -n {$count} --pretty=format:\"%h|%an|%ar|%s\" 2>&1");

        if (! $gitLog || str_contains($gitLog, 'fatal') || str_contains($gitLog, 'not a git repository')) {
            return [];
        }

        $commits = [];
        $lines = explode("\n", trim($gitLog));

        foreach ($lines as $line) {
            if (empty(trim($line))) {
                continue;
            }

            $parts = explode('|', $line, 4);
            if (count($parts) === 4) {
                $commits[] = [
                    'hash' => $parts[0],
                    'author' => $parts[1],
                    'time' => $parts[2],
                    'message' => $parts[3],
                ];
            }
        }

        return $commits;
    }
}
