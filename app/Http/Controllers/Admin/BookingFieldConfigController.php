<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookingFieldConfig;
use Illuminate\Http\Request;

class BookingFieldConfigController extends Controller
{
    public function edit()
    {
        $config = BookingFieldConfig::getConfig();

        return view('admin.booking-fields.edit', compact('config'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'show_phone' => 'boolean',
            'require_phone' => 'boolean',
            'show_property_address' => 'boolean',
            'require_property_address' => 'boolean',
            'show_notes' => 'boolean',
            'require_notes' => 'boolean',
            'custom_fields' => 'nullable|json',
            'send_admin_notifications' => 'boolean',
            'admin_notification_email' => 'nullable|email',
        ]);

        // Convert custom_fields JSON string to array if present
        if (isset($validated['custom_fields'])) {
            $validated['custom_fields'] = json_decode($validated['custom_fields'], true);
        }

        $config = BookingFieldConfig::getConfig();
        $config->update($validated);

        return redirect()->route('admin.booking-fields.edit')
            ->with('success', 'Booking form fields updated successfully!');
    }
}
