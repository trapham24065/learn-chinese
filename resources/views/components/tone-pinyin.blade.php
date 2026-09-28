@props(['text' => '', 'as' => 'span'])

@php
    $text = (string) $text;

    // Helper closure to detect tone number from syllable
    $detectTone = function ($syllable) {
        // Check for tone marks
        if (preg_match('/[āēīōūǖĀĒĪŌŪǕ]/u', $syllable)) return 1;
        if (preg_match('/[áéíóúǘÁÉÍÓÚǗ]/u', $syllable)) return 2;
        if (preg_match('/[ǎěǐǒǔǚǍĚǏǑǓǙ]/u', $syllable)) return 3;
        if (preg_match('/[àèìòùǜÀÈÌÒÙǛ]/u', $syllable)) return 4;

        // Check for trailing number
        if (preg_match('/([1-5])$/', $syllable, $matches)) {
            $num = (int)$matches[1];
            return ($num >= 1 && $num <= 4) ? $num : 0;
        }

        return 0;
    };

    // Split text into tokens (words and non-word separators like punctuation and spaces)
    $tokens = preg_split('/(\s+|[.,!?:;，。！？；、])/u', $text, -1, PREG_SPLIT_DELIM_CAPTURE);
@endphp

<{{ $as }} {{ $attributes->merge(['class' => 'inline-flex flex-wrap items-baseline gap-x-1']) }}>
@foreach($tokens as $token)
    @if(trim($token) === '' || preg_match('/^[.,!?:;，。！？；、]+$/u', $token))
        <span>{{ $token }}</span>
    @else
        @php
            $tone = $detectTone($token);
        @endphp
        <span class="tone-{{ $tone }}">{{ $token }}</span>
    @endif
@endforeach
</{{ $as }}>
