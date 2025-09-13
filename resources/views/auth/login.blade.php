<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    :root{
      --primary-1: #4f46e5;                 
      --primary-8: rgba(79, 70, 229, 0.8);  
    }
    @keyframes fadeIn { from{opacity:0; transform:translateY(10px)} to{opacity:1; transform:translateY(0)} }
    .animate-fade-in{ animation: fadeIn .6s ease-out forwards; }
    .delay-100{ animation-delay:.1s } .delay-200{ animation-delay:.2s } .delay-300{ animation-delay:.3s }
    .delay-500{ animation-delay:.5s } .delay-700{ animation-delay:.7s } .delay-900{ animation-delay:.9s } .delay-1000{ animation-delay:1s }
  </style>
</head>
<body class="min-h-screen bg-gray-50 flex items-center justify-center p-4">
  <!-- Background -->
  <div class="pointer-events-none fixed inset-0 -z-10"
       style="background:
        radial-gradient(80vmax 60vmax at 10% 10%, var(--primary-8) 0%, transparent 40%),
        radial-gradient(70vmax 70vmax at 100% 0%, var(--primary-8) 0%, transparent 45%),
        linear-gradient(180deg, #fff, #f6f7fb 40%, #f3f4f6);"></div>

  <div class="w-full max-w-5xl animate-fade-in">
    <div class="grid items-stretch gap-0 overflow-hidden rounded-2xl bg-white shadow-2xl md:grid-cols-2 mx-auto">
      <!-- Left / Brand panel -->
      <div class="relative hidden md:block">
        <div class="absolute inset-0" style="background: linear-gradient(135deg, var(--primary-1), var(--primary-8));"></div>

        <div class="relative z-10 flex h-full flex-col justify-between p-10 text-white">
          <div>
            <div class="mb-8 flex items-center gap-3 animate-fade-in delay-100">
              <div class="h-10 w-10 rounded-xl bg-white/20 backdrop-blur-sm"></div>
              <h2 class="text-2xl font-bold tracking-wide">Welcome Back</h2>
            </div>

            <p class="mb-6 max-w-sm text-white/90 animate-fade-in delay-200">
              Sign in to access your dashboard, manage your purchases, and sync your lists.
            </p>

            <ul class="space-y-3 text-white/90">
              <li class="flex items-center gap-3 animate-fade-in delay-300">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                Secure & fast authentication
              </li>
              <li class="flex items-center gap-3 animate-fade-in delay-500">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v8m4-4H8"/></svg>
                One account for all services
              </li>
              <li class="flex items-center gap-3 animate-fade-in delay-700">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M3 7h18M3 12h18M3 17h18"/></svg>
                Keep your settings in sync
              </li>
            </ul>
          </div>

          <div class="mt-10 text-sm text-white/80 animate-fade-in delay-1000">
            Need an account?
            @if (Route::has('register'))
              <a href="{{ route('register') }}" class="font-semibold underline decoration-white/40 hover:decoration-white">
                Create one
              </a>
            @endif
          </div>
        </div>
      </div>

      <!-- Right / Form -->
      <div class="p-8 sm:p-10">
        
        @if (session('status'))
          <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            {{ session('status') }}
          </div>
        @endif

        
        @if ($errors->any())
          <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <strong class="block">Please fix the following:</strong>
            <ul class="mt-1 list-disc ps-5">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <h1 class="mb-1 text-2xl font-bold tracking-tight animate-fade-in delay-100" style="color: var(--primary-1);">Sign in</h1>
        <p class="mb-8 text-sm animate-fade-in delay-200" style="color: var(--primary-8);">Use your email and password to continue</p>

       
        <form method="POST" action="{{ route('login') }}" class="space-y-6" novalidate>
          @csrf

          <!-- Email -->
          <div class="animate-fade-in delay-300">
            <label for="email" class="mb-1 block text-sm font-medium" style="color: var(--primary-1);">Email</label>
            <div class="relative">
              <span class="pointer-events-none absolute inset-y-0 left-0 flex w-11 items-center justify-center text-gray-400">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16v12H4z" />
                  <path stroke-linecap="round" stroke-linejoin="round" d="M22 6l-10 7L2 6" />
                </svg>
              </span>
              <input id="email" name="email" type="email" autocomplete="username" required
                     class="w-full rounded-xl border border-gray-300 bg-white ps-11 pe-3 py-3 text-gray-900 outline-none transition
                            focus:border-transparent focus:ring-4 focus:ring-[var(--primary-8)]"
                     placeholder="Enter your email" value="{{ old('email') }}"/>
            </div>
            @error('email') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
          </div>

          <!-- Password -->
          <div class="animate-fade-in delay-500">
            <label for="password" class="mb-1 block text-sm font-medium" style="color: var(--primary-1);">Password</label>
            <div class="relative">
              <span class="pointer-events-none absolute inset-y-0 left-0 flex w-11 items-center justify-center text-gray-400">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M8 11V7a4 4 0 118 0v4" />
                  <rect x="6" y="11" width="12" height="10" rx="2" />
                </svg>
              </span>
              <input id="password" name="password" type="password" autocomplete="current-password" required
                     class="w-full rounded-xl border border-gray-300 bg-white ps-11 pe-11 py-3 text-gray-900 outline-none transition
                            focus:border-transparent focus:ring-4 focus:ring-[var(--primary-8)]"
                     placeholder="Enter your password"/>
              <button type="button" id="togglePass"
                      class="absolute inset-y-0 right-0 flex w-11 items-center justify-center text-gray-400 hover:text-gray-600"
                      aria-label="Show password">
                <svg id="eyeIcon" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round"
                        d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"/>
                  <circle cx="12" cy="12" r="3"/>
                </svg>
              </button>
            </div>
            @error('password') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
          </div>

          <!-- Remember + Forgot -->
          <div class="flex flex-wrap items-center justify-between gap-3 animate-fade-in delay-700">
            <label for="remember_me" class="inline-flex select-none items-center gap-2">
              <input id="remember_me" name="remember" type="checkbox"
                     class="h-4 w-4 rounded border-gray-300 text-[var(--primary-1)] focus:ring-[var(--primary-8)]">
              <span class="text-sm" style="color: var(--primary-8);">Remember me</span>
            </label>

            @if (Route::has('password.request'))
              <a href="{{ route('password.request') }}"
                 class="text-sm font-medium underline decoration-transparent transition hover:decoration-[var(--primary-1)]"
                 style="color: var(--primary-1);">
                Forgot password?
              </a>
            @endif
          </div>

          <!-- Submit -->
          <div class="pt-2 animate-fade-in delay-900">
            <button type="submit"
                    class="group relative inline-flex w-full items-center justify-center gap-2 overflow-hidden rounded-xl
                           bg-[var(--primary-1)] px-4 py-3 text-white shadow-lg transition hover:brightness-110 active:scale-[0.99]">
              <span class="absolute inset-0 -z-10 opacity-20"
                    style="background: radial-gradient(120% 80% at 50% 0%, #fff 0%, transparent 60%);"></span>
              <svg class="h-5 w-5 opacity-90" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M12 5l7 7-7 7"/>
              </svg>
              Log In
            </button>
          </div>

          <!-- Sign up prompt -->
          {{-- @if (Route::has('register'))
            <p class="text-center text-sm animate-fade-in delay-1000" style="color: var(--primary-8);">
              Don’t have an account?
              <a href="{{ route('register') }}" class="font-semibold underline decoration-transparent hover:decoration-[var(--primary-1)]"
                 style="color: var(--primary-1);">Sign up</a>
            </a>
          @endif --}}
        </form>
      </div>
    </div>
  </div>

  <script>
    // Password toggle
    const pass = document.getElementById('password');
    const btn  = document.getElementById('togglePass');
    const eye  = document.getElementById('eyeIcon');
    if (btn && pass && eye) {
      btn.addEventListener('click', () => {
        const show = pass.type === 'password';
        pass.type = show ? 'text' : 'password';
        btn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
        eye.innerHTML = show
          ? '<path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18"/><path stroke-linecap="round" stroke-linejoin="round" d="M10.58 10.58A3 3 0 0112 9c1.657 0 3 1.343 3 3 0 .42-.085.82-.24 1.18M9.88 16.12C8.77 15.79 7.78 15.18 6.95 14.5 5.04 12.96 4 12 4 12s3.5-7 10-7c1.28 0 2.46.24 3.54.66"/>'
          : '<path stroke-linecap="round" stroke-linejoin="round" d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/>';
      });
    }
  </script>
</body>
</html>



{{-- <x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
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
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout> --}}