@props(['headers' => []])

<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-700">
        <thead class="bg-gray-800">
            <tr>
                @foreach($headers as $index => $header)
                    @if($header === '')
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider w-12">
                            <input type="checkbox" 
                                   id="select-all-checkbox" 
                                   class="w-4 h-4 text-blue-600 bg-gray-700 border-gray-600 rounded focus:ring-blue-500 focus:ring-2 cursor-pointer"
                                   onchange="toggleAllCheckboxes(this.checked)">
                        </th>
                    @else
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                            {{ $header }}
                        </th>
                    @endif
                @endforeach
            </tr>
        </thead>
        <tbody class="bg-gray-800 divide-y divide-gray-700">
            {{ $slot }}
        </tbody>
    </table>
</div>
