@auth
    {{-- Simple copyright bar for authenticated users --}}
    <footer class="border-t border-gray-100 bg-surface-alt py-4 text-center text-xs text-gray-400">
        &copy; {{ date('Y') }} Astrokart. All rights reserved.
    </footer>
@else
    {{-- Full footer for guests / marketing pages --}}
    <footer class="border-t border-gray-100 bg-night-950 text-gray-300">
        <div class="mx-auto max-w-5xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-8 md:grid-cols-4">
                <div class="md:col-span-2">
                    <h3 class="font-display text-lg font-bold text-white">
                        Astro<span class="text-gold-400">kart</span>
                    </h3>
                    <p class="mt-3 max-w-sm text-sm leading-relaxed text-gray-400">
                        Connect with verified Vedic astrologers for personalized consultations. Get guidance on life, career, relationships, and more.
                    </p>
                </div>
                <div>
                    <h4 class="text-xs font-semibold tracking-wider text-gray-400 uppercase">Quick Links</h4>
                    <ul class="mt-4 space-y-2 text-sm">
                        @if($featureAstrologers ?? false)
                            <li><a href="{{ route('astrologers.index') }}" class="transition hover:text-gold-400">Browse Astrologers</a></li>
                        @endif
                        <li><a href="{{ route('home') }}" class="transition hover:text-gold-400">Home</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-xs font-semibold tracking-wider text-gray-400 uppercase">Support</h4>
                    <ul class="mt-4 space-y-2 text-sm">
                        <li><a href="#" class="transition hover:text-gold-400">Help Center</a></li>
                        <li><a href="#" class="transition hover:text-gold-400">Privacy Policy</a></li>
                        <li><a href="#" class="transition hover:text-gold-400">Terms of Service</a></li>
                    </ul>
                </div>
            </div>
            <div class="mt-10 border-t border-white/10 pt-6 text-center text-xs text-gray-500">
                &copy; {{ date('Y') }} Astrokart. All rights reserved.
            </div>
        </div>
    </footer>
@endauth
