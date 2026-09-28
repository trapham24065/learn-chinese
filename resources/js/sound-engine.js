/**
 * Contextual SFX Engine using Web Audio API
 * Lightweight waveform synthesis (zero MP3 assets, zero load latency)
 * Fully compliant with browser autoplay policies & independent SFX/TTS settings
 */

class SoundEngine {
    constructor() {
        this.ctx = null;
        this.sfxEnabled = this.loadPreference('learn_chinese_sfx_enabled', true);
        this.ttsEnabled = this.loadPreference('learn_chinese_tts_enabled', true);
        this.isUnlocked = false;

        this.initAutoUnlock();
    }

    loadPreference(key, defaultValue) {
        if (typeof window === 'undefined' || !window.localStorage) return defaultValue;
        const val = localStorage.getItem(key);
        return val !== null ? val === 'true' : defaultValue;
    }

    savePreference(key, value) {
        if (typeof window !== 'undefined' && window.localStorage) {
            localStorage.setItem(key, String(value));
        }
    }

    toggleSfx() {
        this.sfxEnabled = !this.sfxEnabled;
        this.savePreference('learn_chinese_sfx_enabled', this.sfxEnabled);
        if (this.sfxEnabled) {
            this.play('tap');
        }
        return this.sfxEnabled;
    }

    isSfxEnabled() {
        return this.sfxEnabled;
    }

    toggleTts() {
        this.ttsEnabled = !this.ttsEnabled;
        this.savePreference('learn_chinese_tts_enabled', this.ttsEnabled);
        return this.ttsEnabled;
    }

    isTtsEnabled() {
        return this.ttsEnabled;
    }

    getContext() {
        if (!this.ctx && typeof window !== 'undefined') {
            const AudioContextClass = window.AudioContext || window.webkitAudioContext;
            if (AudioContextClass) {
                this.ctx = new AudioContextClass();
            }
        }
        if (this.ctx && this.ctx.state === 'suspended') {
            this.ctx.resume();
        }
        return this.ctx;
    }

    initAutoUnlock() {
        if (typeof window === 'undefined') return;

        const unlock = () => {
            const ctx = this.getContext();
            if (ctx && ctx.state === 'suspended') {
                ctx.resume();
            }
            this.isUnlocked = true;
            ['click', 'touchstart', 'keydown'].forEach(evt => {
                document.removeEventListener(evt, unlock, true);
            });
        };

        ['click', 'touchstart', 'keydown'].forEach(evt => {
            document.addEventListener(evt, unlock, { once: true, capture: true, passive: true });
        });
    }

    /**
     * Play synthesized sound effect by name
     * @param {'ding'|'whoosh'|'tap'|'softError'|'milestone'|'fanfare'} soundName
     */
    play(soundName) {
        if (!this.sfxEnabled) return;
        const ctx = this.getContext();
        if (!ctx) return;

        try {
            switch (soundName) {
                case 'ding':
                    this.synthDing(ctx);
                    break;
                case 'whoosh':
                    this.synthWhoosh(ctx);
                    break;
                case 'tap':
                    this.synthTap(ctx);
                    break;
                case 'softError':
                    this.synthSoftError(ctx);
                    break;
                case 'milestone':
                    this.synthMilestone(ctx);
                    break;
                case 'fanfare':
                    this.synthFanfare(ctx);
                    break;
                default:
                    this.synthTap(ctx);
            }
        } catch (e) {
            console.warn('SFX playback error:', e);
        }
    }

    /**
     * Micro: Gentle, pleasant high ding bell (880Hz + harmonic)
     */
    synthDing(ctx) {
        const now = ctx.currentTime;
        const osc1 = ctx.createOscillator();
        const osc2 = ctx.createOscillator();
        const gain = ctx.createGain();

        osc1.type = 'sine';
        osc1.frequency.setValueAtTime(880, now); // A5

        osc2.type = 'sine';
        osc2.frequency.setValueAtTime(1760, now); // A6 overtone

        gain.gain.setValueAtTime(0.2, now);
        gain.gain.exponentialRampToValueAtTime(0.0001, now + 0.35);

        osc1.connect(gain);
        osc2.connect(gain);
        gain.connect(ctx.destination);

        osc1.start(now);
        osc2.start(now);
        osc1.stop(now + 0.35);
        osc2.stop(now + 0.35);
    }

