<x-layouts.customer title="Chat Consultation">
    @php
        $chatConfig = [
            'consultationId' => $consultation->id,
            'userId' => $currentUser->id,
            'userName' => $currentUser->name,
            'status' => $consultation->status->value,
            'startedAt' => $consultation->started_at?->toIso8601String(),
            'pricePerMinute' => $consultation->price_per_minute,
            'walletBalance' => $currentUser->wallet?->balance ?? 0,
            'otherName' => $currentUser->id === $consultation->user_id
                ? $consultation->astrologer->user->name
                : $consultation->user->name,
            'isUser' => $currentUser->id === $consultation->user_id,
        ];
        $existingMessages = $messages->map(fn($m) => [
            'id' => $m->id,
            'type' => $m->type ?? 'text',
            'message' => $m->message,
            'sender_id' => $m->sender_id,
            'sender_name' => $m->sender->name,
            'metadata' => $m->metadata,
            'created_at' => $m->created_at->toIso8601String(),
        ])->values()->toArray();
        $hasChart = (bool) $currentUser->birth_chart;
    @endphp

    <div class="mx-auto flex h-[calc(100vh-4rem)] max-w-4xl flex-col"
         x-data="{
            consultationId: {{ $chatConfig['consultationId'] }},
            userId: {{ $chatConfig['userId'] }},
            userName: '{{ addslashes($chatConfig['userName']) }}',
            status: '{{ $chatConfig['status'] }}',
            startedAt: '{{ $chatConfig['startedAt'] ?? '' }}',
            pricePerMinute: {{ $chatConfig['pricePerMinute'] }},
            walletBalance: {{ $chatConfig['walletBalance'] }},
            otherName: {{ Js::from($chatConfig['otherName']) }},
            isUser: {{ $chatConfig['isUser'] ? 'true' : 'false' }},
            hasChart: {{ $hasChart ? 'true' : 'false' }},
            chartRequested: {{ Js::from(collect($existingMessages)->contains(fn($m) => ($m['type'] ?? '') === 'chart_request')) }},
            chartShared: {{ Js::from(collect($existingMessages)->contains(fn($m) => ($m['type'] ?? '') === 'chart_shared')) }},
            messages: {{ Js::from($existingMessages) }},
            pendingIds: new Set(),
            newMessage: '',
            sending: false,
            isTyping: false,
            typingTimeout: null,
            timerDisplay: '00:00',
            currentCost: 0,
            timerInterval: null,
            msgCounter: 0,

            isMine(msg) {
                return Number(msg.sender_id) === Number(this.userId);
            },

            init() {
                this.$nextTick(() => this.scrollToBottom());
                if (this.status === 'active' && this.startedAt) { this.startTimer(); }

                if (window.Echo) {
                    window.Echo.join('consultation.' + this.consultationId)
                        .listen('.message.sent', (e) => {
                            if (Number(e.sender_id) === Number(this.userId)) {
                                const tempMsg = this.messages.find(m => String(m.id).startsWith('tmp'));
                                if (tempMsg) tempMsg.id = e.id;
                                return;
                            }
                            if (!this.messages.find(m => m.id === e.id)) {
                                this.messages = [...this.messages, e];
                                this.$nextTick(() => this.scrollToBottom());
                            }
                            this.isTyping = false;
                        })
                        .listen('.chart.requested', (e) => {
                            this.chartRequested = true;
                            if (!this.messages.find(m => m.id === e.id)) {
                                this.messages = [...this.messages, e];
                                this.$nextTick(() => this.scrollToBottom());
                            }
                        })
                        .listen('.chart.shared', (e) => {
                            this.chartShared = true;
                            if (!this.messages.find(m => m.id === e.id)) {
                                this.messages = [...this.messages, e];
                                this.$nextTick(() => this.scrollToBottom());
                            }
                        })
                        .listen('.consultation.ended', () => {
                            this.status = 'completed';
                            this.stopTimer();
                        })
                        .listenForWhisper('typing', (e) => {
                            if (e.userId !== this.userId) {
                                this.isTyping = true;
                                clearTimeout(this.typingTimeout);
                                this.typingTimeout = setTimeout(() => this.isTyping = false, 3000);
                            }
                        });

                    window.Echo.private('user.' + this.userId)
                        .listen('.consultation.accepted', (e) => {
                            if (e.consultation_id === this.consultationId) {
                                this.status = 'active';
                                this.startedAt = new Date().toISOString();
                                this.startTimer();
                            }
                        })
                        .listen('.consultation.rejected', (e) => {
                            if (e.consultation_id === this.consultationId) { this.status = 'rejected'; }
                        })
                        .listen('.wallet.updated', (e) => { this.walletBalance = e.balance; });
                }
            },

            async sendMessage() {
                if (!this.newMessage.trim() || this.sending) return;
                this.sending = true;
                const text = this.newMessage;
                this.msgCounter++;
                const tempId = 'tmp' + this.msgCounter;

                this.messages = [...this.messages, {
                    id: tempId, message: text, sender_id: this.userId,
                    sender_name: this.userName, created_at: new Date().toISOString(),
                }];
                this.pendingIds.add(tempId);
                this.newMessage = '';
                this.$nextTick(() => this.scrollToBottom());

                try {
                    const resp = await fetch('/consultation/' + this.consultationId + '/send', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' },
                        body: JSON.stringify({ message: text }),
                    });
                    if (!resp.ok) console.error('Send error:', resp.status);
                } catch (err) { console.error('Send failed:', err); }
                finally { this.sending = false; }
            },

            async requestChart() {
                try {
                    await fetch('/consultation/' + this.consultationId + '/request-chart', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' },
                    });
                    this.chartRequested = true;
                } catch (err) { console.error('Chart request failed:', err); }
            },

            async shareChart() {
                try {
                    await fetch('/consultation/' + this.consultationId + '/share-chart', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' },
                    });
                    this.chartShared = true;
                } catch (err) { console.error('Chart share failed:', err); }
            },

            onTyping() {
                if (window.Echo && this.newMessage.trim()) {
                    window.Echo.join('consultation.' + this.consultationId).whisper('typing', { userId: this.userId });
                }
            },

            startTimer() {
                this.timerInterval = setInterval(() => {
                    if (!this.startedAt) return;
                    const elapsed = Math.floor((Date.now() - new Date(this.startedAt).getTime()) / 1000);
                    this.timerDisplay = String(Math.floor(elapsed / 60)).padStart(2, '0') + ':' + String(elapsed % 60).padStart(2, '0');
                    this.currentCost = Math.ceil(elapsed / 60) * this.pricePerMinute;
                }, 1000);
            },

            stopTimer() { if (this.timerInterval) clearInterval(this.timerInterval); },

            scrollToBottom() {
                const el = this.$refs.messagesContainer;
                if (el) { el.scrollTop = el.scrollHeight; }
            },

            formatTime(iso) {
                return new Date(iso).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            },
         }">

        {{-- ===== CHAT HEADER ===== --}}
        <div class="flex items-center justify-between border-b border-gray-200 bg-white px-5 py-3.5 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="relative">
                    <div class="flex h-11 w-11 items-center justify-center rounded-full bg-gradient-to-br from-cosmic-400 to-cosmic-600 text-sm font-bold text-white">
                        <span x-text="otherName.charAt(0)"></span>
                    </div>
                    <template x-if="status === 'active'">
                        <span class="absolute -bottom-0.5 -right-0.5 h-3.5 w-3.5 rounded-full border-2 border-white bg-emerald-500"></span>
                    </template>
                </div>
                <div>
                    <h2 class="font-display text-base font-semibold text-gray-900" x-text="otherName"></h2>
                    <div class="flex items-center gap-2 text-xs">
                        <template x-if="status === 'active'">
                            <span class="text-emerald-600">Active session</span>
                        </template>
                        <template x-if="status === 'pending'">
                            <span class="text-amber-600">Waiting for response...</span>
                        </template>
                        <template x-if="status === 'completed' || status === 'rejected' || status === 'cancelled'">
                            <span class="text-gray-400">Session ended</span>
                        </template>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <template x-if="status === 'active'">
                    <div class="flex items-center gap-3">
                        {{-- Timer --}}
                        <div class="rounded-lg bg-gray-50 px-3 py-1.5 text-center">
                            <p class="font-mono text-sm font-bold text-gray-900" x-text="timerDisplay"></p>
                            <p class="text-[10px] text-gray-400">₹<span x-text="(currentCost / 100).toFixed(2)"></span></p>
                        </div>

                        {{-- Wallet (user only) --}}
                        <template x-if="isUser">
                            <div class="rounded-lg bg-cosmic-50 px-3 py-1.5 text-center">
                                <p class="text-[10px] text-cosmic-400">Balance</p>
                                <p class="text-xs font-bold text-cosmic-700">₹<span x-text="(walletBalance / 100).toFixed(0)"></span></p>
                            </div>
                        </template>

                        {{-- Request chart (astrologer only) --}}
                        <template x-if="!isUser && !chartRequested && !chartShared">
                            <button @click="requestChart()" class="rounded-lg bg-gold-500 px-3 py-2 text-xs font-semibold text-night-950 shadow-sm transition hover:bg-gold-600">
                                Request Chart
                            </button>
                        </template>
                        <template x-if="!isUser && chartRequested && !chartShared">
                            <span class="rounded-lg bg-amber-50 px-3 py-2 text-xs font-medium text-amber-600">Chart Requested</span>
                        </template>
                        <template x-if="chartShared">
                            <span class="rounded-lg bg-emerald-50 px-3 py-2 text-xs font-medium text-emerald-600">Chart Shared</span>
                        </template>

                        {{-- End chat --}}
                        <form method="POST" action="{{ route('consultation.end', $consultation) }}">
                            @csrf
                            <button type="submit" class="rounded-lg bg-red-500 px-3.5 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-red-600">
                                End Chat
                            </button>
                        </form>
                    </div>
                </template>
            </div>
        </div>

        {{-- ===== MESSAGES AREA ===== --}}
        <div class="flex flex-1 flex-col overflow-y-auto bg-gradient-to-b from-surface-alt to-surface" x-ref="messagesContainer">

            {{-- Top padding + system message --}}
            <div class="flex-shrink-0 px-5 pt-6 pb-2 text-center">
                <span class="inline-block rounded-full bg-gray-100 px-4 py-1.5 text-xs text-gray-500">
                    Consultation started &middot; {{ $consultation->started_at?->format('M d, h:i A') ?? 'Pending' }}
                </span>
            </div>

            {{-- Messages list --}}
            <div class="flex flex-1 flex-col justify-end px-5 pb-4">
                <template x-for="msg in messages" :key="msg.id">
                    <div class="mb-3">

                        {{-- SYSTEM MESSAGE: Chart request --}}
                        <template x-if="msg.type === 'chart_request'">
                            <div class="text-center">
                                <div class="mx-auto inline-block max-w-sm rounded-2xl border border-cosmic-200 bg-cosmic-50 px-5 py-4">
                                    <div class="flex items-center justify-center gap-2 text-cosmic-600">
                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.455 2.456L21.75 6l-1.036.259a3.375 3.375 0 0 0-2.455 2.456Z"/></svg>
                                        <span class="text-sm font-semibold">Birth Chart Request</span>
                                    </div>
                                    <p class="mt-1.5 text-xs text-cosmic-700" x-text="msg.message"></p>

                                    {{-- Show approve button to user only, if chart not already shared --}}
                                    <template x-if="isUser && hasChart && !chartShared">
                                        <button @click="shareChart()" class="mt-3 rounded-xl bg-cosmic-600 px-5 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-cosmic-700">
                                            Share My Birth Chart
                                        </button>
                                    </template>
                                    <template x-if="isUser && !hasChart">
                                        <p class="mt-2 text-[10px] text-gray-400">No birth chart available. Complete your profile first.</p>
                                    </template>
                                    <template x-if="isUser && chartShared">
                                        <p class="mt-2 text-[10px] text-emerald-600 font-medium">Birth chart shared</p>
                                    </template>
                                    <template x-if="!isUser">
                                        <p class="mt-2 text-[10px] text-gray-400">Waiting for user to approve...</p>
                                    </template>

                                    <p class="mt-2 text-[10px] text-gray-400" x-text="formatTime(msg.created_at)"></p>
                                </div>
                            </div>
                        </template>

                                        {{-- SYSTEM MESSAGE: Chart shared with interactive grid --}}
                        <template x-if="msg.type === 'chart_shared'">
                            <div class="text-center" x-data="{
                                chartStyle: 'south',
                                chartLang: 'en',
                                langs: { en: 'EN', hi: 'हि', ta: 'த', te: 'తె', ml: 'മ', mr: 'म' },
                                rashis: {
                                    en: ['Aries','Taurus','Gemini','Cancer','Leo','Virgo','Libra','Scorpio','Sagittarius','Capricorn','Aquarius','Pisces'],
                                    hi: ['मेष','वृषभ','मिथुन','कर्क','सिंह','कन्या','तुला','वृश्चिक','धनु','मकर','कुम्भ','मीन'],
                                    ta: ['மேஷம்','ரிஷபம்','மிதுனம்','கடகம்','சிம்மம்','கன்னி','துலாம்','விருச்சிகம்','தனுசு','மகரம்','கும்பம்','மீனம்'],
                                    te: ['మేషం','వృషభం','మిథునం','కర్కాటకం','సింహం','కన్య','తుల','వృశ్చికం','ధనుస్సు','మకరం','కుంభం','మీనం'],
                                    ml: ['മേടം','ഇടവം','മിഥുനം','കർക്കടകം','ചിങ്ങം','കന്നി','തുലാം','വൃശ്ചികം','ധനു','മകരം','കുംഭം','മീനം'],
                                    mr: ['मेष','वृषभ','मिथुन','कर्क','सिंह','कन्या','तूळ','वृश्चिक','धनू','मकर','कुंभ','मीन'],
                                },
                                grahaNames: {
                                    en: {Sun:'Sun',Moon:'Moon',Mars:'Mars',Mercury:'Mercury',Jupiter:'Jupiter',Venus:'Venus',Saturn:'Saturn',Rahu:'Rahu',Ketu:'Ketu'},
                                    hi: {Sun:'सूर्य',Moon:'चन्द्र',Mars:'मंगल',Mercury:'बुध',Jupiter:'गुरु',Venus:'शुक्र',Saturn:'शनि',Rahu:'राहु',Ketu:'केतु'},
                                    ta: {Sun:'சூரியன்',Moon:'சந்திரன்',Mars:'செவ்வாய்',Mercury:'புதன்',Jupiter:'குரு',Venus:'சுக்கிரன்',Saturn:'சனி',Rahu:'ராகு',Ketu:'கேது'},
                                    te: {Sun:'సూర్యుడు',Moon:'చంద్రుడు',Mars:'కుజుడు',Mercury:'బుధుడు',Jupiter:'గురుడు',Venus:'శుక్రుడు',Saturn:'శని',Rahu:'రాహువు',Ketu:'కేతువు'},
                                    ml: {Sun:'സൂര്യൻ',Moon:'ചന്ദ്രൻ',Mars:'ചൊവ്വ',Mercury:'ബുധൻ',Jupiter:'വ്യാഴം',Venus:'ശുക്രൻ',Saturn:'ശനി',Rahu:'രാഹു',Ketu:'കേതു'},
                                    mr: {Sun:'सूर्य',Moon:'चंद्र',Mars:'मंगळ',Mercury:'बुध',Jupiter:'गुरू',Venus:'शुक्र',Saturn:'शनी',Rahu:'राहू',Ketu:'केतू'},
                                },
                                nakshatras: {{ Js::from(collect(['en','hi','ta','te','ml','mr'])->mapWithKeys(fn($l) => [$l => __('horoscope.nakshatras', [], $l)])->map(fn($v) => is_array($v) ? $v : __('horoscope.nakshatras', [], 'en'))) }},
                                tithis: {{ Js::from(collect(['en','hi','ta','te','ml','mr'])->mapWithKeys(fn($l) => [$l => __('transit.forecasts', [], $l)])->map(fn($v) => [])) }},
                                pLabels: {
                                    en: {nakshatra:'Nakshatra',tithi:'Tithi',yoga:'Yoga'},
                                    hi: {nakshatra:'नक्षत्र',tithi:'तिथि',yoga:'योग'},
                                    ta: {nakshatra:'நட்சத்திரம்',tithi:'திதி',yoga:'யோகம்'},
                                    te: {nakshatra:'నక్షత్రం',tithi:'తిథి',yoga:'యోగం'},
                                    ml: {nakshatra:'നക്ഷത്രം',tithi:'തിഥി',yoga:'യോഗം'},
                                    mr: {nakshatra:'नक्षत्र',tithi:'तिथी',yoga:'योग'},
                                },
                                panchangaVals: {{ Js::from(collect(['en','hi','ta','te','ml','mr'])->mapWithKeys(function($l) {
                                    $t = __('horoscope.panchanga_labels', [], $l);
                                    $tithis = __('transit.ui', [], $l);
                                    return [$l => [
                                        'nakshatras' => is_array(__('horoscope.nakshatras', [], $l)) ? __('horoscope.nakshatras', [], $l) : [],
                                        'tithis' => is_array(__('transit.forecasts', [], $l)) ? [] : [],
                                        'yogas' => is_array(__('transit.ui', [], $l)) ? [] : [],
                                    ]];
                                })) }},
                                nak(name) {
                                    const naks = this.nakshatras[this.chartLang] || this.nakshatras.en;
                                    const enNaks = this.nakshatras.en;
                                    const idx = enNaks.indexOf(name);
                                    return idx >= 0 && naks[idx] ? naks[idx] : name;
                                },
                                tithi(name) {
                                    const map = {{ Js::from(collect(['en','hi','ta','te','ml','mr'])->mapWithKeys(function($l) {
                                        $t = __('horoscope.tithis', [], $l);
                                        return [$l => is_array($t) ? $t : []];
                                    })) }};
                                    return (map[this.chartLang] || {})[name] || name;
                                },
                                yoga(name) {
                                    const map = {{ Js::from(collect(['en','hi','ta','te','ml','mr'])->mapWithKeys(function($l) {
                                        $y = __('horoscope.yogas', [], $l);
                                        return [$l => is_array($y) ? $y : []];
                                    })) }};
                                    return (map[this.chartLang] || {})[name] || name;
                                },
                                pl(key) { return (this.pLabels[this.chartLang] || this.pLabels.en)[key] || key; },
                                r(i) { return (this.rashis[this.chartLang] || this.rashis.en)[i] || ''; },
                                g(n) { return (this.grahaNames[this.chartLang] || this.grahaNames.en)[n] || n; },
                                // South Indian fixed positions: signIndex => [col, row]
                                southCells() {
                                    return {11:[0,0],0:[1,0],1:[2,0],2:[3,0],3:[3,1],4:[3,2],5:[3,3],6:[2,3],7:[1,3],8:[0,3],9:[0,2],10:[0,1]};
                                },
                                // Build house planets from chart data
                                housePlanets() {
                                    if (!msg.metadata?.grahas) return {};
                                    const hp = {};
                                    msg.metadata.grahas.forEach(g => {
                                        if (!hp[g.house]) hp[g.house] = [];
                                        let label = this.g(g.name);
                                        if (g.is_retrograde) label += ' (R)';
                                        hp[g.house].push(label);
                                    });
                                    return hp;
                                },
                                signToHouse() {
                                    if (!msg.metadata?.lagna) return {};
                                    const lagnaIdx = msg.metadata.lagna.rashi.index;
                                    const m = {};
                                    for (let i = 0; i < 12; i++) m[(lagnaIdx + i) % 12] = i + 1;
                                    return m;
                                },
                                lagnaIndex() { return msg.metadata?.lagna?.rashi?.index ?? 0; },
                            }">
                                <div class="mx-auto inline-block max-w-lg w-full rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-left">
                                    {{-- Header --}}
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2 text-emerald-700">
                                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                                            <span class="text-sm font-semibold">Birth Chart Shared</span>
                                        </div>
                                        {{-- Language + Style switcher --}}
                                        <div class="flex items-center gap-1">
                                            <template x-for="(label, code) in langs" :key="code">
                                                <button @click="chartLang = code" class="rounded px-1.5 py-0.5 text-[9px] font-bold transition"
                                                        :class="chartLang === code ? 'bg-emerald-600 text-white' : 'text-emerald-600 hover:bg-emerald-100'" x-text="label"></button>
                                            </template>
                                            <span class="mx-1 text-emerald-300">|</span>
                                            <button @click="chartStyle = 'south'" class="rounded px-1.5 py-0.5 text-[9px] font-bold transition"
                                                    :class="chartStyle === 'south' ? 'bg-emerald-600 text-white' : 'text-emerald-600 hover:bg-emerald-100'">S</button>
                                            <button @click="chartStyle = 'north'" class="rounded px-1.5 py-0.5 text-[9px] font-bold transition"
                                                    :class="chartStyle === 'north' ? 'bg-emerald-600 text-white' : 'text-emerald-600 hover:bg-emerald-100'">N</button>
                                        </div>
                                    </div>

                                    {{-- User info --}}
                                    <template x-if="msg.metadata">
                                        <div class="mt-2 flex flex-wrap items-center gap-x-3 gap-y-0.5 text-xs text-gray-600">
                                            <span class="font-medium text-gray-800" x-text="msg.metadata.user_name"></span>
                                            <span x-text="msg.metadata.date_of_birth"></span>
                                            <span x-text="msg.metadata.time_of_birth"></span>
                                            <span x-text="msg.metadata.place_of_birth"></span>
                                        </div>
                                    </template>

                                    {{-- Lagna --}}
                                    <template x-if="msg.metadata?.lagna">
                                        <div class="mt-2 flex items-center gap-2 rounded-lg bg-white/60 px-3 py-1.5">
                                            <span class="text-[10px] font-medium text-gray-400 uppercase" x-text="chartLang === 'ta' ? 'லக்னம்' : chartLang === 'hi' ? 'लग्न' : 'Lagna'"></span>
                                            <span class="text-sm font-semibold text-gray-900" x-text="r(msg.metadata.lagna.rashi.index)"></span>
                                            <span class="text-xs text-gray-400" x-text="'(' + nak(msg.metadata.lagna.nakshatra?.name || '') + ')'"></span>
                                        </div>
                                    </template>

                                    {{-- SOUTH INDIAN CHART (SVG square) --}}
                                    <template x-if="msg.metadata?.grahas && chartStyle === 'south'">
                                        <div class="mt-3 mx-auto" style="max-width: 320px" x-html="(() => {
                                            const cs = 100, sz = 400;
                                            const positions = [[11,0,0],[0,1,0],[1,2,0],[2,3,0],[3,3,1],[4,3,2],[5,3,3],[6,2,3],[7,1,3],[8,0,3],[9,0,2],[10,0,1]];
                                            const li = lagnaIndex();
                                            const sth = signToHouse();
                                            const hp = housePlanets();

                                            let svg = `<svg viewBox='0 0 ${sz} ${sz}' class='w-full aspect-square' xmlns='http://www.w3.org/2000/svg'>`;
                                            svg += `<rect x='0' y='0' width='${sz}' height='${sz}' fill='white' stroke='#d1d5db' stroke-width='1.5' rx='3'/>`;
                                            for (let i=1;i<=3;i++) {
                                                svg += `<line x1='${i*cs}' y1='0' x2='${i*cs}' y2='${sz}' stroke='#e5e7eb' stroke-width='1'/>`;
                                                svg += `<line x1='0' y1='${i*cs}' x2='${sz}' y2='${i*cs}' stroke='#e5e7eb' stroke-width='1'/>`;
                                            }
                                            svg += `<rect x='${cs}' y='${cs}' width='${cs*2}' height='${cs*2}' fill='#faf9fc' stroke='#d1d5db' stroke-width='1'/>`;

                                            positions.forEach(([si, col, row]) => {
                                                const x = col*cs, y = row*cs;
                                                const isL = si === li;
                                                if (isL) svg += `<rect x='${x+1}' y='${y+1}' width='${cs-2}' height='${cs-2}' fill='#f5f0ff' rx='2'/>`;
                                                svg += `<text x='${x+5}' y='${y+13}' style='font-size:8px;font-weight:500;font-family:Plus Jakarta Sans,sans-serif' fill='${isL?'#6d28d9':'#9ca3af'}'>${r(si)}</text>`;
                                                if (isL) svg += `<text x='${x+cs-5}' y='${y+12}' text-anchor='end' style='font-size:7px;font-weight:700' fill='#ca8a04'>Asc</text>`;
                                                const planets = hp[sth[si]] || [];
                                                planets.forEach((p, pi) => {
                                                    svg += `<text x='${x+5}' y='${y+28+pi*13}' style='font-size:9px;font-weight:600;font-family:Plus Jakarta Sans,sans-serif' fill='#4c1d95'>${p}</text>`;
                                                });
                                            });
                                            svg += `</svg>`;
                                            return svg;
                                        })()">
                                        </div>
                                    </template>

                                    {{-- NORTH INDIAN CHART (SVG square) --}}
                                    <template x-if="msg.metadata?.grahas && chartStyle === 'north'">
                                        <div class="mt-3 mx-auto" style="max-width: 320px" x-html="(() => {
                                            const li = lagnaIndex(), hp = housePlanets();
                                            const hx = [0,150,75,38,75,38,75,150,225,263,225,263,225];
                                            const hy = [0,62,35,75,150,225,263,230,263,225,150,75,35];
                                            const py = [0,72,48,88,163,238,276,243,276,238,163,88,48];

                                            let svg = `<svg viewBox='0 0 300 300' class='w-full aspect-square' xmlns='http://www.w3.org/2000/svg'>`;
                                            svg += `<rect x='0' y='0' width='300' height='300' fill='white' stroke='#d1d5db' stroke-width='1'/>`;
                                            svg += `<line x1='0' y1='0' x2='300' y2='300' stroke='#d1d5db' stroke-width='0.8'/>`;
                                            svg += `<line x1='300' y1='0' x2='0' y2='300' stroke='#d1d5db' stroke-width='0.8'/>`;
                                            svg += `<polygon points='150,0 300,150 150,300 0,150' fill='none' stroke='#d1d5db' stroke-width='1'/>`;

                                            for (let h=1;h<=12;h++) {
                                                const si = (li + h - 1) % 12;
                                                svg += `<text x='${hx[h]}' y='${hy[h]}' text-anchor='middle' style='font-size:7px;font-family:Plus Jakarta Sans' fill='#9ca3af'>${r(si)}</text>`;
                                                (hp[h]||[]).forEach((p,pi) => {
                                                    svg += `<text x='${hx[h]}' y='${py[h]+pi*11}' text-anchor='middle' style='font-size:8px;font-weight:600;font-family:Plus Jakarta Sans' fill='#4c1d95'>${p}</text>`;
                                                });
                                            }
                                            svg += `</svg>`;
                                            return svg;
                                        })()">
                                        </div>
                                    </template>

                                    {{-- Panchanga (translated) --}}
                                    <template x-if="msg.metadata?.panchanga">
                                        <div class="mt-2 flex flex-wrap gap-x-3 gap-y-0.5 rounded-lg bg-white/60 px-3 py-1.5 text-[10px] text-gray-600">
                                            <span><span class="font-medium text-gray-500" x-text="pl('nakshatra') + ': '"></span><span x-text="nak(msg.metadata.panchanga.nakshatra)"></span></span>
                                            <span><span class="font-medium text-gray-500" x-text="pl('tithi') + ': '"></span><span x-text="tithi(msg.metadata.panchanga.tithi)"></span></span>
                                            <span><span class="font-medium text-gray-500" x-text="pl('yoga') + ': '"></span><span x-text="yoga(msg.metadata.panchanga.yoga)"></span></span>
                                        </div>
                                    </template>

                                    <div class="mt-3 flex items-center justify-between">
                                        <p class="text-[10px] text-gray-400" x-text="formatTime(msg.created_at)"></p>
                                        <a :href="'/consultation/' + consultationId + '/chart-view/' + msg.id" target="_blank" rel="noopener"
                                           class="flex items-center gap-1 text-[10px] font-medium text-emerald-600 hover:text-emerald-800 transition">
                                            <svg class="h-3 w-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                                            Open Full View
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </template>

                        {{-- REGULAR TEXT MESSAGE --}}
                        <template x-if="!msg.type || msg.type === 'text'">
                            <div>
                                <div class="mb-0.5 px-1" :class="isMine(msg) ? 'text-right' : 'text-left'">
                                    <span class="text-[10px] font-medium" :class="isMine(msg) ? 'text-cosmic-400' : 'text-gray-400'" x-text="isMine(msg) ? 'You' : msg.sender_name"></span>
                                </div>
                                <div class="flex" :class="isMine(msg) ? 'justify-end' : 'justify-start'">
                                    <div class="max-w-[70%] rounded-2xl px-4 py-2.5 shadow-sm"
                                         :class="isMine(msg)
                                            ? 'bg-cosmic-600 text-white rounded-tr-md'
                                            : 'bg-white text-gray-900 border border-gray-100 rounded-tl-md'">
                                        <p class="text-sm leading-relaxed" x-text="msg.message"></p>
                                        <p class="mt-1 text-[10px]" :class="isMine(msg) ? 'text-cosmic-200' : 'text-gray-400'" x-text="formatTime(msg.created_at)"></p>
                                    </div>
                                </div>
                            </div>
                        </template>

                    </div>
                </template>

                {{-- Typing indicator --}}
                <div x-show="isTyping" x-transition class="mb-3">
                    <div class="mb-0.5 px-1 text-left">
                        <span class="text-[10px] font-medium text-gray-400" x-text="otherName"></span>
                    </div>
                    <div class="flex justify-start">
                        <div class="rounded-2xl rounded-tl-md border border-gray-100 bg-white px-4 py-3 shadow-sm">
                            <div class="flex gap-1">
                                <span class="h-2 w-2 animate-bounce rounded-full bg-gray-300" style="animation-delay: 0ms"></span>
                                <span class="h-2 w-2 animate-bounce rounded-full bg-gray-300" style="animation-delay: 150ms"></span>
                                <span class="h-2 w-2 animate-bounce rounded-full bg-gray-300" style="animation-delay: 300ms"></span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Session ended --}}
                <template x-if="status === 'completed' || status === 'rejected'">
                    <div class="mt-2 text-center">
                        <span class="inline-block rounded-full bg-gray-100 px-4 py-1.5 text-xs text-gray-500">
                            Session ended
                        </span>
                    </div>
                </template>
            </div>
        </div>

        {{-- ===== INPUT BAR ===== --}}
        <div class="flex-shrink-0 border-t border-gray-200 bg-white px-5 py-4">
            <template x-if="status === 'active'">
                <form @submit.prevent="sendMessage" class="flex items-center gap-3">
                    <input
                        type="text"
                        x-model="newMessage"
                        @input="onTyping"
                        @keydown.enter.prevent="sendMessage"
                        placeholder="Type your message..."
                        class="flex-1 rounded-2xl border border-gray-200 bg-surface px-5 py-3 text-sm transition focus:border-cosmic-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-cosmic-200"
                        :disabled="sending"
                        autofocus
                    >
                    <button
                        type="submit"
                        :disabled="!newMessage.trim() || sending"
                        class="flex h-11 w-11 items-center justify-center rounded-2xl bg-cosmic-600 text-white shadow-md transition hover:bg-cosmic-700 disabled:opacity-40 disabled:shadow-none">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5"/></svg>
                    </button>
                </form>
            </template>

            <template x-if="status === 'pending'">
                <div class="flex items-center justify-center gap-2 py-2 text-sm text-amber-600">
                    <svg class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    Waiting for astrologer to accept...
                </div>
            </template>

            <template x-if="status === 'completed' || status === 'rejected' || status === 'cancelled'">
                <div class="flex items-center justify-center gap-4 py-2">
                    <a href="{{ route('consultations.history') }}" class="text-sm font-medium text-cosmic-600 hover:text-cosmic-800">View History</a>
                    <a href="{{ route('astrologers.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700">Browse Astrologers</a>
                </div>
            </template>
        </div>
    </div>
</x-layouts.customer>
