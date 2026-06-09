<x-layouts.customer title="Consultation History">
    <div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
        <h1 class="font-display text-2xl font-bold text-gray-900">Consultation History</h1>

        @if($consultations->isEmpty())
            <x-card class="mt-6">
                <x-empty-state
                    title="No consultations yet"
                    description="Start a chat consultation with an astrologer to see your history here."
                >
                    <x-slot:action>
                        <x-button href="{{ route('astrologers.index') }}" variant="primary">Browse Astrologers</x-button>
                    </x-slot:action>
                </x-empty-state>
            </x-card>
        @else
            <div class="mt-6 space-y-3">
                @foreach($consultations as $c)
                    <x-card>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-cosmic-100 text-sm font-bold text-cosmic-700">
                                    {{ substr($c->astrologer?->user?->name ?? '?', 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-display text-sm font-semibold text-gray-900">{{ $c->astrologer?->user?->name ?? 'Unknown' }}</p>
                                    <p class="text-xs text-gray-500">{{ $c->created_at->format('M d, Y h:i A') }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-4">
                                @if($c->duration_seconds > 0)
                                    <div class="text-right text-xs text-gray-500">
                                        <p>{{ floor($c->duration_seconds / 60) }}m {{ $c->duration_seconds % 60 }}s</p>
                                        <p class="font-medium text-gray-900">₹{{ number_format($c->gross_amount / 100, 2) }}</p>
                                    </div>
                                @endif

                                @php
                                    $statusColors = [
                                        'completed' => 'green', 'active' => 'blue', 'pending' => 'yellow',
                                        'rejected' => 'red', 'cancelled' => 'gray',
                                    ];
                                @endphp
                                <x-badge :color="$statusColors[$c->status->value] ?? 'gray'" :dot="true">
                                    {{ ucfirst($c->status->value) }}
                                </x-badge>

                                @if($c->rating)
                                    <div class="flex items-center gap-0.5 text-gold-500">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="h-3.5 w-3.5 {{ $i <= $c->rating ? 'fill-current' : 'text-gray-200' }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 0 0 .95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 0 0-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 0 0-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 0 0-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 0 0 .951-.69l1.07-3.292Z"/></svg>
                                        @endfor
                                    </div>
                                @endif
                            </div>
                        </div>
                    </x-card>
                @endforeach
            </div>

            <div class="mt-6">{{ $consultations->links() }}</div>
        @endif
    </div>
</x-layouts.customer>
