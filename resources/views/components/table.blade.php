@props(['headers' => [], 'empty' => null])
<div class="overflow-x-auto">
    <table class="w-full text-sm">
        @if(!empty($headers))
        <thead class="bg-gray-50 border-b">
            <tr>
                @foreach($headers as $header)
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        @endif
        <tbody class="divide-y divide-gray-100">
            {{ $slot }}
        </tbody>
    </table>

    @if($empty)
    <div class="px-4 py-12 text-center text-gray-400">{{ $empty }}</div>
    @endif
</div>
