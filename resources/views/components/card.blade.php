@props(['title' => null, 'padding' => true])
<div class="bg-white rounded-lg shadow-sm border">
    @if($title)
    <div class="px-5 py-3 border-b">
        <h2 class="font-semibold text-gray-700">{{ $title }}</h2>
    </div>
    @endif
    <div @class(['px-5 py-4' => $padding, 'p-0' => !$padding])>
        {{ $slot }}
    </div>
</div>
