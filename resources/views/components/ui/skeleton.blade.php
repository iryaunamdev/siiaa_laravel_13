{{--
|--------------------------------------------------------------------------
| UI Component: Skeleton
|--------------------------------------------------------------------------
|
| Propósito:
| Mostrar placeholders visuales mientras se carga contenido.
|
| Uso principal:
| - Cargas Livewire
| - Tablas
| - Cards / paneles
| - Formularios
|
| Props:
| - type: line | block | avatar | table
| - rows: int
| - width: string|null
| - height: string|null
| - rounded: string
| - animated: bool
|
|--------------------------------------------------------------------------
--}}

@props([
    'type' => 'line',
    'rows' => 1,
    'width' => null,
    'height' => null,
    'rounded' => 'rounded-md',
    'animated' => true,
])

@php
    $animationClass = $animated ? 'animate-pulse' : '';

    $baseClass = trim("bg-slate-200 {$rounded} {$animationClass}");

    $defaultSizes = [
        'line' => [
            'width' => $width ?? 'w-full',
            'height' => $height ?? 'h-4',
        ],
        'block' => [
            'width' => $width ?? 'w-full',
            'height' => $height ?? 'h-24',
        ],
        'avatar' => [
            'width' => $width ?? 'w-10',
            'height' => $height ?? 'h-10',
        ],
    ];
@endphp

@if ($type === 'table')
    <div {{ $attributes->merge(['class' => 'space-y-3']) }}>
        @for ($i = 0; $i < $rows; $i++)
            <div class="grid grid-cols-12 gap-3">
                <div class="{{ $baseClass }} h-4 col-span-2"></div>
                <div class="{{ $baseClass }} h-4 col-span-4"></div>
                <div class="{{ $baseClass }} h-4 col-span-3"></div>
                <div class="{{ $baseClass }} h-4 col-span-3"></div>
            </div>
        @endfor
    </div>
@else
    @for ($i = 0; $i < $rows; $i++)
        @php
            $resolvedWidth = $defaultSizes[$type]['width'] ?? ($width ?? 'w-full');
            $resolvedHeight = $defaultSizes[$type]['height'] ?? ($height ?? 'h-4');
        @endphp

        <div
            {{ $attributes->merge([
                'class' => "{$baseClass} {$resolvedWidth} {$resolvedHeight}" . ($rows > 1 ? ' mb-2' : ''),
            ]) }}>
        </div>
    @endfor
@endif
