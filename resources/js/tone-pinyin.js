/**
 * Tone-colored Pinyin Helper
 * Learn Chinese Design System
 */

export function detectPinyinTone(syllable) {
    if (!syllable) return 0;
    if (/[āēīōūǖĀĒĪŌŪǕ]/.test(syllable)) return 1;
    if (/[áéíóúǘÁÉÍÓÚǗ]/.test(syllable)) return 2;
    if (/[ǎěǐǒǔǚǍĚǏǑǓǙ]/.test(syllable)) return 3;
    if (/[àèìòùǜÀÈÌÒÙǛ]/.test(syllable)) return 4;

    const match = syllable.match(/([1-5])$/);
    if (match) {
        const num = parseInt(match[1], 10);
        return (num >= 1 && num <= 4) ? num : 0;
    }

    return 0;
}

export function formatTonePinyin(text) {
    if (!text || typeof text !== 'string') return '';

    // Split preserving spaces and punctuation
    const tokens = text.split(/(\s+|[.,!?:;，。！？；、])/u);

    return tokens.map(token => {
        if (!token.trim() || /^[.,!?:;，。！？；、]+$/u.test(token)) {
            return token;
        }
        const tone = detectPinyinTone(token);
        return `<span class="tone-${tone}">${token}</span>`;
    }).join('');
}

// Expose globally for Alpine and inline scripts
if (typeof window !== 'undefined') {
    window.detectPinyinTone = detectPinyinTone;
    window.formatTonePinyin = formatTonePinyin;
}
