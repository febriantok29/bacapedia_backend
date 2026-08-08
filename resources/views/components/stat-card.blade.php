@props(['label', 'value', 'color' => 'blue'])
@php
$colorClasses = match($color) {
    'blue' => 'text-blue-600 bg-blue-50',
    'green' => 'text-green-600 bg-green-50',
    'red' => 'text-red-600 bg-red-50',
    'yellow' => 'text-yellow-600 bg-yellow-50',
    'purple' => 'text-purple-600 bg-purple-50',
    'orange' => 'text-orange-600 bg-orange-50',
    default => 'text-gray-600 bg-gray-50',
};
@endphp
<div class="bg-white rounded-lg shadow-sm border p-5">
    <p class="text-sm text-gray-500 mb-1">{{ $label }}</p>
    <p class="text-2xl font-bold {{ $colorClasses }} inline-block px-2 py-1 rounded">{{ $value }}</p>
</div>
