<nav class="mb-4 flex gap-1 overflow-x-auto rounded-xl bg-gray-100 p-1 sm:mb-6">
    @php
        $tabs = [
            ['route' => 'horoscope.show', 'label' => 'Chart'],
            ['route' => 'horoscope.transits', 'label' => 'Transits'],
            ['route' => 'horoscope.hora', 'label' => 'Hora'],
            ['route' => 'horoscope.analysis', 'label' => 'Analysis'],
        ];
    @endphp
    @foreach($tabs as $tab)
        @php $active = request()->routeIs($tab['route']); @endphp
        <a href="{{ route($tab['route']) }}"
           class="shrink-0 rounded-lg px-4 py-2 text-sm font-medium transition {{ $active ? 'bg-white text-cosmic-700 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
            {{ $tab['label'] }}
        </a>
    @endforeach
</nav>
