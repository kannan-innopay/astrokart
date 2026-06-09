@props([
    'length' => 6,
    'name' => 'otp',
])

<div x-data="{
    digits: Array({{ $length }}).fill(''),
    get otp() { return this.digits.join(''); },
    focusNext(index) {
        if (index < {{ $length - 1 }}) this.$refs['digit' + (index + 1)].focus();
    },
    handleInput(index, event) {
        const val = event.target.value.replace(/\D/g, '');
        this.digits[index] = val.slice(-1);
        event.target.value = this.digits[index];
        if (val && index < {{ $length - 1 }}) this.focusNext(index);
    },
    handleKeydown(index, event) {
        if (event.key === 'Backspace' && !this.digits[index] && index > 0) {
            this.$refs['digit' + (index - 1)].focus();
        }
    },
    handlePaste(event) {
        const text = event.clipboardData.getData('text').replace(/\D/g, '').slice(0, {{ $length }});
        text.split('').forEach((char, i) => {
            this.digits[i] = char;
            if (this.$refs['digit' + i]) this.$refs['digit' + i].value = char;
        });
        const lastIndex = Math.min(text.length, {{ $length }}) - 1;
        if (this.$refs['digit' + lastIndex]) this.$refs['digit' + lastIndex].focus();
        event.preventDefault();
    }
}" class="flex justify-center gap-2.5">
    <input type="hidden" name="{{ $name }}" :value="otp">

    @for($i = 0; $i < $length; $i++)
        <input
            type="text"
            inputmode="numeric"
            maxlength="1"
            x-ref="digit{{ $i }}"
            @input="handleInput({{ $i }}, $event)"
            @keydown="handleKeydown({{ $i }}, $event)"
            @paste="handlePaste($event)"
            class="h-13 w-11 rounded-xl border border-gray-200 bg-white text-center text-xl font-semibold text-gray-900 shadow-sm transition-all focus:border-cosmic-400 focus:outline-none focus:ring-2 focus:ring-cosmic-200"
            {{ $i === 0 ? 'autofocus' : '' }}
        >
    @endfor
</div>
