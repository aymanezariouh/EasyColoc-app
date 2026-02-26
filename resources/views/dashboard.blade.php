<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <p class="text-sm font-semibold text-indigo-600 tracking-wide uppercase">Overview</p>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight mt-1">
                    Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 18 ? 'afternoon' : 'evening') }}, {{ auth()->user()->name }} 👋
                </h1>
                <p class="text-sm text-gray-500 mt-1">Here's what's happening with your shared spaces.</p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-8 stagger-children">
        {{-- Active Colocation --}}
        @if ($activeMembership)
            <div class="relative overflow-hidden rounded-3xl animate-slide-up">
                {{-- Gradient background --}}
                <div class="absolute inset-0 bg-gradient-to-br from-indigo-600 via-violet-600 to-purple-700 animate-gradient"></div>
                {{-- Decorative shapes --}}
                <div class="absolute top-0 right-0 w-80 h-80 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/3 animate-blob"></div>
                <div class="absolute bottom-0 left-0 w-60 h-60 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/4 animate-blob-delayed"></div>
                <div class="absolute top-1/2 right-1/4 w-32 h-32 bg-white/5 rounded-full animate-float"></div>
                {{-- Grid overlay for texture --}}
                <div class="absolute inset-0 bg-grid-pattern opacity-10"></div>

                <div class="relative p-7 sm:p-9">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
                        <div class="flex items-start gap-4">
                            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white/15 backdrop-blur-sm border border-white/10 shadow-lg flex-shrink-0 animate-float-slow">
                                <svg class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" /></svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-white/60 uppercase tracking-wider">Active Colocation</p>
                                <h2 class="text-2xl font-extrabold text-white mt-1">{{ $activeMembership->colocation->name }}</h2>
                                <div class="flex items-center gap-2 mt-2">
                                    <span class="inline-flex items-center gap-1 bg-white/15 backdrop-blur-sm text-white/90 text-xs font-medium rounded-full px-3 py-1 border border-white/10">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 016.75 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                                        {{ ucfirst($activeMembership->role) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('colocations.show', $activeMembership->colocation) }}"
                           class="inline-flex items-center gap-2 bg-white hover:bg-gray-50 text-indigo-700 font-bold rounded-2xl px-6 py-3.5 text-sm shadow-elevated-lg hover:shadow-xl transition-all duration-300 btn-press group">
                            Open Dashboard
                            <svg class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
                        </a>
                    </div>
                </div>
            </div>
        @else
            {{-- Empty State: No colocation --}}
            <x-card class="text-center py-14 animate-slide-up">
                <div class="relative mx-auto w-24 h-24 mb-6">
                    <div class="absolute inset-0 bg-gradient-to-br from-indigo-200 to-violet-200 rounded-3xl blur-xl opacity-60 animate-pulse-soft"></div>
                    <div class="relative flex h-24 w-24 items-center justify-center rounded-3xl bg-gradient-to-br from-indigo-100 to-violet-100 animate-float">
                        <svg class="h-12 w-12 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" /></svg>
                    </div>
                </div>
                <h3 class="text-xl font-extrabold text-gray-900">No active colocation</h3>
                <p class="text-sm text-gray-500 mt-2 max-w-sm mx-auto">Create your first shared space to start tracking expenses with your housemates.</p>
                <a href="{{ route('colocations.create') }}"
                   class="inline-flex items-center gap-2 mt-6 bg-gradient-to-r from-indigo-600 via-violet-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white rounded-2xl px-7 py-3.5 text-sm font-bold shadow-glow-indigo hover:shadow-xl transition-all duration-300 btn-press animate-gradient group">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    Create Colocation
                    <svg class="h-4 w-4 transition-transform group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
                </a>
            </x-card>
        @endif

        {{-- Pending Invitations --}}
        @if ($pendingInvitations->count() > 0)
            <x-card class="animate-slide-up">
                <div class="flex items-center gap-3 mb-5">
                    <div class="relative">
                        <div class="absolute inset-0 bg-amber-400 rounded-xl blur-md opacity-30 animate-pulse-soft"></div>
                        <div class="relative flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-amber-400 to-orange-500 shadow-md">
                            <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg>
                        </div>
                    </div>
                    <div>
                        <h2 class="text-lg font-extrabold text-gray-900">Pending Invitations</h2>
                        <p class="text-xs text-gray-500">{{ $pendingInvitations->count() }} invitation{{ $pendingInvitations->count() > 1 ? 's' : '' }} waiting</p>
                    </div>
                </div>
                <div class="space-y-2">
                    @foreach ($pendingInvitations as $invitation)
                        <a href="{{ route('invitations.show', $invitation->token) }}"
                           class="flex items-center justify-between gap-4 p-4 rounded-2xl bg-gradient-to-r from-amber-50/80 to-orange-50/40 hover:from-amber-50 hover:to-orange-50/60 border border-amber-100/60 transition-all duration-200 group">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-amber-100 text-amber-700 text-xs font-bold flex-shrink-0">
                                    {{ strtoupper(substr($invitation->colocation->name, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="font-bold text-gray-900 text-sm truncate">{{ $invitation->colocation->name }}</p>
                                    <p class="text-xs text-gray-500">Tap to review</p>
                                </div>
                            </div>
                            <svg class="h-5 w-5 text-amber-400 flex-shrink-0 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" /></svg>
                        </a>
                    @endforeach
                </div>
            </x-card>
        @endif

        {{-- Admin Area --}}
        @if (auth()->user()?->is_admin)
            <x-card class="overflow-hidden animate-slide-up">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="relative">
                            <div class="absolute inset-0 bg-violet-500 rounded-xl blur-md opacity-25 animate-pulse-soft"></div>
                            <div class="relative flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-violet-500 to-purple-600 shadow-md">
                                <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 010 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 010-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            </div>
                        </div>
                        <div>
                            <h2 class="text-lg font-extrabold text-gray-900">Admin Panel</h2>
                            <p class="text-xs text-gray-500">Manage your platform</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.dashboard') }}"
                       class="inline-flex items-center gap-2 bg-gradient-to-r from-violet-600 to-purple-600 hover:from-violet-700 hover:to-purple-700 text-white rounded-2xl px-6 py-3 text-sm font-bold shadow-glow-violet hover:shadow-xl transition-all duration-300 btn-press group">
                        Open Admin
                        <svg class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
                    </a>
                </div>
            </x-card>
        @endif
    </div>
</x-app-layout>
