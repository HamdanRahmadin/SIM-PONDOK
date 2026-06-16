@props([
    'show' => false,
    'title' => 'Konfirmasi',
    'message' => 'Apakah Anda yakin?',
    'confirmText' => 'Ya, Lanjutkan',
    'cancelText' => 'Batal',
    'confirmAction' => null,
    'cancelAction' => null,
    'icon' => 'lucide-help-circle',
])

@if($show || $attributes->has('x-show'))
<div {{ $attributes }} class="fixed inset-0 z-[60] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-sm w-full overflow-hidden p-6 text-center space-y-4">
        <div class="mx-auto w-11 h-11 bg-emerald-50 text-emerald-800 rounded-full flex items-center justify-center">
            <x-dynamic-component :component="$icon" class="w-6 h-6" />
        </div>
        <div>
            <h4 class="font-bold text-sm text-slate-800 font-heading">{{ $title }}</h4>
            <p class="text-xs text-slate-500 mt-2 leading-relaxed">{{ $message }}</p>
        </div>
        <div class="flex items-center justify-center space-x-2 pt-2">
            @if($cancelAction)
                <button wire:click="{{ $cancelAction }}" class="py-2 px-4 border border-slate-200 text-slate-600 hover:bg-slate-100 rounded-lg text-xs font-semibold cursor-pointer transition" type="button">
                    {{ $cancelText }}
                </button>
            @else
                {{ $cancel ?? '' }}
            @endif
            @if($confirmAction)
                <button wire:click="{{ $confirmAction }}" class="py-2 px-5 bg-emerald-800 hover:bg-emerald-950 text-white rounded-lg text-xs font-bold transition shadow-md cursor-pointer" type="button">
                    {{ $confirmText }}
                </button>
            @else
                {{ $confirm ?? '' }}
            @endif
        </div>
    </div>
</div>
@endif
