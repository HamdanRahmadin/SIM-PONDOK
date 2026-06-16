@props([
    'show' => false,
    'title' => '',
    'subtitle' => null,
    'maxWidth' => 'max-w-lg',
    'closeAction' => null,
])

@if($show)
<div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full {{ $maxWidth }} overflow-hidden border border-slate-100 flex flex-col max-h-[90vh]">
        <div class="bg-gradient-to-br from-emerald-800 to-emerald-900 px-6 py-4 text-white flex items-center justify-between shrink-0">
            <div>
                <h3 class="font-bold text-base">{{ $title }}</h3>
                @if($subtitle)
                    <p class="text-xs text-emerald-100 mt-0.5">{{ $subtitle }}</p>
                @endif
            </div>
            @if($closeAction)
                <button wire:click="{{ $closeAction }}" class="text-white/80 hover:text-white transition-colors cursor-pointer focus:outline-none" type="button">
                    <x-lucide-x class="w-5 h-5" />
                </button>
            @else
                {{ $close ?? '' }}
            @endif
        </div>
        <div class="p-6 overflow-y-auto flex-1 scrollbar-none">
            {{ $slot }}
        </div>
        @isset($footer)
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 flex items-center justify-between shrink-0">
                {{ $footer }}
            </div>
        @endisset
    </div>
</div>
@endif
