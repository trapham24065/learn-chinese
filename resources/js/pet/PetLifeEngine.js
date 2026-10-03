/**
 * PetLifeEngine.js - Contextual Finite State Machine & Tactile Perception Engine
 * 
 * Drives organic, living behavior without server overhead or random interval spam.
 * Decides Pet behavior based on 4 vectors:
 *   1. Time of Day (Biological clock: 22h-6h sleeping, 6h-9h fresh morning, etc.)
 *   2. Page Context (dashboard, flashcard, quiz, lesson, pet_room, other)
 *   3. User Inactivity & Engagement (idle seconds, active learning)
 *   4. Tactile Perception (cursor tracking, poking, stroking/petting)
 * 
 * Hardware-accelerated and ultra-lightweight for low-spec devices and TV Box.
 */

export class PetLifeEngine {
    constructor(options = {}) {
        this.pageContext = options.pageContext || 'other';
        this.personality = options.personality || 'playful';
        this.hunger = options.hunger ?? 100;
        this.callbacks = {
            onStateChange: options.onStateChange || (() => {}),
            onLookChange: options.onLookChange || (() => {}),
            onPetting: options.onPetting || (() => {}),
            onPoked: options.onPoked || (() => {}),
            onWakeUp: options.onWakeUp || (() => {}),
            onWave: options.onWave || (() => {}),
        };

        // State Machine
        // States: 'idle' | 'observing' | 'reading' | 'curious' | 'poked' | 'petting' | 'dozing' | 'sleeping' | 'waking_up' | 'eating' | 'happy' | 'waving'
        this.currentState = 'idle';
        this.previousState = 'idle';
        this.stateLockUntil = 0; // Timestamp to prevent interrupting brief reaction states

        // Time & Inactivity Trackers
        this.lastUserActionTime = Date.now();
        this.idleSeconds = 0;
        this.evalTimer = null;
        this.rafId = null;

        // Tactile / Mouse Perception
        this.petElement = null;
        this.mousePos = { x: -1, y: -1 };
        this.lookVector = { x: 0, y: 0, distance: 0 };
        this.strokeHistory = [];
        this.lastStrokeDirection = 0;

        // Biological Schedule
        this.isNightTime = this.checkIsNightTime();

        this.initEventListeners();
        this.startEvaluationLoop();
    }

    setPetElement(el) {
        this.petElement = el;
    }

    setPersonality(personality) {
        this.personality = personality || 'playful';
    }

    setHunger(hunger) {
        this.hunger = typeof hunger === 'number' ? hunger : 100;
    }

    setPageContext(ctx) {
        this.pageContext = ctx || 'other';
        this.evaluateNextState(true);
    }

    checkIsNightTime() {
        const hour = new Date().getHours();
        return hour >= 22 || hour < 6;
    }

    initEventListeners() {
        if (typeof window === 'undefined') return;

        // Track user activity (resets idle timer)
        const onActivity = () => {
            const wasDeepSleeping = this.currentState === 'sleeping';
            const wasDozing = this.currentState === 'dozing';
            this.lastUserActionTime = Date.now();
            this.idleSeconds = 0;

            if (wasDeepSleeping) {
                this.wakeUp();
            } else if (wasDozing && !this.isStateLocked()) {
                this.transitionTo('curious', 1500);
            }
        };

        ['mousemove', 'keydown', 'touchstart', 'scroll'].forEach(evt => {
            window.addEventListener(evt, onActivity, { passive: true });
        });

        // Mousemove for Eye/Head Tracking and Stroke Detection
        window.addEventListener('mousemove', (e) => {
            this.mousePos.x = e.clientX;
            this.mousePos.y = e.clientY;
            this.updateLookVector();
        }, { passive: true });

        // Tab Visibility / Page Leave detection
        document.addEventListener('visibilitychange', () => {
            if (document.visibilityState === 'hidden') {
                this.callbacks.onWave();
            } else {
                onActivity();
            }
        });
    }

    startEvaluationLoop() {
        // Run behavioral evaluation every 3.2 seconds (extremely low CPU usage)
        this.evalTimer = setInterval(() => {
            this.idleSeconds = Math.round((Date.now() - this.lastUserActionTime) / 1000);
            this.isNightTime = this.checkIsNightTime();
            this.evaluateNextState();
        }, 3200);

        // Initial evaluation
        this.evaluateNextState(true);
    }

    isStateLocked() {
        return Date.now() < this.stateLockUntil;
    }

    lockState(durationMs) {
        this.stateLockUntil = Date.now() + durationMs;
    }

    transitionTo(newState, lockDuration = 0) {
        if (this.currentState === newState) return;
        this.previousState = this.currentState;
        this.currentState = newState;
        if (lockDuration > 0) {
            this.lockState(lockDuration);
        }
        this.callbacks.onStateChange(this.currentState, this.previousState);
    }

