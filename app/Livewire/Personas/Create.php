<?php

namespace App\Livewire\Personas;

use App\Models\Persona;
use App\Models\CatalogoItem;
use Livewire\Component;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Create extends Component
{
    use AuthorizesRequests;

    public string $nombre = '';
    public ?string $apellidop = null;
    public ?string $apellidom = null;
    public ?string $email = null;
    public ?string $curp = null;
    public ?string $rfc = null;
    public ?string $fecha_nacimiento = null;
    public ?int $sexo_id = null;
    public ?int $nacionalidad_id = null;
    public bool $activo = true;
    public ?string $observaciones = null;

    public function mount(): void
    {
        $this->authorize('personas.create');
    }

    public function save()
    {
        $this->authorize('personas.create');

        $data = $this->validate();

        $data['email'] = $this->normalizeNullableLower($data['email'] ?? null);
        $data['curp'] = $this->normalizeNullableUpper($data['curp'] ?? null);
        $data['rfc'] = $this->normalizeNullableUpper($data['rfc'] ?? null);

        $persona = Persona::query()->create($data);

        session()->flash(
            'success',
            'Persona registrada correctamente. Ahora puedes completar su información institucional.'
        );

        return redirect()->route('personas.edit', $persona);
    }

    protected function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:150'],
            'apellidop' => ['nullable', 'string', 'max:150'],
            'apellidom' => ['nullable', 'string', 'max:150'],

            'email' => [
                'nullable',
                'email',
                'max:190',
                Rule::unique('personas', 'email'),
            ],

            'curp' => [
                'nullable',
                'string',
                'max:18',
                Rule::unique('personas', 'curp'),
            ],

            'rfc' => [
                'nullable',
                'string',
                'max:13',
                Rule::unique('personas', 'rfc'),
            ],

            'fecha_nacimiento' => ['nullable', 'date'],

            'sexo_id' => [
                'nullable',
                'integer',
                Rule::exists('catalogos_items', 'id'),
            ],

            'nacionalidad_id' => [
                'nullable',
                'integer',
                Rule::exists('catalogos_items', 'id'),
            ],

            'activo' => ['boolean'],
            'observaciones' => ['nullable', 'string', 'max:2000'],
        ];
    }

    protected function messages(): array
    {
        return [
            'nombre.required' => 'El nombre es obligatorio.',
            'email.email' => 'El correo electrónico no tiene un formato válido.',
            'email.unique' => 'Ya existe una persona registrada con este correo electrónico.',
            'curp.unique' => 'Ya existe una persona registrada con esta CURP.',
            'rfc.unique' => 'Ya existe una persona registrada con este RFC.',
            'sexo_id.exists' => 'El sexo seleccionado no es válido.',
            'nacionalidad_id.exists' => 'La nacionalidad seleccionada no es válida.',
        ];
    }

    protected function normalizeNullableLower(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : mb_strtolower($value);
    }

    protected function normalizeNullableUpper(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : mb_strtoupper($value);
    }

    protected function catalogOptions(string $catalogoClave): array
    {
        return CatalogoItem::query()
            ->whereHas('catalogo', function ($query) use ($catalogoClave) {
                $query->where('clave', $catalogoClave);
            })
            ->where('activo', true)
            ->orderBy('orden')
            ->orderBy('nombre')
            ->pluck('nombre', 'id')
            ->toArray();
    }

    public function render()
    {
        return view('livewire.personas.create', [
            'c_sexos' => $this->catalogOptions('SEXOS'),
            'c_nacionalidades' => $this->catalogOptions('PAISES'),
        ]);
    }
}