    /**
     * Micro: Quick smooth frequency sweep whoosh for card flip
     */
    synthWhoosh(ctx) {
        const now = ctx.currentTime;
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();

        osc.type = 'sine';
        osc.frequency.setValueAtTime(450, now);
        osc.frequency.exponentialRampToValueAtTime(140, now + 0.16);

        gain.gain.setValueAtTime(0.12, now);
        gain.gain.exponentialRampToValueAtTime(0.001, now + 0.16);

        osc.connect(gain);
        gain.connect(ctx.destination);

        osc.start(now);
        osc.stop(now + 0.16);
    }

    /**
     * Micro: Crisp light tap for controls
     */
    synthTap(ctx) {
        const now = ctx.currentTime;
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();

        osc.type = 'triangle';
        osc.frequency.setValueAtTime(320, now);
        osc.frequency.exponentialRampToValueAtTime(120, now + 0.06);

        gain.gain.setValueAtTime(0.15, now);
        gain.gain.exponentialRampToValueAtTime(0.001, now + 0.06);

        osc.connect(gain);
        gain.connect(ctx.destination);

        osc.start(now);
        osc.stop(now + 0.06);
    }

    /**
     * Micro: Soft double tone for wrong answer (not harsh)
     */
    synthSoftError(ctx) {
        const now = ctx.currentTime;
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();

        osc.type = 'sine';
        osc.frequency.setValueAtTime(330, now);
        osc.frequency.setValueAtTime(260, now + 0.12);

        gain.gain.setValueAtTime(0.15, now);
        gain.gain.exponentialRampToValueAtTime(0.001, now + 0.32);

        osc.connect(gain);
        gain.connect(ctx.destination);

        osc.start(now);
        osc.stop(now + 0.32);
    }

    /**
     * Milestone: 5-streak combo arpeggio (C5 - E5 - G5 - C6)
     */
    synthMilestone(ctx) {
        const notes = [523.25, 659.25, 783.99, 1046.50];
        const now = ctx.currentTime;

        notes.forEach((freq, i) => {
            const startTime = now + i * 0.09;
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();

            osc.type = 'sine';
            osc.frequency.setValueAtTime(freq, startTime);

            gain.gain.setValueAtTime(0.18, startTime);
            gain.gain.exponentialRampToValueAtTime(0.001, startTime + 0.22);

            osc.connect(gain);
            gain.connect(ctx.destination);

            osc.start(startTime);
            osc.stop(startTime + 0.22);
        });
    }

    /**
     * Completion: Triumphant fanfare chord progression (1.2s)
     */
    synthFanfare(ctx) {
        const chords = [
            { time: 0.0, freqs: [523.25, 659.25, 783.99], dur: 0.2 },       // C major
            { time: 0.22, freqs: [587.33, 739.99, 880.00], dur: 0.2 },      // D major
            { time: 0.44, freqs: [659.25, 783.99, 1046.50, 1318.51], dur: 0.7 } // Final high chord
        ];

        const now = ctx.currentTime;

        chords.forEach(chord => {
            const chordStart = now + chord.time;
            chord.freqs.forEach(freq => {
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();

                osc.type = 'triangle';
                osc.frequency.setValueAtTime(freq, chordStart);

                gain.gain.setValueAtTime(0.12, chordStart);
                gain.gain.exponentialRampToValueAtTime(0.001, chordStart + chord.dur);

                osc.connect(gain);
                gain.connect(ctx.destination);

                osc.start(chordStart);
                osc.stop(chordStart + chord.dur);
            });
        });
    }
}

export const soundEngine = new SoundEngine();

// Expose globally
if (typeof window !== 'undefined') {
    window.soundEngine = soundEngine;
}