    /**
     * Behavioral Score Algorithm:
     * Calculates weighted desires based on Time + Context + User Idle + Personality.
     */
    evaluateNextState(force = false) {
        if (this.isStateLocked() && !force) return;

        // Starving Pet check
        if (this.hunger <= 15) {
            this.transitionTo('hungry');
            return;
        }

        const hour = new Date().getHours();
        const idle = this.idleSeconds;

        // 1. Sleep Scoring
        let sleepScore = 0;
        if (this.isNightTime) sleepScore += 70;
        if (idle >= 180) sleepScore += 80;
        else if (idle >= 90) sleepScore += 45;
        else if (idle >= 45 && (hour >= 21 || hour === 13)) sleepScore += 35; // Night or siesta

        if (sleepScore >= 100) {
            this.transitionTo('sleeping');
            return;
        } else if (sleepScore >= 50 && this.currentState !== 'sleeping') {
            this.transitionTo('dozing');
            return;
        }

        // If woke up from sleep or not sleeping:
        if (this.currentState === 'sleeping' || this.currentState === 'dozing') {
            // Need user input to wake up
            return;
        }

        // 2. Context-Driven Desires
        switch (this.pageContext) {
            case 'flashcard':
                // In Flashcards: Pet is a quiet study partner.
                // It observes user studying, glances at cards, never makes noise.
                if (idle < 15) {
                    this.transitionTo('observing'); // Watching user click/flip cards
                } else if (idle < 45) {
                    this.transitionTo('reading');   // Quietly reading along
                } else {
                    this.transitionTo('dozing');   // Drowsy if user paused too long
                }
                break;

            case 'quiz':
                // In Quiz: Pet holds its breath, attentive and supportive.
                if (idle < 20) {
                    this.transitionTo('observing');
                } else {
                    this.transitionTo('curious');
                }
                break;

            case 'lesson':
                // In Lesson: Attentive listener taking notes quietly
                if (idle < 30) {
                    this.transitionTo('reading');
                } else {
                    this.transitionTo('observing');
                }
                break;

            case 'dashboard':
            case 'pet_room':
            default:
                // Relaxed home environment: relaxed, playing, curious
                if (this.lookVector.distance > 0 && this.lookVector.distance < 250 && idle < 10) {
                    this.transitionTo('curious');
                } else if (idle > 40) {
                    this.transitionTo(this.personality === 'calm' ? 'dozing' : 'idle');
                } else {
                    this.transitionTo('idle');
                }
                break;
        }
    }

    /**
     * Tactile Interaction: Cursor Tracking
     * Computes normalized look vector [-1, 1] relative to Pet's bounding box.
     */
    updateLookVector() {
        if (!this.petElement || typeof window === 'undefined') return;

        // Skip calculations if sleeping
        if (this.currentState === 'sleeping') {
            this.applyLookStyles(0, 0);
            return;
        }

        const rect = this.petElement.getBoundingClientRect();
        const petCenterX = rect.left + rect.width / 2;
        const petCenterY = rect.top + rect.height / 2;

        const deltaX = this.mousePos.x - petCenterX;
        const deltaY = this.mousePos.y - petCenterY;
        const distance = Math.hypot(deltaX, deltaY);

        // Normalize within max radius of 350px
        const maxDist = 350;
        const normX = Math.max(-1, Math.min(1, deltaX / maxDist));
        const normY = Math.max(-1, Math.min(1, deltaY / maxDist));

        this.lookVector = { x: normX, y: normY, distance };
        this.applyLookStyles(normX, normY);
        this.callbacks.onLookChange(this.lookVector);

        // Detect Petting (Stroking cursor over pet)
        this.checkPettingMotion(rect);
    }

    applyLookStyles(normX, normY) {
        if (!this.petElement) return;
        // Inject hardware-accelerated CSS custom properties
        this.petElement.style.setProperty('--pet-look-x', normX.toFixed(2));
        this.petElement.style.setProperty('--pet-look-y', normY.toFixed(2));
    }

    /**
     * Tactile Interaction: Petting / Stroking Detection
     * Moving cursor back and forth across Pet bounding box triggers purring affection.
     */
    checkPettingMotion(rect) {
        if (this.currentState === 'sleeping' || this.isStateLocked()) return;

        const isInside = (
            this.mousePos.x >= rect.left &&
            this.mousePos.x <= rect.right &&
            this.mousePos.y >= rect.top &&
            this.mousePos.y <= rect.bottom
        );

        if (!isInside) return;

        const now = Date.now();
        const currentX = this.mousePos.x;

        // Clean older stroke points (> 1.2s ago)
        this.strokeHistory = this.strokeHistory.filter(pt => now - pt.time < 1200);

        if (this.strokeHistory.length > 0) {
            const lastPt = this.strokeHistory[this.strokeHistory.length - 1];
            const diffX = currentX - lastPt.x;
            if (Math.abs(diffX) > 12) {
                const direction = Math.sign(diffX);
                if (this.lastStrokeDirection !== 0 && direction !== this.lastStrokeDirection) {
                    // Reversed direction within Pet's body!
                    this.strokeHistory.push({ x: currentX, time: now, reversal: true });
                }
                this.lastStrokeDirection = direction;
            }
        } else {
            this.strokeHistory.push({ x: currentX, time: now, reversal: false });
        }

        const reversalCount = this.strokeHistory.filter(pt => pt.reversal).length;

        // If user stroked back and forth 3+ times:
        if (reversalCount >= 3) {
            this.strokeHistory = [];
            this.onPettingDetected();
        }
    }

    onPettingDetected() {
        this.transitionTo('petting', 2200);
        this.callbacks.onPetting();
        setTimeout(() => {
            if (this.currentState === 'petting') {
                this.evaluateNextState(true);
            }
        }, 2300);
    }

    /**
     * Tactile Interaction: User Pokes Pet (Click)
     */
    handlePoke() {
        if (this.currentState === 'sleeping') {
            this.wakeUp();
            return;
        }

        // Startle / Blushing recoil
        this.transitionTo('poked', 1200);
        this.callbacks.onPoked();

        setTimeout(() => {
            if (this.currentState === 'poked') {
                // Return to curious or context state
                this.evaluateNextState(true);
            }
        }, 1300);
    }

    /**
     * Wake up transition with organic sleepy blink
     */
    wakeUp() {
        this.transitionTo('waking_up', 2000);
        this.callbacks.onWakeUp();
        setTimeout(() => {
            if (this.currentState === 'waking_up') {
                this.evaluateNextState(true);
            }
        }, 2100);
    }

    /**
     * Cleanup timers on destroy
     */
    destroy() {
        clearInterval(this.evalTimer);
    }
}
