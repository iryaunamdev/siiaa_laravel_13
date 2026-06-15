<div class="space-y-6">
    <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
        <form wire:submit.prevent="save" class="space-y-5">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">
                        Tipo de solicitud
                    </label>
                    <select wire:model="form.tipo_solicitud_id"
                        class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Seleccione una opción</option>
                    </select>
                    @error('form.tipo_solicitud_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">
                        Motivo
                    </label>
                    <select wire:model="form.motivo_id"
                        class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Seleccione una opción</option>
                    </select>
                    @error('form.motivo_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">
                        Fecha de inicio
                    </label>
                    <input type="date" wire:model="form.fecha_inicio"
                        class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('form.fecha_inicio')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">
                        Fecha de fin
                    </label>
                    <input type="date" wire:model="form.fecha_fin"
                        class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('form.fecha_fin')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">
                    Información adicional
                </label>
                <textarea wire:model="form.informacion_adicional" rows="5"
                    class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                @error('form.informacion_adicional')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div
                class="flex flex-col gap-3 border-t border-gray-100 pt-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <span
                        class="inline-flex rounded-full border px-2.5 py-1 text-xs font-medium {{ $solicitud->estatusBadgeClass() }}">
                        {{ $solicitud->estatusNombre() }}
                    </span>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('solicitudes.show', $solicitud) }}"
                        class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Cancelar
                    </a>

                    <button type="submit"
                        class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700"
                        wire:loading.attr="disabled">
                        Guardar cambios
                    </button>

                    @can('send', $solicitud)
                        <button type="button" wire:click="enviar"
                            wire:confirm="Al enviar la solicitud ya no podrá editarla libremente. ¿Desea continuar?"
                            class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-emerald-700"
                            wire:loading.attr="disabled">
                            Enviar solicitud
                        </button>
                    @endcan
                </div>
            </div>
        </form>
    </section>
</div>
