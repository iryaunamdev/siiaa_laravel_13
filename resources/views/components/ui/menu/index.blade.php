@props([
    'class' => '',
])

<ul {{ $attributes->merge([
    'class' => 'space-y-1 ' . $class,
]) }}>
    {{ $slot }}
</ul>
