@props(['variant' => 'primary', 'size' => 'md', 'type' => 'submit'])
@php
$base = 'inline-flex items-center justify-center font-medium rounded transition-colors';
$variants = match($variant) {
    'primary' => 'bg-blue-600 text-white hover:bg-blue-700',
    'danger' => 'bg-red-600 text-white hover:bg-red-700',
    'success' => 'bg-green-600 text-white hover:bg-green-700',
    'secondary' => 'bg-gray-200 text-gray-700 hover:bg-gray-300',
    'outline' => 'border border-gray-300 text-gray-700 hover:bg-gray-50',
    default => 'bg-blue-600 text-white hover:bg-blue-700',
};
$sizes = match($size) {
    'sm' => 'px-3 py-1.5 text-xs',
    'md' => 'px-4 py-2 text-sm',
    'lg' => 'px-5 py-2.5 text-base',
    default => 'px-4 py-2 text-sm',
};
@endphp
<button type="{{ $type }}" {{ $attributes->merge(['class' => "$base $variants $sizes"]) }}>{{ $slot }}</button>
