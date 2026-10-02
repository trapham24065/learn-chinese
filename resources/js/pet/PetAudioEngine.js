/**
 * PetAudioEngine - Procedural Web Audio API Synthesizer for Pet Companion
 * Uses a single shared AudioContext with lazy unlock on first user gesture.
 * Zero external MP3 files, zero network bandwidth, independent SFX toggle.
 */

class PetAudioEngineClass {
    constructor() {
        this.ctx = null;
        this.masterGain = null;
        this.isUnlocked = false;

        this.sfxEnabled = this.loadPreference('pet_sfx_enabled', true);
        this.volume = parseFloat(this.loadPreference('pet_audio_volume', '0.8'));

        this.lastPlayTime = {
            chirp: 0,
            pop: 0,
            munch: 0,
            purr: 0,
            hungry: 0,
            celebration: 0,
            levelUp: 0,
        };

        this.cooldowns = {
            chirp: 300,
            pop: 500,
            munch: 200,
            purr: 400,
            hungry: 400,
            celebration: 800,
            levelUp: 0, // Rare evolution fanfare: no cooldown
        };

        this.initAutoUnlock();
    }

    loadPreference(key, defaultValue) {
        if (typeof window === 'undefined' || !window.localStorage) return defaultValue;
        const val = localStorage.getItem(key);
        return val !== null ? val : defaultValue;
    }

    savePreference(key, value) {
        if (typeof window !== 'undefined' && window.localStorage) {
            localStorage.setItem(key, String(value));
        }
    }

    isSfxEnabled() {
        return this.sfxEnabled === true || this.sfxEnabled === 'true' || this.sfxEnabled === '1';
    }

    setSfxEnabled(enabled) {
        this.sfxEnabled = Boolean(enabled);
        this.savePreference('pet_sfx_enabled', this.sfxEnabled ? '1' : '0');
        if (this.sfxEnabled) {
            this.chirp();
        }
        return this.isSfxEnabled();
    }

    toggleSfx() {
        return this.setSfxEnabled(!this.isSfxEnabled());
    }

    getVolume() {
        return isNaN(this.volume) ? 0.8 : Math.max(0, Math.min(1, this.volume));
    }

    setVolume(vol) {
        this.volume = Math.max(0, Math.min(1, parseFloat(vol) || 0.8));
        this.savePreference('pet_audio_volume', this.volume.toFixed(2));
        if (this.masterGain && this.ctx) {
            this.masterGain.gain.setValueAtTime(this.volume, this.ctx.currentTime);
        }
    }

    /**
     * Get or initialize the shared AudioContext singleton
     */
    getContext() {
        if (typeof window === 'undefined') return null;

        if (!this.ctx) {
            const AudioContextClass = window.AudioContext || window.webkitAudioContext;
            if (!AudioContextClass) return null;
            try {
                this.ctx = new AudioContextClass();
                this.masterGain = this.ctx.createGain();
                this.masterGain.gain.setValueAtTime(this.getVolume(), this.ctx.currentTime);
                this.masterGain.connect(this.ctx.destination);
            } catch (e) {
                console.warn('[PetAudioEngine] Failed to create AudioContext:', e);
                return null;
            }
        }

        if (this.ctx && this.ctx.state === 'suspended') {
            this.ctx.resume().catch(() => {});
        }

        return this.ctx;
    }

