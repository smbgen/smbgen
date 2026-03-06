<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingFieldConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'custom_fields',
        'show_phone',
        'require_phone',
        'show_property_address',
        'require_property_address',
        'show_notes',
        'require_notes',
        'send_admin_notifications',
        'admin_notification_email',
    ];

    protected function casts(): array
    {
        return [
            'custom_fields' => 'array',
            'show_phone' => 'boolean',
            'require_phone' => 'boolean',
            'show_property_address' => 'boolean',
            'require_property_address' => 'boolean',
            'show_notes' => 'boolean',
            'require_notes' => 'boolean',
            'send_admin_notifications' => 'boolean',
        ];
    }

    /**
     * Get the singleton configuration instance
     */
    public static function getConfig(): self
    {
        return static::firstOrCreate(
            ['id' => 1],
            [
                'show_phone' => true,
                'require_phone' => false,
                'show_property_address' => true,
                'require_property_address' => false,
                'show_notes' => true,
                'require_notes' => false,
                'custom_fields' => [],
                'send_admin_notifications' => true,
                'admin_notification_email' => null,
            ]
        );
    }

    /**
     * Get all fields (built-in + custom) with their configuration
     */
    public function getAllFields(): array
    {
        $fields = [];

        // Built-in always-required fields
        $fields[] = [
            'name' => 'name',
            'label' => 'Your Name',
            'type' => 'text',
            'required' => true,
            'built_in' => true,
        ];

        $fields[] = [
            'name' => 'email',
            'label' => 'Email',
            'type' => 'email',
            'required' => true,
            'built_in' => true,
        ];

        // Built-in toggleable fields
        if ($this->show_phone) {
            $fields[] = [
                'name' => 'phone',
                'label' => 'Phone',
                'type' => 'tel',
                'required' => $this->require_phone,
                'placeholder' => '(555) 123-4567',
                'built_in' => true,
            ];
        }

        if ($this->show_property_address) {
            $fields[] = [
                'name' => 'property_address',
                'label' => 'Property Address',
                'type' => 'textarea',
                'required' => $this->require_property_address,
                'placeholder' => '123 Main St, City, State ZIP',
                'rows' => 2,
                'built_in' => true,
            ];
        }

        if ($this->show_notes) {
            $fields[] = [
                'name' => 'notes',
                'label' => 'Notes',
                'type' => 'textarea',
                'required' => $this->require_notes,
                'placeholder' => 'Any additional details...',
                'rows' => 3,
                'built_in' => true,
            ];
        }

        // Custom fields
        foreach ($this->custom_fields ?? [] as $customField) {
            $fields[] = array_merge($customField, ['built_in' => false]);
        }

        return $fields;
    }
}
