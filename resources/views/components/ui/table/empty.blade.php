@props([
    'colspan' => 1,
    'message' => null,
])

<tr>
    <td colspan="{{ $colspan }}"
        {{ $attributes->merge([
            'class' => 'px-[var(--table-cell-x)] py-10 text-center text-[length:var(--table-text)] text-slate-500',
        ]) }}>
        <div class="flex flex-col items-center justify-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-slate-300" viewBox="0 0 20 20" fill="currentColor"
                aria-hidden="true">
                <path fill-rule="evenodd"
                    d="M4.25 3A2.25 2.25 0 002 5.25v9.5A2.25 2.25 0 004.25 17h11.5A2.25 2.25 0 0018 14.75v-9.5A2.25 2.25 0 0015.75 3H4.25ZM3.5 7.5h13v7.25a.75.75 0 01-.75.75H4.25a.75.75 0 01-.75-.75V7.5Zm.75-3h11.5a.75.75 0 01.75.75V6h-13v-.75a.75.75 0 01.75-.75Z"
                    clip-rule="evenodd" />
            </svg>

            <span>
                {{ $message ?? $slot }}
            </span>
        </div>
    </td>
</tr>
