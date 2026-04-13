@extends('layouts.admin')

@section('content')
    <!-- Header -->
    <div class="admin-page-header">
        <div>
            <h1 class="admin-page-title">Booking Form Configuration</h1>
            <p class="admin-page-subtitle">Customize which fields appear on the booking form</p>
        </div>
        <div class="action-buttons">
            <a href="{{ route('admin.bookings.index') }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Back to Bookings
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="max-w-5xl mx-auto">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl overflow-hidden border border-gray-200 dark:border-gray-700">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                
                <!-- Info Panel -->
                <div class="mb-6 bg-gray-100 dark:bg-gray-700/50 border border-gray-300 dark:border-gray-600 rounded-lg p-4">
                    <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-2 flex items-center gap-2">
                        <i class="fas fa-info-circle text-gray-600 dark:text-gray-400"></i>
                        About Booking Form Fields
                    </h3>
                    <div class="text-sm text-gray-700 dark:text-gray-300 space-y-1">
                        <p><strong class="text-gray-900 dark:text-gray-200">Required Fields:</strong> Name and Email are always required and cannot be toggled off.</p>
                        <p><strong class="text-gray-900 dark:text-gray-200">Toggleable Fields:</strong> Phone, Property Address, and Notes can be shown/hidden and made optional or required.</p>
                        <p><strong class="text-gray-900 dark:text-gray-200">Custom Fields:</strong> Add your own custom fields to collect additional information (e.g., Budget, Property Type, Referral Source).</p>
                    </div>
                </div>

                    <form action="{{ route('admin.booking-fields.update') }}" method="POST" x-data="formBuilder()">
                        @csrf
                        @method('PUT')

                        <!-- Always-Visible Fields -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 pb-2 border-b border-gray-300 dark:border-gray-700">
                                Required Fields (Always Visible)
                            </h3>
                            <div class="space-y-3">
                                <div class="bg-gray-100 dark:bg-gray-700/30 rounded-lg p-4 border border-gray-300 dark:border-gray-600">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h4 class="font-medium text-gray-900 dark:text-gray-100">Name</h4>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Customer's full name</p>
                                        </div>
                                        <span class="px-3 py-1 bg-gray-300 dark:bg-gray-600/50 text-gray-700 dark:text-gray-300 text-xs font-semibold rounded-full border border-gray-400 dark:border-gray-500">
                                            Required
                                        </span>
                                    </div>
                                </div>
                                <div class="bg-gray-100 dark:bg-gray-700/30 rounded-lg p-4 border border-gray-300 dark:border-gray-600">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h4 class="font-medium text-gray-900 dark:text-gray-100">Email</h4>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Customer's email address</p>
                                        </div>
                                        <span class="px-3 py-1 bg-gray-300 dark:bg-gray-600/50 text-gray-700 dark:text-gray-300 text-xs font-semibold rounded-full border border-gray-400 dark:border-gray-500">
                                            Required
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Toggleable Built-in Fields -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 pb-2 border-b border-gray-300 dark:border-gray-700">
                                Toggleable Fields
                            </h3>
                            <div class="space-y-4">
                                
                                <!-- Phone Field -->
                                <div class="bg-gray-100 dark:bg-gray-700/30 rounded-lg p-4 border border-gray-300 dark:border-gray-600">
                                    <div class="flex items-start justify-between mb-3">
                                        <div class="flex-1">
                                            <h4 class="font-medium text-gray-900 dark:text-gray-100">Phone Number</h4>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Customer's phone number</p>
                                        </div>
                                        <div class="flex items-center ml-4">
                                            <input type="hidden" name="show_phone" value="0">
                                            <input type="checkbox" name="show_phone" id="show_phone" value="1" 
                                                   {{ old('show_phone', $config->show_phone) ? 'checked' : '' }}
                                                   class="form-checkbox">
                                            <label for="show_phone" class="ml-2 text-sm font-medium text-gray-800 dark:text-gray-200">
                                                Show Field
                                            </label>
                                        </div>
                                    </div>
                                    <div class="ml-4 pl-4 border-l-2 border-gray-300 dark:border-gray-600">
                                        <input type="hidden" name="require_phone" value="0">
                                        <label class="flex items-center">
                                            <input type="checkbox" name="require_phone" id="require_phone" value="1" 
                                                   {{ old('require_phone', $config->require_phone) ? 'checked' : '' }}
                                                   class="form-checkbox">
                                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Make Required</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Property Address Field -->
                                <div class="bg-gray-100 dark:bg-gray-700/30 rounded-lg p-4 border border-gray-300 dark:border-gray-600">
                                    <div class="flex items-start justify-between mb-3">
                                        <div class="flex-1">
                                            <h4 class="font-medium text-gray-900 dark:text-gray-100">Property Address</h4>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Address of property for inspection/service</p>
                                        </div>
                                        <div class="flex items-center ml-4">
                                            <input type="hidden" name="show_property_address" value="0">
                                            <input type="checkbox" name="show_property_address" id="show_property_address" value="1" 
                                                   {{ old('show_property_address', $config->show_property_address) ? 'checked' : '' }}
                                                   class="form-checkbox">
                                            <label for="show_property_address" class="ml-2 text-sm font-medium text-gray-800 dark:text-gray-200">
                                                Show Field
                                            </label>
                                        </div>
                                    </div>
                                    <div class="ml-4 pl-4 border-l-2 border-gray-300 dark:border-gray-600">
                                        <input type="hidden" name="require_property_address" value="0">
                                        <label class="flex items-center">
                                            <input type="checkbox" name="require_property_address" id="require_property_address" value="1" 
                                                   {{ old('require_property_address', $config->require_property_address) ? 'checked' : '' }}
                                                   class="form-checkbox">
                                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Make Required</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Notes Field -->
                                <div class="bg-gray-100 dark:bg-gray-700/30 rounded-lg p-4 border border-gray-300 dark:border-gray-600">
                                    <div class="flex items-start justify-between mb-3">
                                        <div class="flex-1">
                                            <h4 class="font-medium text-gray-900 dark:text-gray-100">Notes / Comments</h4>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Additional details or special requests</p>
                                        </div>
                                        <div class="flex items-center ml-4">
                                            <input type="hidden" name="show_notes" value="0">
                                            <input type="checkbox" name="show_notes" id="show_notes" value="1" 
                                                   {{ old('show_notes', $config->show_notes) ? 'checked' : '' }}
                                                   class="form-checkbox">
                                            <label for="show_notes" class="ml-2 text-sm font-medium text-gray-800 dark:text-gray-200">
                                                Show Field
                                            </label>
                                        </div>
                                    </div>
                                    <div class="ml-4 pl-4 border-l-2 border-gray-300 dark:border-gray-600">
                                        <input type="hidden" name="require_notes" value="0">
                                        <label class="flex items-center">
                                            <input type="checkbox" name="require_notes" id="require_notes" value="1" 
                                                   {{ old('require_notes', $config->require_notes) ? 'checked' : '' }}
                                                   class="form-checkbox">
                                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Make Required</span>
                                        </label>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- Custom Fields Builder -->
                        <div class="mb-8">
                            <div class="flex justify-between items-center mb-4 pb-2 border-b border-gray-300 dark:border-gray-700">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Custom Fields</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Add additional fields to collect specific information</p>
                                </div>
                                <button type="button" @click="addField()" class="btn-primary text-sm">
                                    <i class="fas fa-plus mr-2"></i>Add Custom Field
                                </button>
                            </div>

                            <div class="space-y-3">
                                <template x-for="(field, index) in fields" :key="index">
                                    <div class="bg-gray-100 dark:bg-gray-700/30 border border-gray-300 dark:border-gray-600 rounded-lg p-4">
                                        <div class="grid grid-cols-12 gap-3">
                                            <!-- Field Type -->
                                            <div class="col-span-3">
                                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Type</label>
                                                <select x-model="field.type" class="w-full px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:border-gray-500 focus:ring-2 focus:ring-gray-500/20">
                                                    <option value="text">Text</option>
                                                    <option value="email">Email</option>
                                                    <option value="tel">Phone</option>
                                                    <option value="textarea">Textarea</option>
                                                    <option value="number">Number</option>
                                                    <option value="date">Date</option>
                                                    <option value="select">Select</option>
                                                    <option value="checkbox">Checkbox</option>
                                                </select>
                                            </div>

                                            <!-- Field Name -->
                                            <div class="col-span-3">
                                                  <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Name</label>
                                                <input type="text" x-model="field.name" placeholder="field_name" 
                                                      class="w-full px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-500 focus:border-gray-500 focus:ring-2 focus:ring-gray-500/20">
                                            </div>

                                            <!-- Field Label -->
                                            <div class="col-span-4">
                                                  <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Label</label>
                                                <input type="text" x-model="field.label" placeholder="Field Label" 
                                                      class="w-full px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-500 focus:border-gray-500 focus:ring-2 focus:ring-gray-500/20">
                                            </div>

                                            <!-- Required Checkbox -->
                                            <div class="col-span-1 flex items-end">
                                                <label class="flex items-center">
                                                    <input type="checkbox" x-model="field.required" class="form-checkbox">
                                                    <span class="ml-1 text-xs text-gray-600 dark:text-gray-400">Req</span>
                                                </label>
                                            </div>

                                            <!-- Delete Button -->
                                            <div class="col-span-1 flex items-end">
                                                <button type="button" @click="removeField(index)" class="btn-danger text-xs w-full py-1.5">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>

                                            <!-- Placeholder -->
                                            <div class="col-span-6">
                                                  <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Placeholder</label>
                                                <input type="text" x-model="field.placeholder" placeholder="Placeholder text" 
                                                      class="w-full px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-500 focus:border-gray-500 focus:ring-2 focus:ring-gray-500/20">
                                            </div>

                                            <!-- Options (for select) -->
                                            <div class="col-span-6" x-show="field.type === 'select'">
                                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Options (comma-separated)</label>
                                                <input type="text" x-model="field.options" placeholder="Option 1,Option 2,Option 3" 
                                                       class="w-full px-2 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-500 focus:border-gray-500 focus:ring-2 focus:ring-gray-500/20">
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <!-- Empty State -->
                                <div x-show="fields.length === 0" class="text-center py-8 text-gray-600 dark:text-gray-400">
                                    <i class="fas fa-inbox text-3xl mb-2"></i>
                                    <p>No custom fields yet. Click "Add Custom Field" to create one.</p>
                                </div>
                            </div>

                            <!-- Hidden input to store custom_fields JSON -->
                            <input type="hidden" name="custom_fields" :value="JSON.stringify(fields)">
                        </div>

                        <!-- Admin Notifications Section -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 pb-2 border-b border-gray-300 dark:border-gray-700">
                                <i class="fas fa-bell mr-2"></i>Admin Notifications
                            </h3>
                            <div class="space-y-4">
                                <div class="bg-gray-100 dark:bg-gray-700/30 rounded-lg p-4 border border-gray-300 dark:border-gray-600">
                                    <div class="flex items-start justify-between mb-3">
                                        <div class="flex-1">
                                            <h4 class="font-medium text-gray-900 dark:text-gray-100">Send Admin Notifications</h4>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Receive email notifications when new bookings or contact form submissions are received</p>
                                        </div>
                                        <div class="flex items-center ml-4">
                                            <input type="hidden" name="send_admin_notifications" value="0">
                                            <input type="checkbox" name="send_admin_notifications" id="send_admin_notifications" value="1" 
                                                   {{ old('send_admin_notifications', $config->send_admin_notifications ?? true) ? 'checked' : '' }}
                                                   class="form-checkbox">
                                            <label for="send_admin_notifications" class="ml-2 text-sm font-medium text-gray-800 dark:text-gray-200">
                                                Enable
                                            </label>
                                        </div>
                                    </div>
                                    <div class="ml-4 pl-4 border-l-2 border-gray-300 dark:border-gray-600">
                                        <label for="admin_notification_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Notification Email (Optional)
                                        </label>
                                        <input type="email" 
                                               name="admin_notification_email" 
                                               id="admin_notification_email" 
                                               value="{{ old('admin_notification_email', $config->admin_notification_email) }}"
                                               placeholder="admin@example.com"
                                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-500 focus:border-gray-500 focus:ring-2 focus:ring-gray-500/20">
                                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Leave empty to notify all admin users who have opted in</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end space-x-3 pt-6 border-t border-gray-300 dark:border-gray-700">
                            <a href="{{ route('admin.bookings.index') }}" class="btn-secondary">
                                <i class="fas fa-times mr-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn-primary">
                                <i class="fas fa-save mr-2"></i>Save Configuration
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function formBuilder() {
            return {
                fields: {!! old('custom_fields') ? old('custom_fields') : json_encode($config->custom_fields ?? []) !!},
                
                addField() {
                    this.fields.push({
                        type: 'text',
                        name: '',
                        label: '',
                        placeholder: '',
                        required: false,
                        options: ''
                    });
                },
                
                removeField(index) {
                    this.fields.splice(index, 1);
                }
            }
        }
    </script>
    @endpush
@endsection
