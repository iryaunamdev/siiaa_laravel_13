@props([
    'sidebar' => false,
])

@if ($sidebar)
    <x-app-logo-icon align="left" size="size-9" text="text-3xl" color="text-blue-900" />
@else
    <flux:brand name="Laravel Starter Kit" {{ $attributes }}>
        <x-slot name="logo"
            class="flex aspect-square size-8 items-center justify-center rounded-md bg-accent-content text-accent-foreground">
            <x-app-logo-icon class="size-5 fill-current text-white dark:text-black" />
        </x-slot>
    </flux:brand>
@endif
