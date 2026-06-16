@props(['type' => 'success', 'message' => null])

@php
$styles = [
    'success' => 'bg-emerald-50 border-emerald-200 text-emerald-800',
    'error'   => 'bg-red-50 border-red-200 text-red-800',
    'warning' => 'bg-amber-50 border-amber-200 text-amber-800',
    'info'    => 'bg-blue-50 border-blue-200 text-blue-800',
];

$icons = [
    'success' => 'lucide-check-circle',
    'error'   => 'lucide-alert-circle',
    'warning' => 'lucide-alert-triangle',
    'info'    => 'lucide-info',
];

$msg = $message ?? session('success') ?? session('error') ?? null;
$resolvedType = $message ? $type : (session('success') ? 'success' : (session('error') ? 'error' : $type));
@endphp

@if($msg)
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
     x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4"
     x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 translate-x-4"
     class="flex items-center gap-2 px-4 py-3 rounded-xl text-sm font-medium border shadow-sm {{ $styles[$resolvedType] }}">
    <x-dynamic-component :component="$icons[$resolvedType]" class="w-5 h-5 shrink-0" />
    <span>{{ $msg }}</span>
</div>
@endif
