@props([
    'type' => null,
    'title' => null,
    'message' => null,
    'dismissible' => true,
    'icon' => true,
])

@php
    /*
    |--------------------------------------------------------------------------
    | x-ui.alert
    |--------------------------------------------------------------------------
    | Uso automático:
    | <x-ui.alert />
    |
    | Uso manual:
    | <x-ui.alert type="success" message="Usuario guardado correctamente." />
    |
    | Con título y contenido largo:
    | <x-ui.alert type="warning" title="Atención">
    |     Revisa la información antes de continuar.
    | </x-ui.alert>
    |
    | Flash soportados:
    | success, error, warning, info, status
    */

    $flashTypes = ['success', 'error', 'warning', 'info', 'status'];

    if (!$type) {
        foreach ($flashTypes as $flashType) {
            if (session()->has($flashType)) {
                $type = $flashType;
                $message = session($flashType);
                break;
            }
        }
    }

    $type = $type ?? 'info';

    if ($type === 'status') {
        $type = 'info';
    }

    $styles = [
        'success' => [
            'wrapper' => 'border-green-200 bg-green-50 text-green-800',
            'icon' => 'text-green-600',
            'button' => 'text-green-700 hover:bg-green-100 focus:ring-green-500',
            'title' => 'text-green-900',
        ],
        'error' => [
            'wrapper' => 'border-red-200 bg-red-50 text-red-800',
            'icon' => 'text-red-600',
            'button' => 'text-red-700 hover:bg-red-100 focus:ring-red-500',
            'title' => 'text-red-900',
        ],
        'warning' => [
            'wrapper' => 'border-yellow-200 bg-yellow-50 text-yellow-800',
            'icon' => 'text-yellow-600',
            'button' => 'text-yellow-700 hover:bg-yellow-100 focus:ring-yellow-500',
            'title' => 'text-yellow-900',
        ],
        'info' => [
            'wrapper' => 'border-sky-200 bg-sky-50 text-sky-800',
            'icon' => 'text-sky-600',
            'button' => 'text-sky-700 hover:bg-sky-100 focus:ring-sky-500',
            'title' => 'text-sky-900',
        ],
    ];

    $current = $styles[$type] ?? $styles['info'];

    $hasContent = filled($message) || trim((string) $slot) !== '';
@endphp

@if ($hasContent)
    <div x-data="{ show: true }" x-show="show" x-transition.opacity.duration.200ms
        {{ $attributes->merge([
            'class' => 'mb-4 rounded-xl border px-4 py-3 text-sm shadow-sm ' . $current['wrapper'],
            'role' => 'alert',
        ]) }}>
        <div class="flex items-start gap-3">
            @if ($icon)
                <div class="mt-0.5 shrink-0 {{ $current['icon'] }}">
                    @switch($type)
                        @case('success')
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"
                                aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16Zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4Z"
                                    clip-rule="evenodd" />
                            </svg>
                        @break

                        @case('error')
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"
                                aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16ZM7.879 6.464a1 1 0 10-1.415 1.415L8.586 10l-2.122 2.121a1 1 0 001.415 1.415L10 11.414l2.121 2.122a1 1 0 001.415-1.415L11.414 10l2.122-2.121a1 1 0 00-1.415-1.415L10 8.586 7.879 6.464Z"
                                    clip-rule="evenodd" />
                            </svg>
                        @break

                        @case('warning')
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"
                                aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.515 2.625H3.72c-1.345 0-2.188-1.458-1.515-2.625L8.485 2.495ZM10 6a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 6Zm0 7a1 1 0 100-2 1 1 0 000 2Z"
                                    clip-rule="evenodd" />
                            </svg>
                        @break

                        @default
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"
                                aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M18 10A8 8 0 112 10a8 8 0 0116 0ZM9.25 7a.75.75 0 011.5 0v.01a.75.75 0 01-1.5 0V7ZM10 9a.75.75 0 00-.75.75v3.5a.75.75 0 001.5 0v-3.5A.75.75 0 0010 9Z"
                                    clip-rule="evenodd" />
                            </svg>
                    @endswitch
                </div>
            @endif

            <div class="min-w-0 flex-1">
                @if ($title)
                    <div class="mb-1 font-semibold {{ $current['title'] }}">
                        {{ $title }}
                    </div>
                @endif

                <div class="leading-6">
                    @if (filled($message))
                        {{ $message }}
                    @else
                        {{ $slot }}
                    @endif
                </div>
            </div>

            @if ($dismissible)
                <button type="button" x-on:click="show = false"
                    class="ml-3 inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-lg transition focus:outline-none focus:ring-2 focus:ring-offset-2 {{ $current['button'] }}"
                    aria-label="Cerrar alerta">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"
                        aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414Z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
            @endif
        </div>
    </div>
@endif
