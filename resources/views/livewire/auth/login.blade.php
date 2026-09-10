<x-layouts::auth :title="__('Log in')">
    <div class="flex flex-col gap-6 mt-4">
        <x-auth-header :title="__('Ingresa a tu cuenta')" :description="__('Utiliza tu cuenta de la Red IRyA para acceder')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-6">
            @csrf

            <!-- Username -->
            <x-ui.input name="username" :label="__('Nombre de usuario')" :value="old('username')" type="text" required autofocus
                autocomplete="username" placeholder="Nombre de usuario (red IRyA)" />

            <!-- Password -->
            <div class="relative">
                <x-ui.input name="password" :label="__('Contraseña')" type="password" required autocomplete="current-password"
                    :placeholder="__('Contraseña')" viewable />

                @if (Route::has('password.request'))
                    <flux:link class="absolute top-0 text-sm end-0" :href="route('password.request')" wire:navigate>
                        {{ __('Olvidó su conraseña?') }}
                    </flux:link>
                @endif
            </div>

            <!-- Remember Me -->
            <xcheckbox name="remember" :label="__('Recordarme')" :checked="old('remember')" />

            <div class="flex items-center justify-end">
                <x-ui.button variant="primary" type="submit" class="w-full" data-test="login-button">
                    {{ __('Ingresar') }}
                </x-ui.button>
            </div>
        </form>

        @if (Route::has('register'))
            <div class="space-x-1 text-sm text-center rtl:space-x-reverse text-zinc-600 dark:text-zinc-400">
                <span>{{ __('Don\'t have an account?') }}</span>
                <flux:link :href="route('register')" wire:navigate>{{ __('Sign up') }}</flux:link>
            </div>
        @endif
    </div>
</x-layouts::auth>
