/**
 * Chinese Cultural Ambiance System (新国风氛围引擎)
 * - Falling Red Plum / Peach Blossom Petals (落梅花瓣飘落)
 * - Ink-wash Click Ripple (水墨轻泛涟漪)
 * - Persistent toggle switch saved in localStorage
 */

class ChineseAmbianceEngine {
    constructor() {
        this.enabled = localStorage.getItem('chinese_ambiance_enabled') !== 'false'; // Default to true
        this.canvas = null;
        this.ctx = null;
        this.petals = [];
        this.animationFrameId = null;
        this.maxPetals = window.innerWidth < 768 ? 10 : 20;
        this.init();
    }

    init() {
        if (typeof window === 'undefined') return;

        // Initialize Canvas
        this.createCanvas();
        this.createToggleButton();
        this.setupInkRipple();

        window.addEventListener('resize', () => this.handleResize());
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                this.stopAnimation();
            } else if (this.enabled) {
                this.startAnimation();
            }
        });

        if (this.enabled) {
            this.startAnimation();
        }
    }

    createCanvas() {
        this.canvas = document.createElement('canvas');
        this.canvas.id = 'chinese-ambiance-canvas';
        this.canvas.style.position = 'fixed';
        this.canvas.style.top = '0';
        this.canvas.style.left = '0';
        this.canvas.style.width = '100vw';
        this.canvas.style.height = '100vh';
        this.canvas.style.pointerEvents = 'none';
        this.canvas.style.zIndex = '35'; // Sits above background, below modals and navigation
        this.canvas.style.transition = 'opacity 0.6s ease';
        this.canvas.style.opacity = this.enabled ? '1' : '0';
        document.body.appendChild(this.canvas);

        this.ctx = this.canvas.getContext('2d');
        this.handleResize();
        this.initPetals();
    }

    handleResize() {
        if (!this.canvas) return;
        this.canvas.width = window.innerWidth;
        this.canvas.height = window.innerHeight;
        this.maxPetals = window.innerWidth < 768 ? 10 : 20;
    }

    initPetals() {
        this.petals = [];
        for (let i = 0; i < this.maxPetals; i++) {
            this.petals.push(this.createPetal(true));
        }
    }

    createPetal(randomY = false) {
        const width = this.canvas ? this.canvas.width : window.innerWidth;
        const height = this.canvas ? this.canvas.height : window.innerHeight;
        return {
            x: Math.random() * width,
            y: randomY ? Math.random() * height : -20,
            size: 6 + Math.random() * 8, // 6px to 14px
            speedY: 0.8 + Math.random() * 1.2,
            speedX: 0.3 + Math.random() * 0.8,
            rotation: Math.random() * Math.PI * 2,
            rotationSpeed: (Math.random() - 0.5) * 0.03,
            swingFactor: 1 + Math.random() * 2,
            swingOffset: Math.random() * Math.PI * 2,
            opacity: 0.45 + Math.random() * 0.45,
            colorHue: Math.random() > 0.6 ? 'crimson' : (Math.random() > 0.3 ? 'rose' : 'blush')
        };
    }

    startAnimation() {
        if (this.animationFrameId) return;
        let lastTime = performance.now();

        const animate = (time) => {
            if (!this.enabled) return;
            const delta = (time - lastTime) / 1000;
            lastTime = time;

            this.render(time);
            this.animationFrameId = requestAnimationFrame(animate);
        };

        this.animationFrameId = requestAnimationFrame(animate);
    }

    stopAnimation() {
        if (this.animationFrameId) {
            cancelAnimationFrame(this.animationFrameId);
            this.animationFrameId = null;
        }
        if (this.ctx && this.canvas) {
            this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
        }
    }

    render(time) {
        if (!this.ctx || !this.canvas) return;
        this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);

        const width = this.canvas.width;
        const height = this.canvas.height;

        for (let i = 0; i < this.petals.length; i++) {
            const p = this.petals[i];

            // Horizontal drift + sinusoidal breeze
            p.x += p.speedX + Math.sin((time * 0.001) * p.swingFactor + p.swingOffset) * 0.6;
            p.y += p.speedY;
            p.rotation += p.rotationSpeed;

            // Recycle petal if offscreen
            if (p.y > height + 20 || p.x > width + 20) {
                this.petals[i] = this.createPetal(false);
                continue;
            }

            // Draw petal
            this.ctx.save();
            this.ctx.translate(p.x, p.y);
            this.ctx.rotate(p.rotation);
            this.ctx.scale(Math.cos(p.rotation * 0.5), 1); // 3D tumbling effect

            // Colors based on traditional Chinese hues (Chu Sa, Hải Đường, Đào Hồng)
            let grad = this.ctx.createRadialGradient(0, 0, 1, 0, 0, p.size);
            if (p.colorHue === 'crimson') {
                grad.addColorStop(0, `rgba(185, 28, 28, ${p.opacity})`);
                grad.addColorStop(0.7, `rgba(220, 38, 38, ${p.opacity * 0.8})`);
                grad.addColorStop(1, `rgba(239, 68, 68, 0)`);
            } else if (p.colorHue === 'rose') {
                grad.addColorStop(0, `rgba(225, 29, 72, ${p.opacity})`);
                grad.addColorStop(0.7, `rgba(244, 63, 94, ${p.opacity * 0.75})`);
                grad.addColorStop(1, `rgba(251, 113, 133, 0)`);
            } else {
                grad.addColorStop(0, `rgba(244, 114, 182, ${p.opacity})`);
                grad.addColorStop(0.7, `rgba(251, 146, 60, ${p.opacity * 0.7})`);
                grad.addColorStop(1, `rgba(254, 205, 211, 0)`);
            }

            this.ctx.fillStyle = grad;

            // Draw realistic curved petal shape
            this.ctx.beginPath();
            this.ctx.moveTo(0, -p.size);
            this.ctx.bezierCurveTo(p.size * 0.7, -p.size * 0.5, p.size * 0.7, p.size * 0.5, 0, p.size);
            this.ctx.bezierCurveTo(-p.size * 0.7, p.size * 0.5, -p.size * 0.7, -p.size * 0.5, 0, -p.size);
            this.ctx.fill();

            this.ctx.restore();
        }
    }

    toggle() {
        this.enabled = !this.enabled;
        localStorage.setItem('chinese_ambiance_enabled', this.enabled ? 'true' : 'false');
        if (this.canvas) {
            this.canvas.style.opacity = this.enabled ? '1' : '0';
        }
        if (this.enabled) {
            this.initPetals();
            this.startAnimation();
        } else {
            this.stopAnimation();
        }
        this.updateButtonLabel();
    }

    createToggleButton() {
        const btn = document.createElement('button');
        btn.id = 'chinese-ambiance-toggle';
        btn.type = 'button';
        btn.setAttribute('aria-label', 'Bật tắt hiệu ứng hoa đào cổ phong');
        btn.title = 'Bật/Tắt hiệu ứng hoa đào cổ phong (新国风特效)';
        btn.className = 'fixed bottom-5 right-5 z-30 flex items-center gap-1.5 rounded-full border border-amber-900/20 bg-white/90 px-3 py-1.5 text-xs font-bold text-amber-950 shadow-lg shadow-amber-950/10 backdrop-blur transition-all hover:scale-105 active:scale-95 hover:bg-amber-50 cursor-pointer select-none';
        
        btn.addEventListener('click', () => this.toggle());
        document.body.appendChild(btn);
        this.updateButtonLabel();
    }

    updateButtonLabel() {
        const btn = document.getElementById('chinese-ambiance-toggle');
        if (!btn) return;
        if (this.enabled) {
            btn.innerHTML = `<span class="text-sm">🌸</span><span class="hidden sm:inline">Cổ phong:</span><span class="text-emerald-700 font-extrabold">Bật</span>`;
        } else {
            btn.innerHTML = `<span class="text-sm grayscale opacity-60">🌸</span><span class="hidden sm:inline">Cổ phong:</span><span class="text-slate-400 font-bold">Tắt</span>`;
        }
    }

    setupInkRipple() {
        // Soft ink wash ripple on interactive clicks
        document.addEventListener('click', (e) => {
            if (!this.enabled) return;
            const target = e.target.closest('button, a, .flip-card, .chinese-seal');
            if (!target) return;

            const ripple = document.createElement('span');
            ripple.className = 'ink-ripple-effect';
            const rect = target.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height) * 1.5;
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;

            ripple.style.width = `${size}px`;
            ripple.style.height = `${size}px`;
            ripple.style.left = `${x}px`;
            ripple.style.top = `${y}px`;

            if (window.getComputedStyle(target).position === 'static') {
                target.style.position = 'relative';
            }
            target.style.overflow = 'hidden';
            target.appendChild(ripple);

            setTimeout(() => {
                ripple.remove();
            }, 650);
        });
    }
}

// Auto-initialize when DOM is ready
if (typeof window !== 'undefined') {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            window.chineseAmbiance = new ChineseAmbianceEngine();
        });
    } else {
        window.chineseAmbiance = new ChineseAmbianceEngine();
    }
}

export { ChineseAmbianceEngine };
