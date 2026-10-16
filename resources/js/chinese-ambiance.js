/**
 * Chinese Cultural Four Seasons Ambiance Engine (四季时令·新国风氛围引擎)
 * 
 * - Four Seasons Particle System (四季粒子矩阵):
 *   🌸 Xuân (Spring): Peach & Red Plum petals (Đào hoa khinh vũ)
 *   🍃 Hạ (Summer): Jade Bamboo leaves & Bioluminescent Fireflies (Trúc lâm dạ huỳnh)
 *   🍁 Thu (Autumn): Golden Ginkgo fans & Crimson Maple leaves (Kim ngân phong diệp)
 *   ❄️ Đông (Winter): Hexagonal Crystal Snowflakes & Cold Plum petals (Thụy tuyết hàn mai)
 * - Auto Calendar Detection based on real-time month or manual selection
 * - Interactive Mouse Breeze Dynamics (repulsion physics)
 * - Learning Milestone Celebration Shower (Radial Blossom Burst)
 * - "Tứ Quý Trà Phổ" Jade-tablet Popover Menu (四季茶谱)
 * - Persistent configuration in localStorage
 * - Ink-wash click ripple (水墨涟漪)
 */

const SEASONS_DATA = {
    spring: {
        id: 'spring',
        name: 'Mùa Xuân',
        cnName: '春·桃李春风',
        icon: '🌸',
        badge: 'Đào Hoa',
        desc: 'Cánh đào hồng phớt & mai đỏ chu sa bồng bềnh',
        quoteCn: '等闲识得东风面，万紫千红总是春',
        quotePinyin: 'Děng xián shí dé dōng fēng miàn, wàn zǐ qiān hóng zǒng shì chūn',
        quoteVi: 'Nhận ra mặt gió đông quen thuộc, muôn tía nghìn hồng thảy đều xuân.',
        types: ['peach', 'plum']
    },
    summer: {
        id: 'summer',
        name: 'Mùa Hạ',
        cnName: '夏·竹林夜萤',
        icon: '🍃',
        badge: 'Trúc Huỳnh',
        desc: 'Lá trúc ngọc bích & đom đóm dạ quang lấp lánh',
        quoteCn: '竹深树密虫虫绿，蝉噪声中带小凉',
        quotePinyin: 'Zhú shēn shù mì chóng chóng lǜ, chán zào shēng zhōng dài xiǎo liáng',
        quoteVi: 'Trúc thẳm cây rậm muôn trùng biếc, tiếng ve râm ran lẫn gió lành.',
        types: ['bamboo', 'firefly']
    },
    autumn: {
        id: 'autumn',
        name: 'Mùa Thu',
        cnName: '秋·金叶知秋',
        icon: '🍁',
        badge: 'Kim Ngân',
        desc: 'Lá ngân hạnh vàng óng & lá phong đỏ thắm xoay tít',
        quoteCn: '停车坐爱枫林晚，霜叶红于二月花',
        quotePinyin: 'Tíng chē zuò ài fēng lín wǎn, shuāng yè hóng yú èr yuè huā',
        quoteVi: 'Dừng xe vì mến phong rừng muộn, sương điểm lá phong đỏ hơn hoa.',
        types: ['ginkgo', 'maple']
    },
    winter: {
        id: 'winter',
        name: 'Mùa Đông',
        cnName: '冬·瑞雪寒梅',
        icon: '❄️',
        badge: 'Hàn Mai',
        desc: 'Bông tuyết pha lê lục giác & hàn mai trong sương',
        quoteCn: '忽如一夜春风来，千树万树梨花开',
        quotePinyin: 'Hū rú yí yè chūn fēng lái, qiān shù wàn shù lí huā kāi',
        quoteVi: 'Chợt như gió xuân đêm qua tới, nghìn cành muôn nhánh nở hoa tuyết.',
        types: ['snowflake', 'winterPlum']
    }
};

class ChineseAmbianceEngine {
    constructor() {
        this.enabled = localStorage.getItem('chinese_ambiance_enabled') !== 'false';
        this.seasonSetting = localStorage.getItem('chinese_ambiance_season') || 'auto';
        this.mouseBreezeEnabled = localStorage.getItem('chinese_ambiance_breeze') !== 'false';

        this.canvas = null;
        this.ctx = null;
        this.particles = [];
        this.celebrationParticles = [];
        this.animationFrameId = null;
        this.maxParticles = window.innerWidth < 768 ? 10 : 20;

        // Mouse physics state
        this.mouseX = -9999;
        this.mouseY = -9999;
        this.mouseActive = false;
        this.mouseTimeout = null;

        // Popover state
        this.isMenuOpen = false;

        this.init();
    }

