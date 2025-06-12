<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="logo flex justify-center mb-4">
            <img src="{{ asset('images/adn-hd-removebg-preview.png') }}" alt="" style="width: 150px; height: 150px;">
        </div>

        <div class="text-center mb-4">
            <h2>Masuk ke Akun Anda</h2>
            <p class="text-secondary text-center" style="font-style: italic;">Silakan masuk untuk melanjutkan ke akun ADN Anda</p>
        </div>

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')"
                required autofocus autocomplete="username" placeholder="Masukkan email akun"/>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="my-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                autocomplete="current-password" placeholder="Masukkan password (min 8 karakter)"/>

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        {{-- <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" name="remember">
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
            </label>
        </div> --}}

        <button class="btn btn-primary w-100 mb-2">
            {{ __('Log in') }}
        </button>

        <div class="forgot-pass text-center">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                    href="{{ route('password.request') }}">
                    {{ __('Lupa Kata Sandi?') }}
                </a>
            @endif
        </div>

        <div class="register my-4 text-center">
            <p class="text-sm text-gray-600 dark:text-gray-400">Belum punya akun? <a href="{{ route('register') }}"
                    class="underline text-sm text-blue-600 dark:text-gray-400 hover:text-gray-900">
                    {{ __('Daftar Sekarang') }}</a></p>
        </div>
    </form>
</x-guest-layout>
