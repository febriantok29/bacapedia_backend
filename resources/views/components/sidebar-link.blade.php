@props(['href', 'icon' => '', 'active' => false])
<a href="{{ $href }}" class="flex items-center gap-3 px-5 py-2.5 text-sm transition-colors {{ $active ? 'bg-sidebar-hover text-white border-r-4 border-blue-400' : 'text-gray-300 hover:bg-sidebar-hover hover:text-white' }}">
    <span>{{ $icon }}</span>
    <span>{{ $slot }}</span>
</a>
