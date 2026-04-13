<?php

namespace App\Mail;

use App\Models\InspectionReport;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InspectionReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public InspectionReport $report;

    public function __construct(InspectionReport $report)
    {
        $this->report = $report;
    }

    public function build()
    {
        return $this->subject('Your Inspection Report: '.$this->report->summary_title)
            ->view('emails.inspection-report')
            ->with(['report' => $this->report]);
    }
}
