/**
 * Chinese Cultural Four Seasons Ambiance Engine (四季时令·新国风氛围引擎)
 * 
 * - Four Seasons Particle System (四季粒子矩阵):
 *   🌸 Xuân (Spring): Peach & Red Plum petals (Đào hoa khinh vũ)
 *   🍃 Hạ (Summer): Jade Bamboo leaves & Bioluminescent Fireflies (Trúc lâm dạ huỳnh)
 *   🍁 Thu (Autumn): Golden Ginkgo fans & Crimson Maple leaves (Kim ngân phong diệp)
 *   ❄️ Đông (Winter): Hexagonal Crystal Snowflakes & Cold Plum petals (Thụy tuyết hàn mai)
 * - Auto Calendar Detection based on real-time month (locked to seasonal flow)
 * - Interactive Mouse Breeze Dynamics (repulsion physics)
 * - Learning Milestone Celebration Shower (Radial Blossom Burst)
 * - Managed directly within Student Profile (no floating UI conflict with Pet Mascot)
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
        period: 'Tháng 2 – 4',
        desc: 'Cánh đào hồng phớt & mai đỏ chu sa bồng bềnh 3D',
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
        period: 'Tháng 5 – 7',
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
        period: 'Tháng 8 – 10',
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
        period: 'Tháng 11 – 1',
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

        this.init();
    }

    /**
     * Season is locked to real-time calendar cycle (Theo thời gian mùa trong năm):
     * Tháng 2 - 4: Xuân (Đào hoa & Mai đỏ)
     * Tháng 5 - 7: Hạ (Trúc lâm & Đom đóm)
     * Tháng 8 - 10: Thu (Kim ngân & Lá phong)
     * Tháng 11 - 1: Đông (Tuyết lục giác & Hàn mai)
     */
    getEffectiveSeason() {
        const month = new Date().getMonth() + 1;
        if (month >= 2 && month <= 4) return 'spring';
        if (month >= 5 && month <= 7) return 'summer';
        if (month >= 8 && month <= 10) return 'autumn';
        return 'winter';
    }

    getSeasonData(seasonKey) {
        return SEASONS_DATA[seasonKey] || SEASONS_DATA.autumn;
    }

    init() {
        if (typeof window === 'undefined') return;

        this.createCanvas();
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
        this.canvas.style.zIndex = '35'; // Sits above background, below modals, companion and navigation
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
        this.ctx.scale(Math.cos(p.flipProgress), 1);
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

        this.ctx.beginPath();
        this.ctx.moveTo(0, 0);
        this.ctx.bezierCurveTo(-p.size * 0.8, -p.size * 0.2, -p.size, -p.size * 0.8, -p.size * 0.25, -p.size);
        this.ctx.lineTo(0, -p.size * 0.75);
        this.ctx.lineTo(p.size * 0.25, -p.size);
        this.ctx.bezierCurveTo(p.size, -p.size * 0.8, p.size * 0.8, -p.size * 0.2, 0, 0);
        this.ctx.fill();

        // Slender petiole (stem)
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

        this.ctx.beginPath();
        this.ctx.moveTo(0, p.size * 0.6);
        this.ctx.lineTo(0, 0);

        // Center lobe
        this.ctx.lineTo(-p.size * 0.2, -p.size * 0.5);
        this.ctx.lineTo(0, -p.size);
        this.ctx.lineTo(p.size * 0.2, -p.size * 0.5);

        // Right lobes
        this.ctx.lineTo(p.size * 0.55, -p.size * 0.4);
        this.ctx.lineTo(p.size * 0.9, -p.size * 0.35);
        this.ctx.lineTo(p.size * 0.55, -p.size * 0.1);
        this.ctx.lineTo(p.size * 0.75, p.size * 0.2);
        this.ctx.lineTo(p.size * 0.4, p.size * 0.15);

        // Base connection
        this.ctx.lineTo(0, 0);

        // Left lobes
        this.ctx.lineTo(-p.size * 0.4, p.size * 0.15);
        this.ctx.lineTo(-p.size * 0.75, p.size * 0.2);
        this.ctx.lineTo(-p.size * 0.55, -p.size * 0.1);
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
                vy: Math.sin(angle) * speed - 2.5,
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

    setEnabled(enabled) {
        this.enabled = !!enabled;
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
    }

    toggle() {
        this.setEnabled(!this.enabled);
    }

    toggleMouseBreeze() {
        this.mouseBreezeEnabled = !this.mouseBreezeEnabled;
        localStorage.setItem('chinese_ambiance_breeze', this.mouseBreezeEnabled ? 'true' : 'false');
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

        // Listen to cross-component toggle events
        window.addEventListener('ambiance:set-enabled', (e) => {
            this.setEnabled(e.detail?.enabled);
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
