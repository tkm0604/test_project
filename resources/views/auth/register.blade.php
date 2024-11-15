<x-guest-layout>
    <div class="p-5">
    <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>
        <!-- Avatar -->
        <div class="mt-4">
            <x-input-label for="avatar" :value="__('プロフィール画像（任意・1MBまで）')" />

            <x-text-input id="avatar" class="block mt-1 w-full rounded-none" type="file" name="avatar" :value="old('avatar')" />
        </div>
        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Xでログイン -->
        <div class="block mt-4">
            <p class="ms-2 text-m font-bold text-[#1DA1F2]">Xアカウントでユーザー登録される方はこちら</p>
            <label for="" class="inline-flex items-center">
                <span class="ms-2 text-sm text-gray-600">
                <!-- Xでログイン -->
                <a href="{{ url('login/x') }}" class="btn btn-primary flex items-center">
                    <img style="width:40px" src="{{ asset('logo/x_logo.png') }}" alt="">
                    Xアカウントでユーザー登録
                </a>
                </span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</div>
</x-guest-layout>
