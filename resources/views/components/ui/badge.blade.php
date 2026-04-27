@props([
    'variant' => 'default',
    'shape' => 'default', // default | pill
    'textSize' => 'text-xs',
])

@php
    /*
    |--------------------------------------------------------------------------
    | x-ui.badge
    |--------------------------------------------------------------------------
    | Badge/Pill reutilizable para estados, etiquetas y conteos.
    |
    | Uso:
    | <x-ui.badge variant="success">Activo</x-ui.badge>
    |
    | Con pill:
    | <x-ui.badge variant="warning" shape="pill">Pendiente</x-ui.badge>
    |
    | Tamaño de texto:
    | <x-ui.badge text-size="text-sm">Admin</x-ui.badge>
    |
    | Reglas de padding:
    | - < text-xs  → px-1.5 py-0.5
    | - text-xs/sm → padding proporcional
    | - >= text-base → px-3 py-1.5
    */

    // 🎨 Variantes (colores suaves)
    $variants = [
        'default' => 'bg-slate-100 text-slate-700 ring-slate-200',
        'neutral' => 'bg-slate-50 text-slate-600 ring-slate-200',
        'success' => 'bg-green-50 text-green-700 ring-green-200',
        'danger' => 'bg-red-50 text-red-700 ring-red-200',
        'warning' => 'bg-yellow-50 text-yellow-700 ring-yellow-200',
        'info' => 'bg-sky-50 text-sky-700 ring-sky-200',
    ];

    $variantClass = $variants[$variant] ?? $variants['default'];

    // 🔵 Forma
    $shapeClass = $shape === 'pill' ? 'rounded-full' : 'rounded-md';

    // 📏 Padding dinámico
    $padding = match (true) {
        str_contains($textSize, 'text-[') => 'px-1.5 py-0.5', // menor a xs (custom)
        $textSize === 'text-xs' => 'px-2 py-0.5',
        $textSize === 'text-sm' => 'px-2.5 py-1',
        in_array($textSize, ['text-base', 'text-lg', 'text-xl']) => 'px-3 py-1.5',
        default => 'px-2 py-0.5',
    };
@endphp

<span
    {{ $attributes->merge([
        'class' => "inline-flex items-center gap-1 font-medium {$textSize} {$padding} {$shapeClass} {$variantClass} ring-1",
    ]) }}>
    {{ $slot }}
</span>
