/**
 * PetVoiceManager - Chinese Voice Adapter for Pet Learning Companion
 * Adapts to existing backend POST /tts (Azure Neural Voice 'zh-CN-XiaoxiaoNeural')
 * with automatic fallback to Web Speech API (window.speechSynthesis).
 * 
 * Strict cancellation & dual-pipeline management:
 * - HTMLAudio & network fetch cancellation (AbortController)
 * - Web Speech API cancellation (window.speechSynthesis.cancel)
 * 
 * Independent toggle: 'pet_voice_enabled' in localStorage.
 */

import { PetAudioEngine } from './PetAudioEngine.js';

class PetVoiceManagerClass {
    constructor() {
        this.currentAudio = null;
        this.abortController = null;
        this.isPlaying = false;
        this.voiceEnabled = this.loadPreference('pet_voice_enabled', true);
        this.serverTtsDisabled = false;
    }

    loadPreference(key, defaultValue) {
        if (typeof window === 'undefined' || !window.localStorage) return defaultValue;
        const val = localStorage.getItem(key);
        return val !== null ? (val === '1' || val === 'true') : defaultValue;
    }

    savePreference(key, value) {
        if (typeof window !== 'undefined' && window.localStorage) {
            localStorage.setItem(key, value ? '1' : '0');
        }
    }

    isVoiceEnabled() {
        return this.voiceEnabled === true;
    }

    setVoiceEnabled(enabled) {
        this.voiceEnabled = Boolean(enabled);
        this.savePreference('pet_voice_enabled', this.voiceEnabled);
        if (!this.voiceEnabled) {
            this.stop();
        }
        return this.voiceEnabled;
    }

    toggleVoice() {
        return this.setVoiceEnabled(!this.isVoiceEnabled());
    }

    getCsrfToken() {
        if (typeof document === 'undefined') return '';
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    /**
     * Stop all ongoing voice playbacks and network requests immediately
     */
    stop() {
        // Pipeline 1: Abort pending HTTP request
        if (this.abortController) {
            try {
                this.abortController.abort();
            } catch (e) {}
            this.abortController = null;
        }

        // Pipeline 1: Pause and unload HTML Audio element
        if (this.currentAudio) {
            try {
                this.currentAudio.pause();
                this.currentAudio.currentTime = 0;
                this.currentAudio.removeAttribute('src');
                this.currentAudio.load();
            } catch (e) {}
            this.currentAudio = null;
        }

        // Pipeline 2: Cancel Web Speech API
        if (typeof window !== 'undefined' && 'speechSynthesis' in window) {
            try {
                window.speechSynthesis.cancel();
            } catch (e) {}
        }

        this.isPlaying = false;
    }

    /**
     * Speak Chinese text (word, sentence, or dialogue)
     * @param {string} text Chinese text to speak
     * @param {Object} [options]
     * @param {Function} [options.onStart] Triggered when playback actually begins
     * @param {Function} [options.onEnd] Triggered when playback finishes or fails
     * @param {Function} [options.onError] Triggered on error
     * @returns {Promise<boolean>} Resolves to true if speech initiated
     */
    async speak(text, { onStart, onEnd, onError } = {}) {
        const cleanText = (text || '').trim();
        if (!cleanText) {
            if (typeof onEnd === 'function') onEnd();
            return false;
        }

        if (!this.isVoiceEnabled()) {
            if (typeof onEnd === 'function') onEnd();
            return false;
        }

        // Stop any current voice playback before starting new one
        this.stop();

        this.isPlaying = true;
        this.abortController = new AbortController();
        const signal = this.abortController.signal;

        let started = false;
        const notifyStart = () => {
            if (!started) {
                started = true;
                if (typeof onStart === 'function') {
                    try { onStart(); } catch (e) {}
                }
            }
        };

        const notifyEnd = () => {
            this.isPlaying = false;
            this.currentAudio = null;
            if (typeof onEnd === 'function') {
                try { onEnd(); } catch (e) {}
            }
        };

        // If server-side TTS was previously determined unavailable, go directly to Web Speech API
        if (this.serverTtsDisabled) {
            return this.speakFallback(cleanText, notifyStart, notifyEnd, onError);
        }

        // Attempt Tier 1: Azure Neural TTS via existing /tts endpoint
        try {
            const csrfToken = this.getCsrfToken();
            const response = await fetch('/tts', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ text: cleanText }),
                signal: signal,
            });

