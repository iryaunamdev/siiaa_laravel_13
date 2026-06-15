<div class="space-y-6">
    <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
        <form wire:submit.prevent="save" class="space-y-5">
            @can('manage', App\Models\Solicitudes\Solicitud::class)
                <div class="rounded-lg border border-amber-200 bg-amber-50 p-4">
                    <label class="mb-1 block text-sm font-medium text-amber-900">
                        Solicitante propietario
                    </label>

                    <select wire:model="form.owner_id"
                        class="w-full rounded-lg border-amber-300 text-sm shadow-sm focus:border-amber-500 focus:ring-amber-500">
                        <option value="">Seleccione la identidad propietaria de la solicitud</option>

                        @foreach($c_owner_identities as $identity)
                            <option value="{{ $identity->id }}">
                                {{ $identity->fullname() ?? 'Identidad sin nombre' }}
                                @if($identity->emailResolved())
                                    — {{ $identity->emailResolved() }}
                                @endif
                                [{{ $identity->identity_type }} #{{ $identity->id }}]
                            </option>
                        @endforeach
                    </select>

                    <p class="mt-2 text-xs text-amber-800">
                        Como administrador, esta solicitud se creara a nombre de la identidad seleccionada.
                        Este dato se guardara como owner_id.
                    </p>

                    @error('form.owner_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            @endcan

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">
                        Tipo de solicitud
                    </label>
                    <select wire:model="form.tipo_solicitud_id"
                        class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Seleccione una opcion</option>

                        @foreach($c_tipos_solicitud as $tipo)
                            <option value="{{ $tipo->id }}">
                                {{ $tipo->nombre }}
                            </option>
                        @endforeach
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
                        <option value="">Seleccione una opcion</option>

                        @foreach($c_motivos as $motivo)
                            <option value="{{ $motivo->id }}">
                                {{ $motivo->nombre }}
                            </option>
                        @endforeach
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
                    Informacion adicional
                </label>
                <textarea wire:model="form.informacion_adicional" rows="5"
                    class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    placeholder="Describa brevemente la solicitud."></textarea>
                @error('form.informacion_adicional')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-4">
                <a href="{{ route('solicitudes.index') }}"
                    class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancelar
                </a>

                <button type="submit"
                    class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700"
                    wire:loading.attr="disabled">
                    Guardar borrador
                </button>
            </div>
        </form>
    </section>
</div>
