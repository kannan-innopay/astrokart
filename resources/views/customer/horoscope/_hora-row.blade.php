<tr class="{{ $hora['is_current'] ? 'bg-cosmic-50 font-semibold' : 'hover:bg-gray-50/50' }}">
    <td class="px-3 py-2 text-gray-700 whitespace-nowrap sm:px-6 sm:py-2.5">
        <span class="text-xs sm:text-sm">{{ $hora['start_label'] }}–{{ $hora['end_label'] }}</span>
        @if($hora['is_current'])
            <span class="ml-1 rounded bg-cosmic-600 px-1 py-0.5 text-[8px] font-bold text-white sm:px-1.5 sm:text-[10px]" x-text="ui('now')"></span>
        @endif
    </td>
    <td class="px-3 py-2 sm:px-6 sm:py-2.5">
        @php $hc = $grahaColors[$hora['planet']] ?? ['text-gray-700', 'bg-gray-50', 'border-gray-200']; @endphp
        <span class="inline-flex items-center gap-1 text-xs whitespace-nowrap {{ $hc[0] }} sm:gap-1.5 sm:text-sm" x-text="graha('{{ $hora['planet'] }}')"></span>
    </td>
    <td class="hidden px-3 py-2 text-gray-500 sm:table-cell sm:px-6 sm:py-2.5" x-text="quality('{{ $hora['data']['quality'] }}')"></td>
    <td class="px-3 py-2 sm:px-6 sm:py-2.5">
        <span class="inline-flex items-center gap-1">
            <span class="h-2.5 w-2.5 shrink-0 rounded-full sm:h-3 sm:w-3 {{ $colorSwatches[$hora['data']['color']] ?? 'bg-gray-400' }}"></span>
            <span class="hidden text-xs text-gray-600 sm:inline">{{ $hora['data']['color'] }}</span>
        </span>
    </td>
    @if($personalized && ($hora['personal'] ?? false))
        @php
            $f = $hora['personal']['favorability'];
            $fc = $favColors[$f] ?? $favColors['neutral'];
        @endphp
        <td class="px-2 py-2 sm:px-6 sm:py-2.5">
            <span class="inline-flex items-center rounded-md px-1.5 py-0.5 text-[9px] font-semibold whitespace-nowrap sm:px-2 sm:text-[10px] {{ $fc[0] }} {{ $fc[1] }}" x-text="fav('{{ $f }}')"></span>
        </td>
    @elseif($personalized)
        <td class="px-2 py-2 sm:px-6 sm:py-2.5"></td>
    @endif
</tr>
