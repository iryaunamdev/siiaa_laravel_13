@props(['title', 'description' => null])

<div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
    <div>
        <h1 class="text-2xl font-semibold tracking-tight text-slate-900">
            {{ $title }}
        </h1>

        @if ($description)
            <p class="mt-1 text-sm text-slate-600">
                {{ $description }}
            </p>
        @endif
    </div>

    @if (isset($actions))
        <div class="flex shrink-0 items-center gap-2">
            {{ $actions }}
        </div>
    @endif
</div>