    getEffectiveSeason() {
        if (this.seasonSetting && this.seasonSetting !== 'auto' && SEASONS_DATA[this.seasonSetting]) {
            return this.seasonSetting;
        }
        // Auto: 2-4: Spring | 5-7: Summer | 8-10: Autumn | 11-1: Winter
        const month = new Date().getMonth() + 1;
        if (month >= 2 && month <= 4) return 'spring';
        if (month >= 5 && month <= 7) return 'summer';
        if (month >= 8 && month <= 10) return 'autumn';
        return 'winter';
    }

    init() {
        if (typeof window === 'undefined') return;

        this.createCanvas();
        this.createControlUI();
        this.setupInkRipple();
        this.setupMouseTracking();
        this.setupEventListeners();

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
        this.initParticles();
    }

    handleResize() {
        if (!this.canvas) return;
        this.canvas.width = window.innerWidth;
        this.canvas.height = window.innerHeight;
        this.maxParticles = window.innerWidth < 768 ? 10 : 20;
    }

    initParticles() {
        this.particles = [];
        for (let i = 0; i < this.maxParticles; i++) {
            this.particles.push(this.createParticle(true));
        }
    }

    createParticle(randomY = false) {
        const width = this.canvas ? this.canvas.width : window.innerWidth;
        const height = this.canvas ? this.canvas.height : window.innerHeight;
        const seasonKey = this.getEffectiveSeason();
        const season = SEASONS_DATA[seasonKey];
        const particleType = season.types[Math.floor(Math.random() * season.types.length)];

        // Base particle attributes
        let size = 8 + Math.random() * 8;
        let speedY = 0.8 + Math.random() * 1.2;
        let speedX = 0.3 + Math.random() * 0.8;
        let opacity = 0.45 + Math.random() * 0.45;

        // Specialized behavior per particle type
        if (particleType === 'firefly') {
            size = 3.5 + Math.random() * 3.5;
            speedY = (Math.random() - 0.5) * 0.6 - 0.2; // Hover/wander slightly upwards
            speedX = (Math.random() - 0.5) * 0.9;
            opacity = 0.6 + Math.random() * 0.4;
        } else if (particleType === 'ginkgo' || particleType === 'maple') {
            size = 11 + Math.random() * 11;
            speedY = 1.0 + Math.random() * 1.3;
            speedX = 0.4 + Math.random() * 0.9;
        } else if (particleType === 'snowflake') {
            size = 5 + Math.random() * 7;
            speedY = 0.6 + Math.random() * 1.0;
            speedX = (Math.random() - 0.5) * 0.5;
            opacity = 0.55 + Math.random() * 0.4;
        } else if (particleType === 'bamboo') {
            size = 10 + Math.random() * 10;
            speedY = 1.1 + Math.random() * 1.4;
        }

        return {
            x: Math.random() * width,
            y: randomY ? Math.random() * height : -25,
            type: particleType,
            size: size,
            speedY: speedY,
            speedX: speedX,
            vx: 0,
            vy: 0,
            rotation: Math.random() * Math.PI * 2,
            rotationSpeed: (Math.random() - 0.5) * 0.03,
            swingFactor: 1 + Math.random() * 2,
            swingOffset: Math.random() * Math.PI * 2,
            opacity: opacity,
            pulseOffset: Math.random() * Math.PI * 2,
            flipProgress: Math.random() * Math.PI
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

        // 1. Render normal ambient particles
        for (let i = 0; i < this.particles.length; i++) {
            const p = this.particles[i];

            // Mouse breeze repulsion
            if (this.mouseBreezeEnabled && this.mouseActive) {
                const dx = p.x - this.mouseX;
                const dy = p.y - this.mouseY;
                const dist = Math.hypot(dx, dy);
                if (dist < 100 && dist > 1) {
                    const force = ((100 - dist) / 100) * 2.2;
                    p.vx += (dx / dist) * force;
                    p.vy += (dy / dist) * force;
                }
            }

            // Dampen repulsion velocities smoothly
            p.vx *= 0.93;
            p.vy *= 0.93;

            // Apply movement
            if (p.type === 'firefly') {
                // Firefly wandering path
                p.x += p.speedX + Math.sin(time * 0.0015 + p.swingOffset) * 0.7 + p.vx;
                p.y += p.speedY + Math.cos(time * 0.0012 + p.swingOffset) * 0.5 + p.vy;
            } else {
                p.x += p.speedX + Math.sin((time * 0.001) * p.swingFactor + p.swingOffset) * 0.6 + p.vx;
                p.y += p.speedY + p.vy;
            }

            p.rotation += p.rotationSpeed;
            p.flipProgress += 0.02;

            // Recycle if offscreen
            if (p.y > height + 30 || p.x > width + 30 || p.x < -30 || (p.type === 'firefly' && p.y < -30)) {
                this.particles[i] = this.createParticle(false);
                continue;
            }

            // Draw based on type
            this.drawParticle(p, time);
        }

        // 2. Render celebration shower particles
        if (this.celebrationParticles.length > 0) {
            for (let i = this.celebrationParticles.length - 1; i >= 0; i--) {
                const cp = this.celebrationParticles[i];
                cp.x += cp.vx;
                cp.y += cp.vy;
                cp.vy += 0.09; // gravity
                cp.vx *= 0.98;
                cp.rotation += cp.rotationSpeed;
                cp.life -= 0.015;

                if (cp.life <= 0 || cp.y > height + 20) {
                    this.celebrationParticles.splice(i, 1);
                    continue;
                }

                const prevOpacity = cp.opacity;
                cp.opacity = Math.max(0, cp.life);
                this.drawParticle(cp, time);
                cp.opacity = prevOpacity;
            }
        }
    }

    drawParticle(p, time) {
        this.ctx.save();
        this.ctx.translate(p.x, p.y);
        this.ctx.rotate(p.rotation);

        switch (p.type) {
            case 'peach':
                this.drawPeachPetal(p);
                break;
            case 'plum':
            case 'winterPlum':
                this.drawPlumPetal(p);
                break;
            case 'bamboo':
                this.drawBambooLeaf(p);
                break;
            case 'firefly':
                this.drawFirefly(p, time);
                break;
            case 'ginkgo':
                this.drawGinkgoLeaf(p);
                break;
            case 'maple':
                this.drawMapleLeaf(p);
                break;
            case 'snowflake':
                this.drawSnowflake(p);
                break;
            default:
                this.drawPeachPetal(p);
        }

        this.ctx.restore();
    }

    /**
     * 🌸 Peach Blossom Petal (Hồng đào phơi phới)
     */
    drawPeachPetal(p) {
        this.ctx.scale(Math.cos(p.flipProgress), 1); // 3D tumbling
        const grad = this.ctx.createRadialGradient(0, 0, 1, 0, 0, p.size);
        grad.addColorStop(0, `rgba(254, 205, 211, ${p.opacity})`);
        grad.addColorStop(0.7, `rgba(251, 113, 133, ${p.opacity * 0.85})`);
        grad.addColorStop(1, `rgba(244, 63, 94, 0)`);
        this.ctx.fillStyle = grad;

        this.ctx.beginPath();
        this.ctx.moveTo(0, -p.size);
        this.ctx.bezierCurveTo(p.size * 0.7, -p.size * 0.5, p.size * 0.7, p.size * 0.5, 0, p.size);
        this.ctx.bezierCurveTo(-p.size * 0.7, p.size * 0.5, -p.size * 0.7, -p.size * 0.5, 0, -p.size);
        this.ctx.fill();
    }

    /**
     * 🌺 Red Cinnabar Plum Petal (Chu sa hàn mai / hồng mai)
     */
    drawPlumPetal(p) {
        this.ctx.scale(Math.cos(p.flipProgress), 1);
        const grad = this.ctx.createRadialGradient(0, 0, 1, 0, 0, p.size);
        grad.addColorStop(0, `rgba(220, 38, 38, ${p.opacity})`);
        grad.addColorStop(0.8, `rgba(185, 28, 28, ${p.opacity * 0.85})`);
        grad.addColorStop(1, `rgba(153, 27, 27, 0)`);
        this.ctx.fillStyle = grad;

        this.ctx.beginPath();
        this.ctx.arc(0, 0, p.size * 0.85, 0, Math.PI * 2);
        this.ctx.fill();

        // Tiny plum blossom stamen dot
        this.ctx.fillStyle = `rgba(254, 240, 138, ${p.opacity * 0.9})`;
        this.ctx.beginPath();
        this.ctx.arc(0, p.size * 0.35, p.size * 0.16, 0, Math.PI * 2);
        this.ctx.fill();
    }

    /**
     * 🍃 Jade Bamboo Leaf (Lá trúc xanh biếc)
     */
    drawBambooLeaf(p) {
        this.ctx.scale(Math.cos(p.flipProgress * 0.8), 1);
        const grad = this.ctx.createLinearGradient(0, -p.size * 1.5, 0, p.size * 1.5);
        grad.addColorStop(0, `rgba(52, 211, 153, ${p.opacity})`);
        grad.addColorStop(0.5, `rgba(5, 150, 105, ${p.opacity * 0.9})`);
        grad.addColorStop(1, `rgba(4, 120, 87, ${p.opacity * 0.7})`);
        this.ctx.fillStyle = grad;

        // Slender curved bamboo blade
        this.ctx.beginPath();
        this.ctx.moveTo(0, -p.size * 1.4);
        this.ctx.quadraticCurveTo(p.size * 0.38, 0, 0, p.size * 1.4);
        this.ctx.quadraticCurveTo(-p.size * 0.38, 0, 0, -p.size * 1.4);
        this.ctx.fill();

        // Delicate midrib vein
        this.ctx.strokeStyle = `rgba(16, 185, 129, ${p.opacity * 0.6})`;
        this.ctx.lineWidth = 0.8;
        this.ctx.beginPath();
        this.ctx.moveTo(0, -p.size * 1.4);
        this.ctx.lineTo(0, p.size * 1.4);
        this.ctx.stroke();
    }

    /**
     * ✨ Summer Night Bioluminescent Firefly (Đom đóm dạ quang lân tinh)
     */
    drawFirefly(p, time) {
        const pulse = 0.5 + 0.5 * Math.sin(time * 0.006 + p.pulseOffset);
        const currentOpacity = p.opacity * pulse;

        // Soft outer phosphorescent glow
        const glow = this.ctx.createRadialGradient(0, 0, 0, 0, 0, p.size * 2.8);
        glow.addColorStop(0, `rgba(254, 240, 138, ${currentOpacity * 0.9})`);
        glow.addColorStop(0.4, `rgba(163, 230, 53, ${currentOpacity * 0.5})`);
        glow.addColorStop(1, 'rgba(163, 230, 53, 0)');
        this.ctx.fillStyle = glow;
        this.ctx.beginPath();
        this.ctx.arc(0, 0, p.size * 2.8, 0, Math.PI * 2);
        this.ctx.fill();

        // Bright white-gold core
        this.ctx.fillStyle = `rgba(255, 255, 255, ${currentOpacity})`;
        this.ctx.beginPath();
        this.ctx.arc(0, 0, p.size * 0.6, 0, Math.PI * 2);
        this.ctx.fill();
    }

    /**
     * 🍂 Golden Ginkgo Leaf (Lá ngân hạnh hình quạt vàng óng)
     */
    drawGinkgoLeaf(p) {
        this.ctx.scale(Math.cos(p.flipProgress * 0.7), 1);
        const grad = this.ctx.createLinearGradient(0, p.size * 0.8, 0, -p.size);
        grad.addColorStop(0, `rgba(217, 119, 6, ${p.opacity})`);
        grad.addColorStop(0.4, `rgba(245, 158, 11, ${p.opacity * 0.9})`);
        grad.addColorStop(1, `rgba(251, 191, 36, ${p.opacity * 0.8})`);
        this.ctx.fillStyle = grad;

        // Fan shape with center notch
        this.ctx.beginPath();
        this.ctx.moveTo(0, 0); // leaf base
        this.ctx.bezierCurveTo(-p.size * 0.8, -p.size * 0.2, -p.size, -p.size * 0.8, -p.size * 0.25, -p.size);
        this.ctx.lineTo(0, -p.size * 0.75); // notch
        this.ctx.lineTo(p.size * 0.25, -p.size);
        this.ctx.bezierCurveTo(p.size, -p.size * 0.8, p.size * 0.8, -p.size * 0.2, 0, 0);
        this.ctx.fill();

        // Delicate slender petiole (stem)
        this.ctx.strokeStyle = `rgba(180, 83, 9, ${p.opacity * 0.7})`;
        this.ctx.lineWidth = 1;
        this.ctx.beginPath();
        this.ctx.moveTo(0, 0);
        this.ctx.lineTo(0, p.size * 0.7);
        this.ctx.stroke();
    }

    /**
     * 🍁 Crimson 5-Pointed Maple Leaf (Lá phong thắm thu)
     */
    drawMapleLeaf(p) {
        this.ctx.scale(Math.cos(p.flipProgress * 0.6), 1);
        const grad = this.ctx.createRadialGradient(0, 0, 1, 0, 0, p.size);
        grad.addColorStop(0, `rgba(153, 27, 27, ${p.opacity})`);
        grad.addColorStop(0.7, `rgba(220, 38, 38, ${p.opacity * 0.85})`);
        grad.addColorStop(1, `rgba(234, 88, 12, ${p.opacity * 0.6})`);
        this.ctx.fillStyle = grad;

        // Draw stylized 5-lobed maple leaf
        this.ctx.beginPath();
        this.ctx.moveTo(0, p.size * 0.6); // stem
        this.ctx.lineTo(0, 0);

        // Center lobe
        this.ctx.lineTo(-p.size * 0.2, -p.size * 0.5);
        this.ctx.lineTo(0, -p.size);
        this.ctx.lineTo(p.size * 0.2, -p.size * 0.5);

        // Right high lobe
        this.ctx.lineTo(p.size * 0.55, -p.size * 0.4);
        this.ctx.lineTo(p.size * 0.9, -p.size * 0.35);
        this.ctx.lineTo(p.size * 0.55, -p.size * 0.1);

        // Right low lobe
        this.ctx.lineTo(p.size * 0.75, p.size * 0.2);
        this.ctx.lineTo(p.size * 0.4, p.size * 0.15);

        // Base connection
        this.ctx.lineTo(0, 0);

        // Left low lobe
        this.ctx.lineTo(-p.size * 0.4, p.size * 0.15);
        this.ctx.lineTo(-p.size * 0.75, p.size * 0.2);
        this.ctx.lineTo(-p.size * 0.55, -p.size * 0.1);

        // Left high lobe
        this.ctx.lineTo(-p.size * 0.9, -p.size * 0.35);
        this.ctx.lineTo(-p.size * 0.55, -p.size * 0.4);
        this.ctx.closePath();
        this.ctx.fill();

        // Stem
        this.ctx.strokeStyle = `rgba(127, 29, 29, ${p.opacity * 0.8})`;
        this.ctx.lineWidth = 1;
        this.ctx.beginPath();
        this.ctx.moveTo(0, 0);
        this.ctx.lineTo(0, p.size * 0.6);
        this.ctx.stroke();
    }

    /**
     * ❄️ Hexagonal Crystal Snowflake (Bông tuyết lục giác tinh thể)
     */
    drawSnowflake(p) {
        this.ctx.strokeStyle = `rgba(248, 250, 252, ${p.opacity})`;
        this.ctx.lineWidth = 1.2;

        for (let i = 0; i < 6; i++) {
            this.ctx.rotate(Math.PI / 3);
            this.ctx.beginPath();
            this.ctx.moveTo(0, 0);
            this.ctx.lineTo(0, p.size);

            // Small crystal side prongs
            this.ctx.moveTo(0, p.size * 0.55);
            this.ctx.lineTo(-p.size * 0.26, p.size * 0.75);
            this.ctx.moveTo(0, p.size * 0.55);
            this.ctx.lineTo(p.size * 0.26, p.size * 0.75);
            this.ctx.stroke();
        }

        // Tiny center crystal dot
        this.ctx.fillStyle = `rgba(224, 242, 254, ${p.opacity * 0.9})`;
        this.ctx.beginPath();
        this.ctx.arc(0, 0, p.size * 0.2, 0, Math.PI * 2);
        this.ctx.fill();
    }

    /**
     * Learning Milestone Celebration Burst (Bùng nổ cánh hoa/lá chúc mừng)
     */
    celebrate(count = 25, originX = null, originY = null) {
        if (!this.enabled) return;
        const width = this.canvas ? this.canvas.width : window.innerWidth;
        const height = this.canvas ? this.canvas.height : window.innerHeight;
        const startX = originX !== null ? originX : width / 2;
        const startY = originY !== null ? originY : height / 2;
        const seasonKey = this.getEffectiveSeason();
        const season = SEASONS_DATA[seasonKey];

        for (let i = 0; i < count; i++) {
            const angle = Math.random() * Math.PI * 2;
            const speed = 3 + Math.random() * 5.5;
            const particleType = season.types[Math.floor(Math.random() * season.types.length)];

            this.celebrationParticles.push({
                x: startX,
                y: startY,
                vx: Math.cos(angle) * speed,
                vy: Math.sin(angle) * speed - 2.5, // initial upward lift
                type: particleType,
                size: 8 + Math.random() * 8,
                rotation: Math.random() * Math.PI * 2,
                rotationSpeed: (Math.random() - 0.5) * 0.08,
                opacity: 0.9,
                life: 1.0,
                flipProgress: Math.random() * Math.PI,
                pulseOffset: Math.random() * Math.PI * 2
            });
        }
    }

    setSeason(seasonKey) {
        if (seasonKey !== 'auto' && !SEASONS_DATA[seasonKey]) return;
        this.seasonSetting = seasonKey;
        localStorage.setItem('chinese_ambiance_season', seasonKey);

        // Re-seed particles smoothly
        this.initParticles();
        this.updateControlUI();
    }

    toggle() {
        this.enabled = !this.enabled;
        localStorage.setItem('chinese_ambiance_enabled', this.enabled ? 'true' : 'false');
        if (this.canvas) {
            this.canvas.style.opacity = this.enabled ? '1' : '0';
        }
        if (this.enabled) {
            this.initParticles();
            this.startAnimation();
        } else {
            this.stopAnimation();
        }
        this.updateControlUI();
    }

    toggleMouseBreeze() {
        this.mouseBreezeEnabled = !this.mouseBreezeEnabled;
        localStorage.setItem('chinese_ambiance_breeze', this.mouseBreezeEnabled ? 'true' : 'false');
        this.updateControlUI();
    }

    setupMouseTracking() {
        window.addEventListener('mousemove', (e) => {
            this.mouseX = e.clientX;
            this.mouseY = e.clientY;
            this.mouseActive = true;
            clearTimeout(this.mouseTimeout);
            this.mouseTimeout = setTimeout(() => {
                this.mouseActive = false;
            }, 120);
        }, { passive: true });
    }

    setupEventListeners() {
        // Listen to custom celebration events from quiz or flashcards
        window.addEventListener('ambiance:celebrate', (e) => {
            const count = e.detail?.count || 28;
            const x = e.detail?.x || null;
            const y = e.detail?.y || null;
            this.celebrate(count, x, y);
        });

        // Click outside to close popover
        document.addEventListener('click', (e) => {
            if (!this.isMenuOpen) return;
            const container = document.getElementById('chinese-ambiance-container');
            if (container && !container.contains(e.target)) {
                this.closeMenu();
            }
        });

        // Escape key to close popover
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.isMenuOpen) {
                this.closeMenu();
            }
        });
    }

    toggleMenu() {
        this.isMenuOpen = !this.isMenuOpen;
        const popover = document.getElementById('chinese-ambiance-popover');
        if (popover) {
            if (this.isMenuOpen) {
                popover.classList.remove('hidden');
                // Trigger reflow for transition
                requestAnimationFrame(() => {
                    popover.classList.remove('opacity-0', 'scale-95', 'translate-y-2');
                    popover.classList.add('opacity-100', 'scale-100', 'translate-y-0');
                });
            } else {
                popover.classList.remove('opacity-100', 'scale-100', 'translate-y-0');
                popover.classList.add('opacity-0', 'scale-95', 'translate-y-2');
                setTimeout(() => {
                    if (!this.isMenuOpen) popover.classList.add('hidden');
                }, 200);
            }
        }
    }

    closeMenu() {
        this.isMenuOpen = false;
        const popover = document.getElementById('chinese-ambiance-popover');
        if (popover) {
            popover.classList.remove('opacity-100', 'scale-100', 'translate-y-0');
            popover.classList.add('opacity-0', 'scale-95', 'translate-y-2');
            setTimeout(() => {
                if (!this.isMenuOpen) popover.classList.add('hidden');
            }, 200);
        }
    }

    createControlUI() {
        const container = document.createElement('div');
        container.id = 'chinese-ambiance-container';
        container.className = 'fixed bottom-5 right-5 z-40 select-none font-sans';

        // Trigger Button (Pill with current season badge)
        const btn = document.createElement('button');
        btn.id = 'chinese-ambiance-trigger';
        btn.type = 'button';
        btn.setAttribute('aria-label', 'Mở bảng điều khiển Bốn Mùa Cổ Phong');
        btn.className = 'flex items-center gap-2 rounded-full border border-amber-900/20 bg-white/95 dark:bg-slate-900/95 px-3.5 py-1.5 text-xs font-bold text-amber-950 dark:text-amber-100 shadow-lg shadow-amber-950/10 backdrop-blur transition-all hover:scale-105 active:scale-95 hover:bg-amber-50 dark:hover:bg-slate-800 cursor-pointer';
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            this.toggleMenu();
        });

        // Popover Menu (Tứ Quý Trà Phổ Jade-tablet Card)
        const popover = document.createElement('div');
        popover.id = 'chinese-ambiance-popover';
        popover.className = 'hidden absolute bottom-full right-0 mb-3 w-80 rounded-2xl border border-amber-900/25 bg-[#fffdfa]/98 dark:bg-slate-900/98 p-4 shadow-2xl shadow-amber-950/20 backdrop-blur-md transition-all duration-200 opacity-0 scale-95 translate-y-2 origin-bottom-right z-50';

        container.appendChild(popover);
        container.appendChild(btn);
        document.body.appendChild(container);

        this.updateControlUI();
    }

    updateControlUI() {
        const trigger = document.getElementById('chinese-ambiance-trigger');
        const popover = document.getElementById('chinese-ambiance-popover');
        if (!trigger || !popover) return;

        const effectiveKey = this.getEffectiveSeason();
        const effectiveSeason = SEASONS_DATA[effectiveKey];
        const isAuto = this.seasonSetting === 'auto';
        const currentMonth = new Date().getMonth() + 1;

        // 1. Update trigger button label
        if (!this.enabled) {
            trigger.innerHTML = `
                <span class="text-sm grayscale opacity-50">🌸</span>
                <span class="hidden sm:inline">Cổ phong:</span>
                <span class="text-slate-400 font-medium">Tắt</span>
                <svg class="h-3.5 w-3.5 text-slate-400 opacity-60" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            `;
        } else {
            trigger.innerHTML = `
                <span class="text-sm">${effectiveSeason.icon}</span>
                <span class="hidden sm:inline">Cổ phong:</span>
                <span class="text-amber-700 dark:text-amber-400 font-extrabold">${isAuto ? 'Tự động' : effectiveSeason.name}</span>
                <svg class="h-3.5 w-3.5 text-amber-800 dark:text-amber-400 opacity-70" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            `;
        }

        // 2. Render popover content
        popover.innerHTML = `
            <!-- Header -->
            <div class="flex items-center justify-between pb-3 border-b border-amber-900/10 dark:border-amber-100/10 mb-3">
                <div class="flex items-center gap-2">
                    <span class="flex h-6 w-6 items-center justify-center rounded-md bg-gradient-to-br from-red-700 to-amber-700 text-white text-xs font-serif font-bold shadow-sm">
                        令
                    </span>
                    <div>
                        <h4 class="text-sm font-bold text-amber-950 dark:text-amber-100 leading-tight">Tứ Quý Trà Phổ</h4>
                        <p class="text-[10px] text-amber-800/70 dark:text-amber-300/70 font-serif">四季时令 · Không Gian Cổ Phong</p>
                    </div>
                </div>
                <button type="button" id="ambiance-close-btn" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-lg leading-none p-1 rounded-md transition-colors" aria-label="Đóng">
                    ×
                </button>
            </div>

            <!-- Master Toggle -->
            <div class="flex items-center justify-between bg-amber-50/70 dark:bg-slate-800/60 rounded-xl p-2.5 mb-3 border border-amber-900/10 dark:border-amber-100/10">
                <div class="flex items-center gap-2">
                    <span class="text-base">✨</span>
                    <span class="text-xs font-semibold text-slate-700 dark:text-slate-200">Hiệu ứng rơi mỹ thuật</span>
                </div>
                <button type="button" id="ambiance-master-toggle" class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none ${this.enabled ? 'bg-emerald-600' : 'bg-slate-300 dark:bg-slate-600'}">
                    <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out ${this.enabled ? 'translate-x-4' : 'translate-x-0'}"></span>
                </button>
            </div>

            ${this.enabled ? `
            <!-- Season Mode Selector -->
            <div class="space-y-1.5 mb-3">
                <p class="text-[10px] uppercase tracking-wider font-bold text-amber-900/60 dark:text-amber-200/60 px-1">Chọn Mùa Không Gian</p>

                <!-- Auto Button -->
                <button type="button" data-season="auto" class="ambiance-season-option w-full flex items-center justify-between p-2 rounded-xl text-left text-xs transition-all ${isAuto ? 'bg-amber-100/80 dark:bg-amber-950/40 text-amber-950 dark:text-amber-100 font-bold border border-amber-900/20' : 'hover:bg-amber-50/60 dark:hover:bg-slate-800/50 text-slate-600 dark:text-slate-300'}">
                    <div class="flex items-center gap-2.5">
                        <span class="text-sm">🗓️</span>
                        <div>
                            <p class="leading-none">Tự động theo ngày thực</p>
                            <p class="text-[10px] text-amber-700 dark:text-amber-400 font-normal mt-0.5">Hiện tại: Tháng ${currentMonth} (${effectiveSeason.name})</p>
                        </div>
                    </div>
                    ${isAuto ? '<span class="text-emerald-700 dark:text-emerald-400 text-xs font-bold">✓</span>' : ''}
                </button>

                <!-- 4 Seasons List -->
                <div class="grid grid-cols-2 gap-1.5 pt-1">
                    ${Object.values(SEASONS_DATA).map(s => {
                        const isSelected = !isAuto && this.seasonSetting === s.id;
                        return `
                            <button type="button" data-season="${s.id}" class="ambiance-season-option flex items-center gap-2 p-2 rounded-xl text-left text-xs transition-all ${isSelected ? 'bg-gradient-to-r from-red-50 to-amber-50 dark:from-red-950/40 dark:to-amber-950/40 text-red-950 dark:text-red-100 font-bold border border-red-700/30' : 'hover:bg-amber-50/60 dark:hover:bg-slate-800/50 text-slate-700 dark:text-slate-300 border border-transparent'}">
                                <span class="text-base">${s.icon}</span>
                                <div class="overflow-hidden">
                                    <p class="leading-tight truncate">${s.name}</p>
                                    <p class="text-[9px] text-slate-400 truncate">${s.badge}</p>
                                </div>
                            </button>
                        `;
                    }).join('')}
                </div>
            </div>

            <!-- Mouse Breeze Option -->
            <div class="flex items-center justify-between py-2 px-1 border-t border-amber-900/10 dark:border-amber-100/10 text-xs text-slate-600 dark:text-slate-300">
                <div class="flex items-center gap-1.5">
                    <span>💨</span>
                    <span>Làn gió theo trỏ chuột</span>
                </div>
                <button type="button" id="ambiance-breeze-toggle" class="relative inline-flex h-4 w-7 shrink-0 cursor-pointer rounded-full border border-transparent transition-colors duration-200 ease-in-out focus:outline-none ${this.mouseBreezeEnabled ? 'bg-amber-600' : 'bg-slate-300 dark:bg-slate-600'}">
                    <span class="pointer-events-none inline-block h-3.5 w-3.5 transform rounded-full bg-white shadow transition duration-200 ease-in-out ${this.mouseBreezeEnabled ? 'translate-x-3' : 'translate-x-0'}"></span>
                </button>
            </div>

            <!-- Classical Poetry Card -->
            <div class="mt-2.5 rounded-xl bg-gradient-to-br from-amber-50/90 to-red-50/60 dark:from-slate-800/80 dark:to-slate-850/80 p-2.5 border border-amber-900/15 dark:border-amber-100/15 text-center">
                <p class="text-xs font-serif font-bold text-amber-950 dark:text-amber-100 tracking-wide">${effectiveSeason.quoteCn}</p>
                <p class="text-[9px] font-sans text-amber-800/70 dark:text-amber-300/70 italic mt-0.5">${effectiveSeason.quotePinyin}</p>
                <p class="text-[10px] text-slate-600 dark:text-slate-300 mt-1 border-t border-amber-900/10 dark:border-amber-100/10 pt-1 leading-snug">"${effectiveSeason.quoteVi}"</p>
            </div>
            ` : `
            <div class="text-center py-4 text-xs text-slate-400">
                Hiệu ứng rơi đang tắt. Bật công tắc trên để cảm nhận không gian bốn mùa cổ phong nhé!
            </div>
            `}
        `;

        // Bind interactive events in popover
        const closeBtn = document.getElementById('ambiance-close-btn');
        if (closeBtn) closeBtn.onclick = () => this.closeMenu();

        const masterToggle = document.getElementById('ambiance-master-toggle');
        if (masterToggle) masterToggle.onclick = () => this.toggle();

        const breezeToggle = document.getElementById('ambiance-breeze-toggle');
        if (breezeToggle) breezeToggle.onclick = () => this.toggleMouseBreeze();

        popover.querySelectorAll('.ambiance-season-option').forEach(btn => {
            btn.onclick = (e) => {
                const season = e.currentTarget.getAttribute('data-season');
                this.setSeason(season);
                // Trigger subtle celebration burst on season switch
                this.celebrate(15);
            };
        });
    }

    setupInkRipple() {
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

export { ChineseAmbianceEngine, SEASONS_DATA };
