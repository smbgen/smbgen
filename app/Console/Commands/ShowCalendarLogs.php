<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ShowCalendarLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calendar:show-logs {--lines=200 : Number of log lines to display}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display recent Google Calendar and Booking related logs (non-interactive)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $logPath = storage_path('logs/laravel.log');

        if (! File::exists($logPath)) {
            $this->error("Log file not found: {$logPath}");

            return 1;
        }

        $linesToRead = max(1, (int) $this->option('lines'));

        // Read the last N lines of the log file using a fixed-size buffer (no shell_exec)
        $buffer = [];
        try {
            $file = new \SplFileObject($logPath, 'r');
            $file->setFlags(\SplFileObject::DROP_NEW_LINE);
            foreach ($file as $line) {
                if ($line === false) {
                    continue;
                }
                $buffer[] = $line;
                if (count($buffer) > $linesToRead) {
                    array_shift($buffer);
                }
            }
        } catch (\Throwable $e) {
            $this->error('Failed to read log file: '.$e->getMessage());

            return 1;
        }

        // Filter for GoogleCalendar, Booking, or GoogleCredential related logs
        $lines = $buffer;
        $filteredLines = [];
        $currentEntry = [];

        foreach ($lines as $line) {
            // Check if line starts a new log entry (Laravel log format: [timestamp] environment.level: message)
            if (preg_match('/^\[\d{4}-\d{2}-\d{2}/', $line)) {
                // Process previous entry
                if (! empty($currentEntry)) {
                    $entryText = implode("\n", $currentEntry);
                    if ($this->matchesFilter($entryText)) {
                        $filteredLines[] = $entryText;
                    }
                }
                // Start new entry
                $currentEntry = [$line];
            } else {
                // Continue current entry (multi-line log)
                $currentEntry[] = $line;
            }
        }

        // Process last entry
        if (! empty($currentEntry)) {
            $entryText = implode("\n", $currentEntry);
            if ($this->matchesFilter($entryText)) {
                $filteredLines[] = $entryText;
            }
        }

        if (empty($filteredLines)) {
            $this->warn('No Google Calendar or Booking related logs found in the last '.$linesToRead.' lines');
            $this->info('Try increasing --lines parameter or check if bookings are being made');

            return 0;
        }

        $this->info('=== Google Calendar & Booking Logs ===');
        $this->line('');

        foreach ($filteredLines as $entry) {
            $this->line($entry);
            $this->line('---');
        }

        $this->info(sprintf('Found %d relevant log entries', count($filteredLines)));

        return 0;
    }

    /**
     * Check if log entry matches our filter criteria
     */
    protected function matchesFilter(string $text): bool
    {
        return preg_match('/\[GoogleCalendar\]|\[Booking\]|\[GoogleCredential\]|google.calendar|GoogleCalendar|BookingController/i', $text);
    }
}
