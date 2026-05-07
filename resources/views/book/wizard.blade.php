@extends('layouts.public')

@push('head')
{{-- Alpine.js is loaded via Vite in app.js, no need for CDN --}}
@php
    $companyColors = \App\Models\CmsCompanyColors::getSettings();
@endphp
<style>
    /* Booking form theme colors */
    .booking-card {
        background-color: {{ $companyColors->background_color }}20 !important;
        border: 1px solid {{ $companyColors->background_color }}40 !important;
    }
    .booking-card h2,
    .booking-card label {
        color: {{ $companyColors->text_color }} !important;
    }
    .booking-input,
    .booking-select {
        background-color: {{ $companyColors->body_background_color }} !important;
        border-color: {{ $companyColors->background_color }}60 !important;
        color: {{ $companyColors->text_color }} !important;
    }
    .booking-input:focus,
    .booking-select:focus {
        border-color: {{ $companyColors->primary_color }} !important;
        ring-color: {{ $companyColors->primary_color }} !important;
    }
    .booking-text {
        color: {{ $companyColors->text_color }}CC !important;
    }
    .booking-text-muted {
        color: {{ $companyColors->text_color }}99 !important;
    }
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto p-4 sm:p-6">
    <!-- DEBUG PANEL -->
    @if(config('app.debug'))
    <div class="bg-yellow-50 border-2 border-yellow-400 rounded-lg p-4 mb-6 shadow-lg" style="background-color: #fefce8 !important; border-color: #facc15 !important;">
        <h3 class="font-bold text-lg mb-3" style="color: #854d0e !important;">🐛 DEBUG: Available Staff</h3>
        <div class="bg-white rounded p-3 space-y-2 text-sm font-mono border border-yellow-200" style="background-color: #ffffff !important; border-color: #fef3c7 !important;">
            <div style="color: #1f2937 !important;">Staff Found: <span class="font-bold" style="color: #854d0e !important;">{{ count($availableStaff) }}</span></div>
            @if(count($availableStaff) > 0)
                <div class="mt-3 space-y-2">
                    @foreach($availableStaff as $staff)
                        <div class="bg-yellow-50 rounded p-2 border border-yellow-200" style="background-color: #fefce8 !important; border-color: #fef3c7 !important;">
                            <div style="color: #1f2937 !important; font-weight: 600;">{{ $staff->name }} (ID: {{ $staff->id }})</div>
                            <div class="text-xs" style="color: #78716c !important;">Email: {{ $staff->email }}</div>
                            <div class="text-xs" style="color: #78716c !important;">
                                Calendar: {{ $staff->googleCredential ? '✓ Connected' : '✗ Not Connected' }}
                            </div>
                            <div class="text-xs" style="color: #78716c !important;">
                                Availabilities: {{ $staff->availabilities->count() }} days configured
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="mt-2 font-bold" style="color: #dc2626 !important;">⚠️ NO STAFF MEMBERS FOUND!</div>
                <div class="text-xs mt-1" style="color: #991b1b !important;">
                    Staff must have:<br>
                    • Role: company_administrator<br>
                    • Google Calendar connected (refresh token)<br>
                    • At least one availability configured
                </div>
            @endif
        </div>
        <div class="mt-3 text-xs" style="color: #854d0e !important;">
            This debug panel is only visible when APP_DEBUG=true
        </div>
    </div>
    @endif
    <!-- END DEBUG PANEL -->

    <!-- Header -->
    <div class="text-center mb-8">
        <h1 class="text-3xl md:text-5xl font-bold booking-text mb-3">Book an Appointment</h1>
        <p class="text-xl booking-text mb-2">with {{ config('app.name') }}</p>
        <p class="booking-text-muted">Select a day and time that works for you</p>
    </div>

    <form id="bookingForm" method="POST" action="{{ route('booking.book') }}">
        @csrf
        
        <!-- Staff Selection -->
        @if(count($availableStaff) > 0)
        <div class="booking-card rounded-lg shadow-lg p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">
                {{ count($availableStaff) === 1 ? 'Meeting With' : 'Select Staff Member' }}
            </h2>
            <div class="max-w-md">
                <label class="block text-sm font-medium mb-2">
                    {{ count($availableStaff) === 1 ? 'You will be meeting with' : 'Choose who you\'d like to meet with' }}
                </label>
                <select name="staff_id" id="staffSelector" class="booking-select w-full rounded-lg px-3 py-2" {{ count($availableStaff) === 1 ? 'disabled' : '' }}>
                    @if(count($availableStaff) > 1)
                        <option value="">Any available staff</option>
                    @endif
                    @foreach($availableStaff as $index => $staff)
                        <option value="{{ $staff->id }}" selected>{{ $staff->name }}</option>
                    @endforeach
                </select>
                @if(count($availableStaff) === 1)
                    <!-- Hidden input to ensure value is submitted when disabled -->
                    <input type="hidden" name="staff_id" value="{{ $availableStaff->first()->id }}" />
                @endif
                <p class="mt-2 text-xs booking-text-muted">
                    {{ count($availableStaff) === 1 ? 'Available times shown below' : 'Selecting a specific staff member will show only their availability' }}
                </p>
            </div>
        </div>
        @endif

        <!-- Timezone Selection -->
        <div class="booking-card rounded-lg shadow-lg p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Your Timezone</h2>
            <div class="max-w-md">
                <label class="block text-sm font-medium mb-2">Select your timezone to view available times</label>
                <select id="timezoneSelector" class="booking-input w-full rounded-lg px-3 py-2">
                    <option value="America/New_York">Eastern Time (ET)</option>
                    <option value="America/Chicago">Central Time (CT)</option>
                    <option value="America/Denver">Mountain Time (MT)</option>
                    <option value="America/Los_Angeles">Pacific Time (PT)</option>
                    <option value="America/Phoenix">Arizona (MST)</option>
                    <option value="America/Anchorage">Alaska Time (AKT)</option>
                    <option value="Pacific/Honolulu">Hawaii Time (HST)</option>
                    <option value="UTC">UTC</option>
                </select>
                <p class="mt-2 text-xs booking-text-muted">Times will be displayed in your selected timezone</p>
            </div>
        </div>
        
        <!-- Customer Information -->
        <div class="booking-card rounded-lg shadow-lg p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Your Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($formFields as $field)
                    @if($field['type'] === 'textarea')
                        <!-- Textarea fields span full width -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium mb-2">
                                {{ $field['label'] }} {{ $field['required'] ? '*' : '(optional)' }}
                            </label>
                            <textarea 
                                name="{{ $field['name'] }}" 
                                rows="{{ $field['rows'] ?? 3 }}" 
                                {{ $field['required'] ? 'required' : '' }}
                                class="booking-input w-full rounded-lg px-3 py-2" 
                                placeholder="{{ $field['placeholder'] ?? '' }}"></textarea>
                        </div>
                    @elseif($field['type'] === 'select')
                        <!-- Select fields -->
                        <div class="{{ in_array($field['name'], ['name', 'email']) ? '' : 'md:col-span-2' }}">
                            <label class="block text-sm font-medium mb-2">
                                {{ $field['label'] }} {{ $field['required'] ? '*' : '(optional)' }}
                            </label>
                            <select 
                                name="{{ $field['name'] }}" 
                                {{ $field['required'] ? 'required' : '' }}
                                class="booking-input w-full rounded-lg px-3 py-2">
                                <option value="">Select...</option>
                                @if(isset($field['options']) && $field['options'])
                                    @foreach(explode(',', $field['options']) as $option)
                                        <option value="{{ trim($option) }}">{{ trim($option) }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    @elseif($field['type'] === 'checkbox')
                        <!-- Checkbox fields -->
                        <div class="md:col-span-2">
                            <label class="flex items-center">
                                <input 
                                    type="checkbox" 
                                    name="{{ $field['name'] }}" 
                                    value="1"
                                    {{ $field['required'] ? 'required' : '' }}
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <span class="ml-2 text-sm font-medium" style="color: {{ $companyColors->text_color }};">
                                    {{ $field['label'] }} {{ $field['required'] ? '*' : '(optional)' }}
                                </span>
                            </label>
                        </div>
                    @else
                        <!-- Regular input fields (text, email, tel, number, date) -->
                        <div class="{{ in_array($field['name'], ['name', 'email']) ? '' : 'md:col-span-2' }}">
                            <label class="block text-sm font-medium mb-2">
                                {{ $field['label'] }} {{ $field['required'] ? '*' : '(optional)' }}
                            </label>
                            <input 
                                name="{{ $field['name'] }}" 
                                type="{{ $field['type'] }}" 
                                {{ $field['required'] ? 'required' : '' }}
                                class="booking-input w-full rounded-lg px-3 py-2" 
                                placeholder="{{ $field['placeholder'] ?? '' }}" />
                        </div>
                    @endif
                @endforeach
            </div>
        </div>

        <!-- Week View Calendar -->
        <div class="booking-card rounded-lg shadow-lg p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Select a Time</h2>
            
            <div id="loadingMessage" class="text-center py-12" style="color: {{ $companyColors->text_color }}99;">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 mb-3" style="border-color: {{ $companyColors->accent_color }};"></div>
                <p>Loading available times...</p>
            </div>

            <div id="noAvailability" class="hidden text-center py-12" style="color: {{ $companyColors->text_color }}99;">
                <p class="text-lg mb-2">No availability set yet.</p>
                <p class="text-sm">Please check back later or contact us directly.</p>
            </div>

            <div id="weeksContainer" class="hidden space-y-8"></div>

            <input type="hidden" name="slot" id="selectedSlot" required />
            <div id="selectionError" class="hidden text-red-500 text-sm mt-2">Please select a time slot</div>
        </div>

        <!-- Sticky Submit Section -->
        <div id="stickySubmit" class="fixed bottom-0 left-0 right-0 shadow-lg transition-transform duration-300 z-50" style="background-color: {{ $companyColors->background_color }}; border-top: 1px solid {{ $companyColors->background_color }}80;">
            <div class="max-w-7xl mx-auto px-4 py-4">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
                    <div id="selectedTimeDisplay" class="text-center sm:text-left hidden">
                        <p class="text-sm" style="color: {{ $companyColors->getNavbarTextColor() }}99;">Selected appointment:</p>
                        <p id="selectedTimeText" class="text-lg font-semibold" style="color: {{ $companyColors->getNavbarTextColor() }};"></p>
                    </div>
                    <div id="noSelectionText" style="color: {{ $companyColors->getNavbarTextColor() }}99;">
                        Please select a time slot to continue
                    </div>
                    <button type="submit" id="submitButton" class="disabled:opacity-50 disabled:cursor-not-allowed font-semibold px-4 py-2 rounded-lg transition-all whitespace-nowrap hover:opacity-90 hover:shadow-lg text-sm" style="background-color: {{ $companyColors->primary_color }}; color: #ffffff;" disabled>
                        Book Appointment
                    </button>
                </div>
                <div class="text-center mt-2 pt-2" style="border-top: 1px solid {{ $companyColors->background_color }}60;">
                    <a href="{{ route('contact') }}" class="inline-flex items-center text-sm hover:opacity-80 transition-opacity" style="color: {{ $companyColors->getNavbarTextColor() }}99;">
                        <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                        </svg>
                        Prefer a callback? Send us a message instead
                    </a>
                </div>
            </div>
        </div>
    </form>
    
    <!-- Spacer for sticky footer -->
    <div class="h-32"></div>
</div>

<style>
.time-slot {
    padding: 0.5rem 0.75rem;
    background-color: {{ $companyColors->body_background_color }};
    border: 2px solid {{ $companyColors->text_color }}20;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    color: {{ $companyColors->text_color }};
    cursor: pointer;
    transition: all 0.2s ease;
    text-align: center;
}
.time-slot:hover:not(.unavailable) {
    background-color: {{ $companyColors->primary_color }}20;
    border-color: {{ $companyColors->primary_color }};
    color: {{ $companyColors->text_color }};
    transform: scale(1.05);
    box-shadow: 0 4px 6px -1px {{ $companyColors->primary_color }}40;
}
.time-slot.selected {
    background-color: {{ $companyColors->primary_color }};
    border-color: {{ $companyColors->primary_color }};
    color: #FFFFFF;
    box-shadow: 0 0 0 3px {{ $companyColors->primary_color }}40, 0 4px 6px -1px {{ $companyColors->primary_color }}60;
    transform: scale(1.02);
}
.time-slot.unavailable {
    background-color: {{ $companyColors->body_background_color }};
    border-color: {{ $companyColors->text_color }}10;
    color: {{ $companyColors->text_color }}40;
    cursor: not-allowed;
    text-decoration: line-through;
    opacity: 0.5;
}
.time-slot.unavailable:hover {
    background-color: {{ $companyColors->body_background_color }};
    border-color: {{ $companyColors->text_color }}10;
    color: {{ $companyColors->text_color }}40;
    transform: none;
    box-shadow: none;
}
</style>

<script>
let availabilityData = null;
let selectedSlotValue = null;
let currentWeekIndex = 0;

// Detect user's timezone
function getUserTimezone() {
    try {
        return Intl.DateTimeFormat().resolvedOptions().timeZone;
    } catch (e) {
        return 'America/New_York'; // Default fallback
    }
}

// Set initial timezone
document.addEventListener('DOMContentLoaded', function() {
    const userTz = getUserTimezone();
    const selector = document.getElementById('timezoneSelector');
    
    // Try to match user's timezone
    const options = Array.from(selector.options);
    const match = options.find(opt => opt.value === userTz);
    if (match) {
        selector.value = userTz;
    }
    
    // Load availability with initial timezone
    loadAvailability();
    
    // Reload when timezone changes
    selector.addEventListener('change', function() {
        selectedSlotValue = null;
        document.getElementById('selectedSlot').value = '';
        document.getElementById('submitButton').disabled = true;
        document.getElementById('selectedTimeDisplay').classList.add('hidden');
        document.getElementById('noSelectionText').classList.remove('hidden');
        loadAvailability();
    });

    // Reload when staff selection changes
    const staffSelector = document.getElementById('staffSelector');
    if (staffSelector) {
        staffSelector.addEventListener('change', function() {
            selectedSlotValue = null;
            document.getElementById('selectedSlot').value = '';
            document.getElementById('submitButton').disabled = true;
            document.getElementById('selectedTimeDisplay').classList.add('hidden');
            document.getElementById('noSelectionText').classList.remove('hidden');
            loadAvailability();
        });
    }
});

async function loadAvailability() {
    try {
        const timezone = document.getElementById('timezoneSelector').value;
        const staffSelector = document.getElementById('staffSelector');
        const staffId = staffSelector ? staffSelector.value : '';
        
        let url = '{{ route('booking.availability') }}?timezone=' + encodeURIComponent(timezone);
        if (staffId) {
            url += '&staff_id=' + encodeURIComponent(staffId);
        }
        
        const res = await fetch(url);
        const data = await res.json();
        availabilityData = data;

        const loadingMsg = document.getElementById('loadingMessage');
        const noAvail = document.getElementById('noAvailability');
        const container = document.getElementById('weeksContainer');

        loadingMsg.classList.add('hidden');

        if (!data.weeks || data.weeks.length === 0 || !data.weeks.some(w => w.days.some(d => d.slots.length > 0))) {
            noAvail.classList.remove('hidden');
            return;
        }

        container.classList.remove('hidden');
        currentWeekIndex = 0;
        renderWeek(container, data, 0);
    } catch (error) {
        console.error('Failed to load availability:', error);
        document.getElementById('loadingMessage').innerHTML = '<p class="text-red-500">Failed to load availability. Please refresh the page.</p>';
    }
}

function selectSlot(buttonElement, value) {
    // Remove selected class from all slots
    document.querySelectorAll('.time-slot.selected').forEach(el => {
        el.classList.remove('selected');
    });

    // Add selected class to clicked slot
    buttonElement.classList.add('selected');
    
    // Store the selected value
    selectedSlotValue = value;
    document.getElementById('selectedSlot').value = value;
    document.getElementById('selectionError').classList.add('hidden');
    
    // Update the confirmation display
    updateSelectedTimeDisplay(value);
    
    // Enable submit button
    document.getElementById('submitButton').disabled = false;
    
    // Scroll to show the sticky footer if not visible
    setTimeout(() => {
        const stickySubmit = document.getElementById('stickySubmit');
        if (stickySubmit && !isElementInViewport(stickySubmit)) {
            stickySubmit.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }, 100);
}

function updateSelectedTimeDisplay(isoString) {
    const date = new Date(isoString);
    const timezone = document.getElementById('timezoneSelector').value;
    const options = { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        hour: 'numeric',
        minute: '2-digit',
        hour12: true,
        timeZone: timezone,
        timeZoneName: 'short'
    };
    const formattedDate = date.toLocaleDateString('en-US', options);
    
    document.getElementById('selectedTimeText').textContent = formattedDate;
    document.getElementById('selectedTimeDisplay').classList.remove('hidden');
    document.getElementById('noSelectionText').classList.add('hidden');
}

function isElementInViewport(el) {
    const rect = el.getBoundingClientRect();
    return (
        rect.top >= 0 &&
        rect.left >= 0 &&
        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
        rect.right <= (window.innerWidth || document.documentElement.clientWidth)
    );
}

function renderWeek(container, data, weekIndex) {
    // Collect only weeks that have at least one available slot
    const visibleWeeks = data.weeks.filter(w => w.days.some(d => d.slots.some(s => s.available)));

    if (!visibleWeeks.length) {
        document.getElementById('noAvailability').classList.remove('hidden');
        container.classList.add('hidden');
        return;
    }

    // Clamp index
    currentWeekIndex = Math.max(0, Math.min(weekIndex, visibleWeeks.length - 1));
    const week = visibleWeeks[currentWeekIndex];

    container.innerHTML = '';

    // Week header row with nav
    const nav = document.createElement('div');
    nav.className = 'flex items-center justify-between mb-5';
    nav.innerHTML = `
        <button type="button" id="prevWeekBtn"
            class="px-3 py-1.5 rounded-lg text-sm font-semibold transition-opacity ${currentWeekIndex === 0 ? 'opacity-30 cursor-not-allowed' : 'hover:opacity-80'}"
            style="background: {{ $companyColors->text_color }}15; color: {{ $companyColors->text_color }};"
            ${currentWeekIndex === 0 ? 'disabled' : ''}>
            &larr; Prev week
        </button>
        <h3 class="text-base font-semibold" style="color: {{ $companyColors->text_color }};">${week.weekLabel} <span class="text-xs font-normal opacity-50">(${currentWeekIndex + 1} / ${visibleWeeks.length})</span></h3>
        <button type="button" id="nextWeekBtn"
            class="px-3 py-1.5 rounded-lg text-sm font-semibold transition-opacity ${currentWeekIndex >= visibleWeeks.length - 1 ? 'opacity-30 cursor-not-allowed' : 'hover:opacity-80'}"
            style="background: {{ $companyColors->text_color }}15; color: {{ $companyColors->text_color }};"
            ${currentWeekIndex >= visibleWeeks.length - 1 ? 'disabled' : ''}>
            Next week &rarr;
        </button>
    `;
    container.appendChild(nav);

    if (!document.getElementById('prevWeekBtn').disabled) {
        document.getElementById('prevWeekBtn').addEventListener('click', () => renderWeek(container, data, currentWeekIndex - 1));
    }
    if (!document.getElementById('nextWeekBtn').disabled) {
        document.getElementById('nextWeekBtn').addEventListener('click', () => renderWeek(container, data, currentWeekIndex + 1));
    }

    // Days grid
    const daysGrid = document.createElement('div');
    daysGrid.className = 'grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-7 gap-4';

    week.days.forEach(day => {
        if (day.slots.length === 0 || !day.slots.some(s => s.available)) {
            return;
        }

        const dayCard = document.createElement('div');
        dayCard.className = 'rounded-lg p-3';
        dayCard.style.backgroundColor = '{{ $companyColors->body_background_color }}';
        dayCard.style.border = '1px solid {{ $companyColors->text_color }}20';

        const dayHeader = document.createElement('div');
        dayHeader.className = 'text-center mb-3 pb-2';
        dayHeader.style.borderBottom = '1px solid {{ $companyColors->text_color }}20';
        dayHeader.innerHTML = `
            <div class="text-sm font-semibold" style="color: {{ $companyColors->text_color }}99;">${day.dayName}</div>
            <div class="text-lg font-bold" style="color: {{ $companyColors->text_color }};">${day.dayNumber}</div>
        `;
        dayCard.appendChild(dayHeader);

        const slotsContainer = document.createElement('div');
        slotsContainer.className = 'space-y-2 max-h-64 overflow-y-auto pr-1';

        day.slots.forEach(slot => {
            const slotBtn = document.createElement('button');
            slotBtn.type = 'button';
            slotBtn.className = 'time-slot w-full';
            slotBtn.textContent = slot.time;
            slotBtn.dataset.value = slot.value;

            // Re-apply selected state if this slot was previously chosen
            if (selectedSlotValue && slot.value === selectedSlotValue) {
                slotBtn.classList.add('selected');
            }

            if (!slot.available) {
                slotBtn.classList.add('unavailable');
                slotBtn.disabled = true;
            } else {
                slotBtn.addEventListener('click', () => selectSlot(slotBtn, slot.value));
            }

            slotsContainer.appendChild(slotBtn);
        });

        dayCard.appendChild(slotsContainer);
        daysGrid.appendChild(dayCard);
    });

    container.appendChild(daysGrid);
}

document.getElementById('bookingForm').addEventListener('submit', (e) => {
    if (!selectedSlotValue) {
        e.preventDefault();
        document.getElementById('selectionError').classList.remove('hidden');
        document.getElementById('weeksContainer').scrollIntoView({ behavior: 'smooth' });
    }
});
</script>

@endsection
