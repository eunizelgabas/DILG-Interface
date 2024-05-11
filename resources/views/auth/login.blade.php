<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    {{-- <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" name="remember">
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form> --}}

    <div class="animated-wiper"></div>
    </div>
        <div class="min-w-screen min-h-screen  flex items-center justify-center px-5 py-5">
            <div class="bg-white rounded-3xl shadow-xl w-full overflow-hidden" style="max-width:1000px">
                <div class="md:flex w-full ">
                    <div class="hidden md:flex md:flex-col md:items-center md:justify-center w-1/2 ">
                        <img src="/images/Tngkrw Icon.png" class="w-[200px] h-[200px] object-fit mx-auto mt-18" alt="">
                        <h1 class="font-bold text-2xl mx-auto mt-4">TANGKARAW</h1>
                        <h5 class="font-bold text-sm mx-auto">DILG - Bohol Province </h5>
                        {{-- <h5 class="font-bold text-2xl mx-auto">Bohol Province</h5> --}}
                    </div>

                    <div class="w-full md:w-1/2 py-10 px-5 md:px-10  bg-blue-200">
                        <div class="text-center mb-10">

                            <h1 class="font-bold text-3xl text-gray-900">Login to TANGKARAW</h1>
                            <p class="mt-5">Enter your information to log in</p>
                        </div>
                        <div>

                            @if(session('error'))
                                <div class="bg-red-200 px-6 py-4 my-4 rounded-md text-lg flex items-center mx-auto max-w-lg">
                                    <svg viewBox="0 0 24 24" class="text-red-600 w-5 h-5 sm:w-5 sm:h-5 mr-3">
                                        <path fill="currentColor"
                                            d="M11.983,0a12.206,12.206,0,0,0-8.51,3.653A11.8,11.8,0,0,0,0,12.207,11.779,11.779,0,0,0,11.8,24h.214A12.111,12.111,0,0,0,24,11.791h0A11.766,11.766,0,0,0,11.983,0ZM10.5,16.542a1.476,1.476,0,0,1,1.449-1.53h.027a1.527,1.527,0,0,1,1.523,1.47,1.475,1.475,0,0,1-1.449,1.53h-.027A1.529,1.529,0,0,1,10.5,16.542ZM11,12.5v-6a1,1,0,0,1,2,0v6a1,1,0,1,1-2,0Z">
                                        </path>
                                    </svg>
                                    <span class="text-red-800 text-sm">  {{ session('error') }} </span>
                                </div>
                            @endif
                            <form method="POST" action="{{ route('login') }}">
                                @csrf

                                <div>
                                    <x-input-label for="email" value="Email" />
                                    <x-text-input
                                        id="email"
                                        type="email" name="email" :value="old('email')"
                                        class="mt-1 block w-full"
                                        required
                                        autofocus
                                        autocomplete="email"
                                    />
                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                </div>

                                <div class="mt-4">
                                    <x-input-label for="password" value="Password" />
                                    <div class="relative">
                                        <x-text-input
                                            id="password"
                                            type="password"
                                            name="password"
                                            class="mt-1 block w-full pr-10"
                                            required
                                            autocomplete="current-password"
                                        />
                                        <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 flex items-center px-3 focus:outline-none">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6">
                                                <!-- Closed eye icon -->
                                                <path x-show="!showPassword" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                <!-- Open eye icon -->
                                                <path x-show="showPassword" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                            </svg>


                                        </button>
                                    </div>
                                    <x-input-error :messages="$errors->get('password')" class="mt-2" />

                                <div class="flex items-center mt-4">

                                    <button class="w-full justify-center inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                        <span class="text-white">Login</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg"></div>
            <div class="bg bg2"></div>
            <div class="bg bg3"></div>


        </div>

</x-guest-layout>

<style scoped>
    .bg {
        animation: slide 3s ease-in-out infinite alternate;
        background-image: linear-gradient(-60deg, rgb(221, 216, 216) 50%, white 50%);
        bottom: 0;
        left: -50%;
        opacity: .5;
        position: fixed;
        right: -50%;
        top: 0;
        z-index: -1;
    }

    .bg2 {
        animation-direction: alternate-reverse;
        animation-duration: 4s;
    }

    .bg3 {
        animation-duration: 5s;
    }

    @keyframes slide {
        0% {
            transform: translateX(-25%);
        }

        100% {
            transform: translateX(25%);
        }
    }

</style>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const passwordInput = document.getElementById('password');
        const togglePasswordButton = document.getElementById('togglePassword');
        let passwordVisible = false;

        togglePasswordButton.addEventListener('click', function () {
            passwordVisible = !passwordVisible;
            const type = passwordVisible ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            togglePasswordButton.querySelector('svg').classList.toggle('text-gray-400');
            togglePasswordButton.querySelector('svg').classList.toggle('text-gray-600');
        });

        function checkVisibility() {
            togglePasswordButton.style.visibility = passwordInput.value.length > 0 ? 'visible' : 'hidden';
        }

        // Check initial visibility
        checkVisibility();

        passwordInput.addEventListener('input', checkVisibility);

        // Keep eye button visible even if user clicks elsewhere
        document.addEventListener('click', function (event) {
            if (!passwordInput.contains(event.target) && !togglePasswordButton.contains(event.target)) {
                checkVisibility();
            }
        });
    });
</script>
