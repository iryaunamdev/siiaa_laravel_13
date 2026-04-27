{{--
|--------------------------------------------------------------------------
| UI Component: Accordion
|--------------------------------------------------------------------------
|
| Propósito:
| Contenedor para grupos de secciones colapsables.
|
| Uso principal:
| - Formularios extensos
| - Filtros
| - Lineamientos o contenidos largos
| - Menús laterales mediante variant="nav"
|
| Props:
| - multiple: bool
|   Permite mantener abiertos varios elementos al mismo tiempo.
|
| - persist: bool
|   Guarda el estado del accordion en localStorage.
|
| - persistKey: string|null
|   Llave única para persistencia. Se recomienda definirla manualmente.
|
| - variant: string
|   default | form | filter | nav
|
| - spacing: string
|   none | sm | md
|
|--------------------------------------------------------------------------
--}}

@props([
    'multiple' => false,
    'persist' => false,
    'persistKey' => null,
    'variant' => 'default',
    'spacing' => 'sm',
])

@php
    $resolvedPersistKey = $persistKey ?: 'siiaa.accordion.' . md5($attributes->get('id') ?? uniqid());

    $spacingClasses =
        [
            'none' => 'space-y-0',
            'sm' => 'space-y-2',
            'md' => 'space-y-3',
        ][$spacing] ?? 'space-y-2';

    $variantClasses =
        [
            'default' => '',
            'form' => '',
            'filter' => '',
            'nav' => 'space-y-1',
        ][$variant] ?? '';
@endphp

<div x-data="{
    multiple: {{ $multiple ? 'true' : 'false' }},
    persist: {{ $persist ? 'true' : 'false' }},
    key: @js($resolvedPersistKey),
    openItems: [],

    init() {
        if (this.persist) {
            const stored = localStorage.getItem(this.key);

            if (stored) {
                try {
                    this.openItems = JSON.parse(stored);
                } catch (e) {
                    this.openItems = [];
                }
            }

            this.$watch('openItems', value => {
                localStorage.setItem(this.key, JSON.stringify(value));
            });
        }
    },

    isOpen(name) {
        return this.openItems.includes(name);
    },

    toggle(name) {
        if (this.isOpen(name)) {
            this.openItems = this.openItems.filter(item => item !== name);
            return;
        }

        this.openItems = this.multiple ? [...this.openItems, name] : [name];
    }
}"
    {{ $attributes->merge([
        'class' => trim("{$spacingClasses} {$variantClasses}"),
    ]) }}>
    {{ $slot }}
</div>
