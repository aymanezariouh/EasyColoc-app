<x-guest-layout>
    <h1 class="text-2xl font-semibold text-slate-900">Verify email</h1>
    <p class="mt-1 text-sm text-slate-600">
        Thanks for signing up. Please verify your email address using the link we sent.
    </p>

    @if (session('status') == 'verification-link-sent')
        <div class="mt-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            A new verification link has been sent to your email address.
        </div>
    @endif

    <div class="mt-6 flex items-center justify-between gap-2">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-primary-button>
                {{ __('Resend Verification Email') }}
            </x-primary-button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-ghost">
                {{ __('Log Out') }}
            </button>
        </form>
    </div>
</x-guest-layout>
