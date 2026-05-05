@props(['name'])

<span
    {{ $attributes->merge([
        'class' => 'inline-flex shrink-0 items-center justify-center overflow-visible leading-none',
    ]) }}>
    @switch($name)
        @case('trash')
            <svg viewBox="0 0 24 24" fill="currentColor" class="block h-full w-full overflow-visible"
                preserveAspectRatio="xMidYMid meet">
                <path
                    d="M8 1.5V2.5H3C2.44772 2.5 2 2.94772 2 3.5V4.5C2 5.05228 2.44772 5.5 3 5.5H21C21.5523 5.5 22 5.05228 22 4.5V3.5C22 2.94772 21.5523 2.5 21 2.5H16V1.5C16 0.947715 15.5523 0.5 15 0.5H9C8.44772 0.5 8 0.947715 8 1.5Z" />
                <path
                    d="M3.9231 7.5H20.0767L19.1344 20.2216C19.0183 21.7882 17.7135 23 16.1426 23H7.85724C6.28636 23 4.98148 21.7882 4.86544 20.2216L3.9231 7.5Z" />
            </svg>
        @break

        @case('edit')
            <svg {{ $attributes->merge(['class' => 'size-4']) }} viewBox="0 0 16 16" xmlns="http://www.w3.org/zinc0/svg"
                aria-hidden="true">
                <path fill="currentColor"
                    d="M15.49 7.3h-1.16v6.35H1.67V3.28H8V2H1.67A1.21 1.21 0 0 0 .5 3.28v10.37a1.21 1.21 0 0 0 1.17 1.25h12.66a1.21 1.21 0 0 0 1.17-1.25z" />
                <path fill="currentColor"
                    d="M10.56 2.87 6.22 7.22l-.44.44-.08.08-1.52 3.16a1.08 1.08 0 0 0 1.45 1.45l3.14-1.53.53-.53.43-.43 4.34-4.36.45-.44.25-.25a2.18 2.18 0 0 0 0-3.08 2.17 2.17 0 0 0-1.53-.63 2.19 2.19 0 0 0-1.54.63l-.7.69-.45.44zM5.51 11l1.18-2.43 1.25 1.26zm2-3.36 3.9-3.91 1.3 1.31L8.85 9zm5.68-5.31a.91.91 0 0 1 .65.27.93.93 0 0 1 0 1.31l-.25.24-1.3-1.3.25-.25a.88.88 0 0 1 .69-.25z" />
            </svg>
        @break

        @case('chevron-down')
            <svg viewBox="0 0 20 20" fill="currentColor" class="block h-full w-full overflow-visible"
                preserveAspectRatio="xMidYMid meet">
                <path fill-rule="evenodd"
                    d="M5.23 7.21a.75.75 0 011.06.02L10 11.17l3.71-3.94a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                    clip-rule="evenodd" />
            </svg>
        @break

        @case('plus')
            <svg {{ $attributes->merge(['class' => 'size-5']) }} viewBox="0 0 20 20" xmlns="http://www.w3.org/zinc0/svg"
                aria-hidden="true">
                <path fill="currentColor" fill-rule="evenodd"
                    d="M9 17a1 1 0 102 0v-6h6a1 1 0 100-2h-6V3a1 1 0 10-2 0v6H3a1 1 0 000 2h6v6z" clip-rule="evenodd" />
            </svg>
        @break

        @case('exclamation-triangle')
            <svg {{ $attributes->merge(['class' => 'size-5 text-amber-500']) }} viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg" fill="none" aria-hidden="true">

                <path
                    d="M5.313 20h13.374c1.505 0 2.471-1.6 1.77-2.931L13.77 4.363c-.75-1.425-2.79-1.425-3.54 0L3.543 17.068C2.842 18.4 3.808 20 5.313 20Z"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />

                <path d="M12 9v4" stroke="currentColor" stroke-width="2" stroke-linecap="round" />

                <circle cx="12" cy="17" r="1" fill="currentColor" />
            </svg>
        @break

        @case('cancel')
            <svg {{ $attributes->merge(['class' => 'size-5']) }} viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"
                aria-hidden="true">

                <path fill="currentColor"
                    d="M420.48 121.81 390.19 91.52 256 225.71 121.81 91.52 91.52 121.81 225.71 256 91.52 390.19 121.81 420.48 256 286.29 390.19 420.48 420.48 390.19 286.29 256z" />
            </svg>
        @break

        @default
            <span {{ $attributes->merge(['class' => 'size-4']) }}>
                {{ $name }}
            </span>
    @endswitch
</span>
