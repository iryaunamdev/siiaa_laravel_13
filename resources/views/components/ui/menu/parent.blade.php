@props([
    'label' => '',
    'open' => false,
])

<li x-data="{ open: @js($open) }" class="space-y-1">
    <button type="button" @click="open = !open"
        class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-sm text-slate-600 transition-colors duration-200 hover:bg-slate-100 hover:text-slate-900"
        :aria-expanded="open.toString()">
        @isset($icon)
            <span class="shrink-0 text-slate-500">
                {{ $icon }}
            </span>
        @endisset

        <span class="flex-1 truncate text-left">
            {{ $label }}
        </span>

        <svg class="h-4 w-4 text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': open }"
            viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd"
                d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.51a.75.75 0 01-1.08 0l-4.25-4.51a.75.75 0 01.02-1.06z"
                clip-rule="evenodd" />
        </svg>
    </button>

    <div x-show="open" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-1" class="ml-3 border-l border-slate-200 pl-3">
        <x-ui.menu class="space-y-1">
            {{ $slot }}
        </x-ui.menu>
    </div>
</li>
