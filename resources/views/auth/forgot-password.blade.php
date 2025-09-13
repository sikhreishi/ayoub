<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Forgot Password</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    :root{
      --primary-1: #4f46e5;                 /* الأساسي */
      --primary-8: rgba(79, 70, 229, 0.8);  /* ظل بنفس اللون */
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
              <h2 class="text-2xl font-bold tracking-wide">Reset your password</h2>
            </div>

            <p class="mb-6 max-w-sm text-white/90 animate-fade-in delay-200">
              Enter your email and we’ll send you a secure link to set a new password.
            </p>

            <ul class="space-y-3 text-white/90">
              <li class="flex items-center gap-3 animate-fade-in delay-300">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                No password? No problem.
              </li>
              <li class="flex items-center gap-3 animate-fade-in delay-500">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v8m4-4H8"/></svg>
                Works with your account email
              </li>
              <li class="flex items-center gap-3 animate-fade-in delay-700">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M3 7h18M3 12h18M3 17h18"/></svg>
                Fast & secure process
              </li>
            </ul>
          </div>

          <div class="mt-10 text-sm text-white/80 animate-fade-in delay-1000">
            Remembered it?
            @if (Route::has('login'))
              <a href="{{ route('login') }}" class="font-semibold underline decoration-white/40 hover:decoration-white">
                Back to Login
              </a>
            @endif
          </div>
        </div>
      </div>

      <!-- Right / Form -->
      <div class="p-8 sm:p-10">
        <!-- Session Status -->
        @if (session('status'))
          <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            {{ session('status') }}
          </div>
        @endif

        <!-- Global Errors -->
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

        <h1 class="mb-1 text-2xl font-bold tracking-tight animate-fade-in delay-100" style="color: var(--primary-1);">
          Forgot your password?
        </h1>
        <p class="mb-8 text-sm animate-fade-in delay-200" style="color: var(--primary-8);">
          No problem. Just enter your email and we’ll email you a reset link.
        </p>

        <form id="forgotForm" method="POST" action="{{ route('password.email') }}" class="space-y-6" novalidate>
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
              <input
                id="email"
                type="email"
                name="email"
                required
                autocomplete="email"
                placeholder="Enter your account email"
                class="w-full rounded-xl border border-gray-300 bg-white ps-11 pe-3 py-3 text-gray-900 outline-none
                       transition focus:border-transparent focus:ring-4 focus:ring-[var(--primary-8)]"
                value="{{ old('email') }}"
              />
            </div>
            @error('email') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
          </div>

          <!-- Submit -->
          <div class="pt-2 animate-fade-in delay-500">
            <button id="submitBtn" type="submit"
                    class="group relative inline-flex w-full items-center justify-center gap-2 overflow-hidden rounded-xl
                           bg-[var(--primary-1)] px-4 py-3 text-white shadow-lg transition hover:brightness-110 active:scale-[0.99]
                           disabled:opacity-60 disabled:cursor-not-allowed">
              <span class="absolute inset-0 -z-10 opacity-20"
                    style="background: radial-gradient(120% 80% at 50% 0%, #fff 0%, transparent 60%);"></span>
              <svg id="btnSpinner" class="hidden h-5 w-5 animate-spin" viewBox="0 0 24 24" fill="none">
                <circle cx="12" cy="12" r="10" stroke="white" stroke-opacity="0.35" stroke-width="4"></circle>
                <path d="M12 2a10 10 0 0 1 10 10" stroke="white" stroke-width="4" stroke-linecap="round"></path>
              </svg>
              <span id="btnText">Email Password Reset Link</span>
            </button>
          </div>

          <!-- Back / Register -->
          <div class="flex items-center justify-between text-sm animate-fade-in delay-700">
            @if (Route::has('login'))
              <a href="{{ route('login') }}" class="font-medium underline decoration-transparent hover:decoration-[var(--primary-1)]"
                 style="color: var(--primary-1);">Back to Login</a>
            @endif

            @if (Route::has('register'))
              <a href="{{ route('register') }}" class="font-medium underline decoration-transparent hover:decoration-[var(--primary-1)]"
                 style="color: var(--primary-1);"></a>
            @endif
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"
          integrity="sha256-/JqT3SQfawRcv/BIHP3vvZ1jGqv1AwYv1bQ+5GQ0B/g="
          crossorigin="anonymous"></script>

  <script>
    $(function () {
      const $email = $('#email');
      const $btn = $('#submitBtn');
      const $btnText = $('#btnText');
      const $spinner = $('#btnSpinner');

      function syncButton() {
        const ok = $email.val().trim() !== '';
        $btn.prop('disabled', !ok);
      }

      $email.on('input blur', syncButton);
      syncButton();

      $('#forgotForm').on('submit', function () {
        $btn.prop('disabled', true);
        $btnText.text('Sending link...');
        $spinner.removeClass('hidden');
      });
    });
  </script>
</body>
</html>


{{-- <x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Email Password Reset Link') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout> --}}