    initAutoUnlock() {
        if (typeof window === 'undefined') return;

        const unlock = () => {
            const ctx = this.getContext();
            if (ctx && ctx.state === 'suspended') {
                ctx.resume().catch(() => {});
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

    canPlay(soundType) {
        if (!this.isSfxEnabled()) return false;
        const now = Date.now();
        const cd = this.cooldowns[soundType] || 0;
        if (now - (this.lastPlayTime[soundType] || 0) < cd) {
            return false;
        }
        this.lastPlayTime[soundType] = now;
        return true;
    }

    /**
     * Cute high chirp / squeak when poked or happy
     */
    chirp(pitchFactor = 1.0) {
        if (!this.canPlay('chirp')) return;
        const ctx = this.getContext();
        if (!ctx) return;

        try {
            const factor = Math.max(0.6, Math.min(1.8, Number(pitchFactor) || 1.0));
            const now = ctx.currentTime;
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();

            osc.type = 'sine';
            osc.frequency.setValueAtTime(650 * factor, now);
            osc.frequency.exponentialRampToValueAtTime(1250 * factor, now + 0.12);

            gain.gain.setValueAtTime(0.22, now);
            gain.gain.exponentialRampToValueAtTime(0.001, now + 0.13);

            osc.connect(gain);
            gain.connect(this.masterGain);

            osc.start(now);
            osc.stop(now + 0.14);
        } catch (e) {}
    }

    /**
     * Comic speech bubble pop sound
     */
    pop() {
        if (!this.canPlay('pop')) return;
        const ctx = this.getContext();
        if (!ctx) return;

        try {
            const now = ctx.currentTime;
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();

            osc.type = 'triangle';
            osc.frequency.setValueAtTime(320, now);
            osc.frequency.exponentialRampToValueAtTime(680, now + 0.07);

            gain.gain.setValueAtTime(0.2, now);
            gain.gain.exponentialRampToValueAtTime(0.001, now + 0.08);

            osc.connect(gain);
            gain.connect(this.masterGain);

            osc.start(now);
            osc.stop(now + 0.09);
        } catch (e) {}
    }

    /**
     * Cute chewing / munching SFX when fed
     */
    munch() {
        if (!this.canPlay('munch')) return;
        const ctx = this.getContext();
        if (!ctx) return;

        try {
            const now = ctx.currentTime;
            // 2 quick crunchy nibbles
            [0, 0.09].forEach(offset => {
                const t = now + offset;
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();

                osc.type = 'triangle';
                osc.frequency.setValueAtTime(420, t);
                osc.frequency.exponentialRampToValueAtTime(240, t + 0.06);

                gain.gain.setValueAtTime(0.25, t);
                gain.gain.exponentialRampToValueAtTime(0.001, t + 0.065);

                osc.connect(gain);
                gain.connect(this.masterGain);

                osc.start(t);
                osc.stop(t + 0.07);
            });
        } catch (e) {}
    }

    /**
     * Soft dragon purr when petted or satisfied
     */
    purr() {
        if (!this.canPlay('purr')) return;
        const ctx = this.getContext();
        if (!ctx) return;

        try {
            const now = ctx.currentTime;
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();

            osc.type = 'sine';
            osc.frequency.setValueAtTime(130, now);
            osc.frequency.linearRampToValueAtTime(150, now + 0.15);
            osc.frequency.linearRampToValueAtTime(120, now + 0.3);

            gain.gain.setValueAtTime(0.12, now);
            gain.gain.exponentialRampToValueAtTime(0.001, now + 0.32);

            osc.connect(gain);
            gain.connect(this.masterGain);

            osc.start(now);
            osc.stop(now + 0.33);
        } catch (e) {}
    }

    /**
     * Soft whimper / hungry sigh when starving
     */
    hungry() {
        if (!this.canPlay('hungry')) return;
        const ctx = this.getContext();
        if (!ctx) return;

        try {
            const now = ctx.currentTime;
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();

            osc.type = 'sine';
            osc.frequency.setValueAtTime(420, now);
            osc.frequency.exponentialRampToValueAtTime(260, now + 0.28);

            gain.gain.setValueAtTime(0.15, now);
            gain.gain.exponentialRampToValueAtTime(0.001, now + 0.3);

            osc.connect(gain);
            gain.connect(this.masterGain);

            osc.start(now);
            osc.stop(now + 0.31);
        } catch (e) {}
    }

    /**
     * Celebration chime for Daily Goal completion
     * Pleasant, sweet harmonic triad (C5 - E5 - G5)
     * Distinct from the epic evolution fanfare
     */
    celebration() {
        if (!this.canPlay('celebration')) return;
        const ctx = this.getContext();
        if (!ctx) return;

        try {
            const now = ctx.currentTime;
            const notes = [
                { freq: 523.25, time: 0, dur: 0.25 }, // C5
                { freq: 659.25, time: 0.1, dur: 0.25 }, // E5
                { freq: 783.99, time: 0.2, dur: 0.45 }, // G5
            ];

            notes.forEach(n => {
                const start = now + n.time;
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();

                osc.type = 'triangle';
                osc.frequency.setValueAtTime(n.freq, start);

                gain.gain.setValueAtTime(0.18, start);
                gain.gain.exponentialRampToValueAtTime(0.001, start + n.dur);

                osc.connect(gain);
                gain.connect(this.masterGain);

                osc.start(start);
                osc.stop(start + n.dur);
            });
        } catch (e) {}
    }

    /**
     * Epic Evolution Fanfare (Stage Up)
     * Rare, magnificent arpeggio + shimmering chords
     */
    levelUp() {
        if (!this.canPlay('levelUp')) return;
        const ctx = this.getContext();
        if (!ctx) return;

        try {
            const now = ctx.currentTime;
            const arpeggio = [
                { freq: 523.25, time: 0, dur: 0.15 },    // C5
                { freq: 659.25, time: 0.12, dur: 0.15 }, // E5
                { freq: 783.99, time: 0.24, dur: 0.18 }, // G5
                { freq: 1046.50, time: 0.38, dur: 0.65 } // C6 (long ring)
            ];

            arpeggio.forEach(n => {
                const start = now + n.time;
                const osc = ctx.createOscillator();
                const osc2 = ctx.createOscillator();
                const gain = ctx.createGain();

                osc.type = 'triangle';
                osc.frequency.setValueAtTime(n.freq, start);

                osc2.type = 'sine';
                osc2.frequency.setValueAtTime(n.freq * 2, start); // Octave overtone

                gain.gain.setValueAtTime(0.2, start);
                gain.gain.exponentialRampToValueAtTime(0.001, start + n.dur);

                osc.connect(gain);
                osc2.connect(gain);
                gain.connect(this.masterGain);

                osc.start(start);
                osc2.start(start);
                osc.stop(start + n.dur);
                osc2.stop(start + n.dur);
            });
        } catch (e) {}
    }
}

export const PetAudioEngine = new PetAudioEngineClass();

if (typeof window !== 'undefined') {
    window.PetAudioEngine = PetAudioEngine;
}