            if (signal.aborted) return false;

            if (response.ok) {
                const data = await response.json();
                if (data && data.audio) {
                    const audio = new Audio(data.audio);
                    this.currentAudio = audio;

                    // Bind PetAudioEngine volume
                    const vol = (typeof PetAudioEngine !== 'undefined' && PetAudioEngine.getVolume) 
                        ? PetAudioEngine.getVolume() 
                        : 0.8;
                    audio.volume = vol;

                    return new Promise((resolve) => {
                        audio.onplay = () => notifyStart();
                        audio.onended = () => {
                            notifyEnd();
                            resolve(true);
                        };
                        audio.onerror = (err) => {
                            console.warn('[PetVoiceManager] Audio element playback failed, falling back:', err);
                            this.speakFallback(cleanText, notifyStart, notifyEnd, onError)
                                .then(resolve);
                        };

                        audio.play().then(() => {
                            notifyStart();
                        }).catch(playErr => {
                            if (signal.aborted) {
                                resolve(false);
                                return;
                            }
                            console.warn('[PetVoiceManager] Autoplay or playback rejected, falling back to Web Speech API:', playErr);
                            this.speakFallback(cleanText, notifyStart, notifyEnd, onError)
                                .then(resolve);
                        });
                    });
                } else {
                    // Server returned fallback or no audio (e.g. Azure key not configured)
                    this.serverTtsDisabled = true;
                    return this.speakFallback(cleanText, notifyStart, notifyEnd, onError);
                }
            } else {
                this.serverTtsDisabled = true;
                return this.speakFallback(cleanText, notifyStart, notifyEnd, onError);
            }
        } catch (err) {
            if (signal.aborted) {
                return false;
            }
            this.serverTtsDisabled = true;
            // Fallback Tier 2: Web Speech API (window.speechSynthesis)
            return this.speakFallback(cleanText, notifyStart, notifyEnd, onError);
        }
    }

    /**
     * Fallback speech playback using window.speechSynthesis
     */
    speakFallback(text, notifyStart, notifyEnd, onError) {
        return new Promise((resolve) => {
            if (typeof window === 'undefined' || !('speechSynthesis' in window)) {
                console.warn('[PetVoiceManager] Web Speech API not supported on this browser');
                if (typeof onError === 'function') onError(new Error('Speech synthesis not supported'));
                notifyEnd();
                resolve(false);
                return;
            }

            try {
                window.speechSynthesis.cancel();
                const utterance = new SpeechSynthesisUtterance(text);
                utterance.lang = 'zh-CN';
                utterance.rate = 0.85;

                const vol = (typeof PetAudioEngine !== 'undefined' && PetAudioEngine.getVolume) 
                    ? PetAudioEngine.getVolume() 
                    : 0.8;
                utterance.volume = vol;

                utterance.onstart = () => {
                    notifyStart();
                };

                utterance.onend = () => {
                    notifyEnd();
                    resolve(true);
                };

                utterance.onerror = (e) => {
                    console.warn('[PetVoiceManager] SpeechSynthesis error:', e);
                    if (typeof onError === 'function') onError(e);
                    notifyEnd();
                    resolve(false);
                };

                window.speechSynthesis.speak(utterance);
            } catch (err) {
                console.warn('[PetVoiceManager] SpeechSynthesis exception:', err);
                if (typeof onError === 'function') onError(err);
                notifyEnd();
                resolve(false);
            }
        });
    }
}

export const PetVoiceManager = new PetVoiceManagerClass();

if (typeof window !== 'undefined') {
    window.PetVoiceManager = PetVoiceManager;
}
