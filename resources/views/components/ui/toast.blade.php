@props([
    'position' => 'top-right',
    'duration' => 4500,
])

@php
    /*
    |--------------------------------------------------------------------------
    | x-ui.toast
    |--------------------------------------------------------------------------
    | Uso en layout principal:
    | <x-ui.toast />
    |
    | Uso desde Controller con redirect:
    | return redirect()
    |     ->route('users.index')
    |     ->with('success', 'Usuario creado correctamente.');
    |
    | Uso desde Livewire con redirect:
    | session()->flash('success', 'Usuario actualizado correctamente.');
    | return $this->redirectRoute('users.index');
    |
    | Uso desde Livewire sin redirect:
    | $this->dispatch('toast',
    |     type: 'success',
    |     title: 'Guardado',
    |     message: 'Usuario guardado correctamente.'
    | );
    |
    | Uso desde JavaScript:
    | window.dispatchEvent(new CustomEvent('toast', {
    |     detail: {
    |         type: 'success',
    |         title: 'Guardado',
    |         message: 'Usuario guardado correctamente.'
    |     }
    | }));
    |
    | Tipos soportados:
    | success, error, warning, info, status
    |
    | Posiciones soportadas:
    | top-right, top-left, bottom-right, bottom-left
    */

    $positions = [
        'top-right' => 'top-4 right-4',
        'top-left' => 'top-4 left-4',
        'bottom-right' => 'bottom-4 right-4',
        'bottom-left' => 'bottom-4 left-4',
    ];

    $positionClass = $positions[$position] ?? $positions['top-right'];
@endphp

<div x-data="{
    toasts: [],
    counter: 0,

    init() {
        @foreach (['success', 'error', 'warning', 'info', 'status'] as $flashType)
                @if (session()->has($flashType))
                    this.addToast({
                        detail: {
                            type: '{{ $flashType === 'status' ? 'info' : $flashType }}',
                            title: null,
                            message: @js(session($flashType)),
                            duration: {{ (int) $duration }}
                        }
                    });
                @endif @endforeach
    },

    addToast(event) {
        const data = event.detail || {};

        const toast = {
            id: ++this.counter,
            type: data.type || 'info',
            title: data.title || null,
            message: data.message || '',
            duration: data.duration || {{ (int) $duration }},
        };

        if (!toast.message) return;

        this.toasts.push(toast);

        setTimeout(() => {
            this.removeToast(toast.id);
        }, toast.duration);
    },

    removeToast(id) {
        this.toasts = this.toasts.filter(toast => toast.id !== id);
    },

    styles(type) {
        const map = {
            success: {
                wrapper: 'border-green-200 bg-green-50 text-green-800',
                icon: 'text-green-600',
                title: 'text-green-900',
                button: 'text-green-700 hover:bg-green-100 focus:ring-green-500',
            },
            error: {
                wrapper: 'border-red-200 bg-red-50 text-red-800',
                icon: 'text-red-600',
                title: 'text-red-900',
                button: 'text-red-700 hover:bg-red-100 focus:ring-red-500',
            },
            warning: {
                wrapper: 'border-yellow-200 bg-yellow-50 text-yellow-800',
                icon: 'text-yellow-600',
                title: 'text-yellow-900',
                button: 'text-yellow-700 hover:bg-yellow-100 focus:ring-yellow-500',
            },
            info: {
                wrapper: 'border-sky-200 bg-sky-50 text-sky-800',
                icon: 'text-sky-600',
                title: 'text-sky-900',
                button: 'text-sky-700 hover:bg-sky-100 focus:ring-sky-500',
            },
        };

        return map[type] || map.info;
    }
}" x-on:toast.window="addToast($event)"
    class="fixed z-50 flex w-full max-w-sm flex-col gap-3 px-4 sm:px-0 {{ $positionClass }}" aria-live="polite"
    aria-atomic="true">
    <template x-for="toast in toasts" :key="toast.id">
        <div x-show="true" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-2 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-2 scale-95"
            class="pointer-events-auto rounded-xl border px-4 py-3 text-sm shadow-lg backdrop-blur-sm"
            :class="styles(toast.type).wrapper" role="status">
            <div class="flex items-start gap-3">
                <div class="mt-0.5 shrink-0" :class="styles(toast.type).icon">
                    <template x-if="toast.type === 'success'">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"
                            aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16Zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4Z"
                                clip-rule="evenodd" />
                        </svg>
                    </template>

                    <template x-if="toast.type === 'error'">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"
                            aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16ZM7.879 6.464a1 1 0 10-1.415 1.415L8.586 10l-2.122 2.121a1 1 0 001.415 1.415L10 11.414l2.121 2.122a1 1 0 001.415-1.415L11.414 10l2.122-2.121a1 1 0 00-1.415-1.415L10 8.586 7.879 6.464Z"
                                clip-rule="evenodd" />
                        </svg>
                    </template>

                    <template x-if="toast.type === 'warning'">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"
                            aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.515 2.625H3.72c-1.345 0-2.188-1.458-1.515-2.625L8.485 2.495ZM10 6a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 6Zm0 7a1 1 0 100-2 1 1 0 000 2Z"
                                clip-rule="evenodd" />
                        </svg>
                    </template>

                    <template x-if="!['success', 'error', 'warning'].includes(toast.type)">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"
                            aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M18 10A8 8 0 112 10a8 8 0 0116 0ZM9.25 7a.75.75 0 011.5 0v.01a.75.75 0 01-1.5 0V7ZM10 9a.75.75 0 00-.75.75v3.5a.75.75 0 001.5 0v-3.5A.75.75 0 0010 9Z"
                                clip-rule="evenodd" />
                        </svg>
                    </template>
                </div>

                <div class="min-w-0 flex-1">
                    <template x-if="toast.title">
                        <div class="mb-1 font-semibold" :class="styles(toast.type).title" x-text="toast.title"></div>
                    </template>

                    <div class="leading-6" x-text="toast.message"></div>
                </div>

                <button type="button" x-on:click="removeToast(toast.id)"
                    class="ml-2 inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-lg transition focus:outline-none focus:ring-2 focus:ring-offset-2"
                    :class="styles(toast.type).button" aria-label="Cerrar notificación">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"
                        aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414Z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        </div>
    </template>
</div>
