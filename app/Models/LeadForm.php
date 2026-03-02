<?php

// app/Models/LeadForm.php

namespace App\Models;

use App\Mail\NewLeadNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Mail;

class LeadForm extends Model
{
    use HasFactory;

    protected $fillable = ['cms_page_id', 'name', 'email', 'message', 'source_site', 'notification_email', 'ip_address', 'user_agent', 'referer', 'form_data'];

    protected function casts(): array
    {
        return [
            'form_data' => 'array',
        ];
    }

    public function cmsPage(): BelongsTo
    {
        return $this->belongsTo(CmsPage::class);
    }

    /**
     * Bootstrap the model and register event handlers
     */
    protected static function boot(): void
    {
        parent::boot();

        static::created(function (LeadForm $lead) {
            // Send email to admins who have opted in
            $admins = User::where('role', User::ROLE_ADMINISTRATOR)
                ->where('notify_on_new_leads', true)
                ->get();

            foreach ($admins as $admin) {
                Mail::to($admin->email)->send(new NewLeadNotification($lead));
            }
        });
    }
}
