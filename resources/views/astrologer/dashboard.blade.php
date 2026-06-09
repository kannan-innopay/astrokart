<x-layouts.astrologer title="Dashboard">
    <div class="mb-6 flex items-center justify-between">
        <h1 class="font-display text-2xl font-bold text-gray-900">Dashboard</h1>
        @if($astrologer->isApproved())
            <x-online-toggle :is-online="$astrologer->is_online" />
        @endif
    </div>

    {{-- Active consultation banner --}}
    @if($activeConsultation)
        <div class="mb-6 rounded-2xl border-2 border-emerald-300 bg-emerald-50 p-5">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-100">
                        <span class="h-3 w-3 animate-pulse rounded-full bg-emerald-500"></span>
                    </div>
                    <div>
                        <p class="font-display text-sm font-semibold text-emerald-900">Active Consultation</p>
                        <p class="text-xs text-emerald-700">with {{ $activeConsultation->user->name }}</p>
                    </div>
                </div>
                <x-button href="{{ route('consultation.chat', $activeConsultation) }}" variant="primary" size="sm">
                    Open Chat
                </x-button>
            </div>
        </div>
    @endif

    {{-- Pending consultation requests --}}
    @if($pendingRequests->isNotEmpty())
        <div class="mb-6 space-y-3">
            @foreach($pendingRequests as $request)
                <div class="rounded-2xl border-2 border-cosmic-300 bg-gradient-to-r from-cosmic-50 to-white p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-cosmic-100 font-bold text-cosmic-700">
                                {{ substr($request->user->name, 0, 1) }}
                            </div>
                            <div>
                                <p class="font-display text-base font-semibold text-gray-900">New Chat Request</p>
                                <p class="text-sm text-gray-600">{{ $request->user->name }} wants to consult with you</p>
                                <p class="mt-0.5 text-xs text-gray-400">₹{{ number_format($request->price_per_minute / 100) }}/min &middot; {{ $request->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <form method="POST" action="{{ route('astrologer.consultation.accept', $request) }}">
                                @csrf
                                <x-button type="submit" variant="primary" size="sm">Accept</x-button>
                            </form>
                            <form method="POST" action="{{ route('astrologer.consultation.reject', $request) }}">
                                @csrf
                                <x-button type="submit" variant="ghost" size="sm">Decline</x-button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Status banner --}}
    @unless($astrologer->isApproved())
        <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 p-4">
            <p class="text-sm font-medium text-amber-800">
                Your account status is <strong>{{ $astrologer->status->value }}</strong>.
                @if($astrologer->status->value === 'applied')
                    Your profile is under review. We'll notify you once approved.
                @endif
            </p>
        </div>
    @endunless

    {{-- Stats --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-stat-card label="Rating" :value="number_format($astrologer->rating, 1)" icon="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" />
        <x-stat-card label="Total Reviews" :value="$astrologer->total_reviews" icon="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.076-4.076a1.526 1.526 0 0 1 1.037-.443 48.282 48.282 0 0 0 5.68-.494c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
        <x-stat-card label="Price / Min" :value="'₹' . number_format($astrologer->price_per_minute / 100)" icon="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
        <x-stat-card label="Experience" :value="$astrologer->years_of_experience . ' years'" icon="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
    </div>

    {{-- Quick links --}}
    <div class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-2">
        <a href="{{ route('astrologer.profile.edit') }}" class="group flex items-center gap-4 rounded-2xl border border-gray-100 bg-white p-5 shadow-sm transition hover:border-cosmic-200 hover:shadow-md">
            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-cosmic-50 text-cosmic-600 transition group-hover:bg-cosmic-100">
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/></svg>
            </div>
            <div>
                <h3 class="font-display font-semibold text-gray-900">Edit Profile</h3>
                <p class="text-sm text-gray-500">Update your bio, pricing, and expertise</p>
            </div>
        </a>

        <a href="{{ route('astrologer.availability.edit') }}" class="group flex items-center gap-4 rounded-2xl border border-gray-100 bg-white p-5 shadow-sm transition hover:border-cosmic-200 hover:shadow-md">
            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-cosmic-50 text-cosmic-600 transition group-hover:bg-cosmic-100">
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/></svg>
            </div>
            <div>
                <h3 class="font-display font-semibold text-gray-900">Set Availability</h3>
                <p class="text-sm text-gray-500">Manage your weekly schedule</p>
            </div>
        </a>
    </div>
</x-layouts.astrologer>
