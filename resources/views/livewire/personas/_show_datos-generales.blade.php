<div class="mb-5">
    <h3 class="text-base font-semibold text-zinc-800">
        Datos generales
    </h3>
</div>
<dl class="grid gap-4 grid-cols-12">
    <div class="col-span-4">
        <dt class="text-xs font-medium uppercase tracking-wide text-zinc-400">
            Nombre
        </dt>
        <dd class="mt-1 text-sm text-zinc-700">
            {{ $persona->nombre ?: '—' }}
        </dd>
    </div>

    <div class="col-span-4">
        <dt class="text-xs font-medium uppercase tracking-wide text-zinc-400">
            Apellido paterno
        </dt>
        <dd class="mt-1 text-sm text-zinc-700">
            {{ $persona->apellidop ?: '—' }}
        </dd>
    </div>

    <div class="col-span-4">
        <dt class="text-xs font-medium uppercase tracking-wide text-zinc-400">
            Apellido materno
        </dt>
        <dd class="mt-1 text-sm text-zinc-700">
            {{ $persona->apellidom ?: '—' }}
        </dd>
    </div>

    <div class="col-span-4">
        <dt class="text-xs font-medium uppercase tracking-wide text-zinc-400">
            Correo electrónico
        </dt>
        <dd class="mt-1 text-sm text-zinc-700">
            {{ $persona->email ?: '—' }}
        </dd>
    </div>

    <div class="col-span-4">
        <dt class="text-xs font-medium uppercase tracking-wide text-zinc-400">
            CURP
        </dt>
        <dd class="mt-1 text-sm text-zinc-700">
            {{ $persona->curp ?: '—' }}
        </dd>
    </div>

    <div class="col-span-4">
        <dt class="text-xs font-medium uppercase tracking-wide text-zinc-400">
            RFC
        </dt>
        <dd class="mt-1 text-sm text-zinc-700">
            {{ $persona->rfc ?: '—' }}
        </dd>
    </div>

    <div class="col-span-3">
        <dt class="text-xs font-medium uppercase tracking-wide text-zinc-400">
            Fecha de nacimiento
        </dt>
        <dd class="mt-1 text-sm text-zinc-700">
            {{ $persona->fecha_nacimiento ? $persona->fecha_nacimiento->format('d/m/Y') : '—' }}
        </dd>
    </div>

    <div class="col-span-3">
        <dt class="text-xs font-medium uppercase tracking-wide text-zinc-400">
            Sexo
        </dt>
        <dd class="mt-1 text-sm text-zinc-700">
            {{ $persona->sexo?->nombre ?? '—' }}
        </dd>
    </div>

    <div class="col-span-4">
        <dt class="text-xs font-medium uppercase tracking-wide text-zinc-400">
            Nacionalidad
        </dt>
        <dd class="mt-1 text-sm text-zinc-700">
            {{ $persona->nacionalidad?->nombre ?? '—' }}
        </dd>
    </div>
</dl>
