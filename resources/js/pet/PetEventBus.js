/**
 * PetEventBus - Unified Pub/Sub Event Bus for Pet Learning Companion
 * Decouples learning triggers (Flashcard, Quiz, Daily Goal) from Pet reaction/audio.
 */
class PetEventBusClass {
    constructor() {
        this.listeners = new Map();
    }

    /**
     * Subscribe to an event
     * @param {string} event e.g. 'pet:poked', 'pet:fed', 'pet:word-mastered', 'pet:daily-goal-completed', 'pet:evolved'
     * @param {Function} callback
     */
    on(event, callback) {
        if (!this.listeners.has(event)) {
            this.listeners.set(event, new Set());
        }
        this.listeners.get(event).add(callback);
        return () => this.off(event, callback);
    }

    /**
     * Unsubscribe from an event
     * @param {string} event
     * @param {Function} callback
     */
    off(event, callback) {
        if (this.listeners.has(event)) {
            this.listeners.get(event).delete(callback);
        }
    }

    /**
     * Emit an event with payload to all subscribers
     * @param {string} event
     * @param {Object} [payload={}]
     */
    emit(event, payload = {}) {
        if (this.listeners.has(event)) {
            this.listeners.get(event).forEach(callback => {
                try {
                    callback(payload);
                } catch (err) {
                    console.error(`[PetEventBus] Error in listener for "${event}":`, err);
                }
            });
        }

        // Also dispatch browser CustomEvent for Alpine or legacy listeners
        if (typeof window !== 'undefined') {
            try {
                window.dispatchEvent(new CustomEvent(event, { detail: payload }));
                window.dispatchEvent(new CustomEvent('pet:event', { detail: { type: event, ...payload } }));
            } catch (e) {}
        }
    }
}

export const PetEventBus = new PetEventBusClass();

if (typeof window !== 'undefined') {
    window.PetEventBus = PetEventBus;
}
