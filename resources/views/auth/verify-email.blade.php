<x-guest-layout>
    <h1 class="mb-1 text-xl font-semibold text-gray-900">Verify email</h1>
    <p class="mb-6 text-sm text-gray-600">
        Thanks for signing up. Before getting started, verify your email address by clicking the link we just sent.
    </p>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            A new verification link has been sent to your email address.
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <x-primary-button>
                {{ __('Resend Verification Email') }}
            </x-primary-button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="text-sm text-gray-600 hover:text-gray-900">
                {{ __('Log Out') }}
            </button>
        </form>
    </div>
</x-guest-layout>
