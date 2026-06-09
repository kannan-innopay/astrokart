@props([
    'slots' => [],
])

@php
    $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    $existingSlots = collect($slots)->groupBy('day_of_week');
@endphp

<div x-data="{
    slots: @js($existingSlots->map(fn($daySlots) => $daySlots->map(fn($s) => ['start_time' => substr($s['start_time'], 0, 5), 'end_time' => substr($s['end_time'], 0, 5)])->values())->toArray()),
    addSlot(day) {
        if (!this.slots[day]) this.slots[day] = [];
        this.slots[day].push({ start_time: '09:00', end_time: '17:00' });
    },
    removeSlot(day, index) {
        this.slots[day].splice(index, 1);
    }
}">
    <div class="space-y-3">
        @foreach($days as $dayIndex => $dayName)
            <div class="rounded-xl border border-gray-100 bg-white p-4">
                <div class="flex items-center justify-between">
                    <h4 class="text-sm font-semibold text-gray-700">{{ $dayName }}</h4>
                    <button type="button" @click="addSlot({{ $dayIndex }})" class="text-xs font-medium text-cosmic-600 hover:text-cosmic-800">
                        + Add Slot
                    </button>
                </div>

                <template x-for="(slot, slotIndex) in (slots[{{ $dayIndex }}] || [])" :key="slotIndex">
                    <div class="mt-3 flex items-center gap-2">
                        <input type="hidden" :name="'slots[' + (Object.keys(slots).slice(0, {{ $dayIndex }}).reduce((sum, k) => sum + (slots[k]?.length || 0), 0) + slotIndex) + '][day_of_week]'" value="{{ $dayIndex }}">
                        <input type="time" :name="'slots[' + (Object.keys(slots).slice(0, {{ $dayIndex }}).reduce((sum, k) => sum + (slots[k]?.length || 0), 0) + slotIndex) + '][start_time]'" x-model="slot.start_time" class="rounded-lg border border-gray-200 px-3 py-1.5 text-sm focus:border-cosmic-400 focus:outline-none focus:ring-2 focus:ring-cosmic-200">
                        <span class="text-xs text-gray-400">to</span>
                        <input type="time" :name="'slots[' + (Object.keys(slots).slice(0, {{ $dayIndex }}).reduce((sum, k) => sum + (slots[k]?.length || 0), 0) + slotIndex) + '][end_time]'" x-model="slot.end_time" class="rounded-lg border border-gray-200 px-3 py-1.5 text-sm focus:border-cosmic-400 focus:outline-none focus:ring-2 focus:ring-cosmic-200">
                        <button type="button" @click="removeSlot({{ $dayIndex }}, slotIndex)" class="text-red-400 hover:text-red-600">
                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </template>

                <template x-if="!slots[{{ $dayIndex }}]?.length">
                    <p class="mt-2 text-xs text-gray-400">No slots — day off</p>
                </template>
            </div>
        @endforeach
    </div>
</div>
