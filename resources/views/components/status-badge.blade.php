@props(['status'])

@php
$styles = [
    'aktif'        => 'bg-emerald-100 text-emerald-800 border-emerald-200',
    'nonaktif'     => 'bg-red-100 text-red-800 border-red-200',
    'lulus'        => 'bg-blue-100 text-blue-800 border-blue-200',
    'lunas'        => 'bg-emerald-100 text-emerald-800 border-emerald-200',
    'dicicil'      => 'bg-amber-100 text-amber-800 border-amber-200',
    'belum_bayar'  => 'bg-slate-100 text-slate-500 border-slate-200',
    'pulang'       => 'bg-red-100 text-red-800 border-red-200',
    'hadir'        => 'bg-emerald-100 text-emerald-800 border-emerald-200',
    'izin_sakit'   => 'bg-amber-100 text-amber-800 border-amber-200',
    'alfa'         => 'bg-rose-100 text-rose-800 border-rose-200',
    'created'      => 'bg-emerald-50 text-emerald-800 border-emerald-100',
    'updated'      => 'bg-amber-50 text-amber-800 border-amber-100',
    'deleted'      => 'bg-red-50 text-red-800 border-red-100',
];

$label = str_replace('_', ' ', $status);
@endphp

<span {{ $attributes->merge(['class' => 'px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider border inline-block ' . ($styles[$status] ?? 'bg-slate-100 text-slate-500 border-slate-200')]) }}>
    {{ $label }}
</span>
