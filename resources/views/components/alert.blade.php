@if(session('success'))
<div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-4 flex items-center gap-2">
    <span>✓</span>
    <span>{{ session('success') }}</span>
</div>
@endif
@if(session('error'))
<div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4 flex items-center gap-2">
    <span>✗</span>
    <span>{{ session('error') }}</span>
</div>
@endif
@if($errors->any())
<div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4">
    <ul class="list-disc list-inside text-sm">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
