<x-layouts.auth>
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Iniciar Sesión')" :description="__('Ingrese su correo y contraseña para iniciar sesión')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-4">
            @csrf
            <!-- Correo electronico -->
            <flux:input name="email" :label="__('Correo electrónico')" type="email" required autofocus
                autocomplete="email" placeholder="email@example.com" />

            <!-- Contraseña -->
            <div class="relative">
                <flux:input name="password" :label="__('Contraseña')" type="password" required
                    autocomplete="current-password" :placeholder="__('*********')" viewable />
            </div>

            <!-- Mantener sesion -->
            <flux:checkbox name="remember" :label="__('Mantener sesión')" :checked="old('remember')" />

            <div class="flex items-center justify-end">
                <flux:button variant="primary" type="submit" class="w-full" data-test="login-button">
                    {{ __('Iniciar sesión') }}
                </flux:button>
            </div>
        </form>
    </div>
</x-layouts.auth>
