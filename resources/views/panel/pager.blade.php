@if(($metadata['last_page'] ?? 1) > 1)
<div class="p-4 flex items-center justify-between text-sm">
    <span class="text-gray-500">Halaman {{ $metadata['current_page'] ?? 1 }} dari {{ $metadata['last_page'] ?? 1 }}</span>
    <div class="flex gap-2">
        @if(($metadata['current_page'] ?? 1) > 1)
        <a href="{{ request()->url() }}?{{ http_build_query(array_merge($query ?? [], ['page' => ($metadata['current_page'] ?? 1) - 1])) }}" class="px-3 py-1 rounded border hover:bg-gray-50">Prev</a>
        @else
        <span class="px-3 py-1 rounded border text-gray-300">Prev</span>
        @endif
        @if(($metadata['current_page'] ?? 1) < ($metadata['last_page'] ?? 1))
        <a href="{{ request()->url() }}?{{ http_build_query(array_merge($query ?? [], ['page' => ($metadata['current_page'] ?? 1) + 1])) }}" class="px-3 py-1 rounded border hover:bg-gray-50">Next</a>
        @else
        <span class="px-3 py-1 rounded border text-gray-300">Next</span>
        @endif
    </div>
</div>
@endif