<div x-data="{ visible: true }" x-show="visible" x-transition.opacity.duration.300ms
    class="lg:hidden border-b border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
    <div class="flex items-start justify-between gap-3">
        <div>
            <span class="font-medium">
                Visualización limitada.
            </span>
            <span class="ml-1">
                Para una mejor experiencia, se recomienda usar este sistema en una tablet en orientación horizontal o en
                una pantalla de mayor resolución.
            </span>
        </div>

        <button type="button" class="text-amber-600 hover:text-amber-800" @click="visible = false">
            ✕
        </button>
    </div>
</div>
