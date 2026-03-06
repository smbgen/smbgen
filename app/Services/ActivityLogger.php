<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogger
{
    /**
     * Log an activity
     */
    public static function log(
        string $action,
        ?string $description = null,
        ?Model $subject = null,
        ?array $properties = null,
        ?int $userId = null
    ): ActivityLog {
        return ActivityLog::create([
            'user_id' => $userId ?? Auth::id(),
            'action' => $action,
            'description' => $description,
            'model_type' => $subject ? get_class($subject) : null,
            'model_id' => $subject?->id,
            'properties' => $properties,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Log user login
     */
    public static function logLogin(?int $userId = null): ActivityLog
    {
        return self::log(
            action: 'login',
            description: 'User logged in',
            userId: $userId ?? Auth::id()
        );
    }

    /**
     * Log user logout
     */
    public static function logLogout(?int $userId = null): ActivityLog
    {
        return self::log(
            action: 'logout',
            description: 'User logged out',
            userId: $userId ?? Auth::id()
        );
    }

    /**
     * Log file upload
     */
    public static function logFileUpload(Model $file, array $properties = []): ActivityLog
    {
        return self::log(
            action: 'file_upload',
            description: "Uploaded file: {$file->original_name}",
            subject: $file,
            properties: $properties
        );
    }

    /**
     * Log file download
     */
    public static function logFileDownload(Model $file): ActivityLog
    {
        return self::log(
            action: 'file_download',
            description: "Downloaded file: {$file->original_name}",
            subject: $file
        );
    }

    /**
     * Log file delete
     */
    public static function logFileDelete(Model $file): ActivityLog
    {
        return self::log(
            action: 'file_delete',
            description: "Deleted file: {$file->original_name}",
            subject: $file
        );
    }

    /**
     * Log client creation
     */
    public static function logClientCreate(Model $client): ActivityLog
    {
        return self::log(
            action: 'client_create',
            description: "Created client: {$client->name}",
            subject: $client
        );
    }

    /**
     * Log client update
     */
    public static function logClientUpdate(Model $client, array $changes = []): ActivityLog
    {
        return self::log(
            action: 'client_update',
            description: "Updated client: {$client->name}",
            subject: $client,
            properties: ['changes' => $changes]
        );
    }

    /**
     * Log client deletion
     */
    public static function logClientDelete(Model $client): ActivityLog
    {
        return self::log(
            action: 'client_delete',
            description: "Deleted client: {$client->name}",
            subject: $client
        );
    }

    /**
     * Log message send
     */
    public static function logMessageSend(Model $message): ActivityLog
    {
        return self::log(
            action: 'message_send',
            description: 'Sent message',
            subject: $message
        );
    }

    /**
     * Log booking creation
     */
    public static function logBookingCreate(Model $booking): ActivityLog
    {
        return self::log(
            action: 'booking_create',
            description: "Created booking for {$booking->customer_name}",
            subject: $booking
        );
    }

    /**
     * Log booking update
     */
    public static function logBookingUpdate(Model $booking): ActivityLog
    {
        return self::log(
            action: 'booking_update',
            description: "Updated booking for {$booking->customer_name}",
            subject: $booking
        );
    }

    /**
     * Log profile update
     */
    public static function logProfileUpdate(): ActivityLog
    {
        return self::log(
            action: 'profile_update',
            description: 'Updated profile'
        );
    }

    /**
     * Log password change
     */
    public static function logPasswordChange(?int $userId = null): ActivityLog
    {
        return self::log(
            action: 'password_change',
            description: 'Changed password',
            userId: $userId
        );
    }

    /**
     * Log account provisioning
     */
    public static function logAccountProvisioned(Model $client, array $properties = []): ActivityLog
    {
        return self::log(
            action: 'account_provisioned',
            description: "Account provisioned for {$client->name}",
            subject: $client,
            properties: $properties
        );
    }

    /**
     * Log account activation
     */
    public static function logAccountActivated(Model $client): ActivityLog
    {
        return self::log(
            action: 'account_activated',
            description: "Account activated for {$client->name} on first login",
            subject: $client
        );
    }

    /**
     * Log client import started
     */
    public static function logClientImportStarted(Model $import, array $properties = []): ActivityLog
    {
        return self::log(
            action: 'client_import_started',
            description: "Started client import: {$import->filename} ({$import->total_rows} rows)",
            subject: $import,
            properties: $properties
        );
    }

    /**
     * Log client import completed
     */
    public static function logClientImportCompleted(Model $import, int $successCount, int $failCount): ActivityLog
    {
        return self::log(
            action: 'client_import_completed',
            description: "Completed client import: {$import->filename} ({$successCount} succeeded, {$failCount} failed)",
            subject: $import,
            properties: [
                'filename' => $import->filename,
                'successful_imports' => $successCount,
                'failed_imports' => $failCount,
                'total_rows' => $import->total_rows,
            ]
        );
    }

    /**
     * Log client import failed
     */
    public static function logClientImportFailed(Model $import, string $error): ActivityLog
    {
        return self::log(
            action: 'client_import_failed',
            description: "Client import failed: {$import->filename} - {$error}",
            subject: $import,
            properties: [
                'filename' => $import->filename,
                'error' => $error,
                'total_rows' => $import->total_rows,
            ]
        );
    }
}
