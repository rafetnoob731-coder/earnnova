/**
 * EARNNOVA - Ultra Premium Next-Gen UI Engine
 * Version: 2.0.0
 * 3D Tilt | Magnetic Buttons | Particles | Mouse Tracking | Advanced Animations
 */

(function() {
    'use strict';

    // ============================================
    // PREMIUM ENGINE
    // ============================================
    const PremiumEngine = {
        initialized: false,
        particles: [],
        animationFrame: null,
        mouseX: 0,
        mouseY: 0,

        // ============================================
        // INITIALIZATION
        // ============================================
        init() {
            if (this.initialized) return;
            this.initialized = true;

            this.init3DCards();
            this.initMagneticButtons();
            this.initParticles();
            this.initParallax();
            this.initScrollReveal();
            this.initCounters();
            this.initProgressRings();

            // Global mouse tracking
            document.addEventListener('mousemove', (e) => {
                this.mouseX = e.clientX;
                this.mouseY = e.clientY;
            });

            console.log('[EARNNOVA Premium] Engine initialized');
        },

        // ============================================
        // 3D CARD TILT EFFECT
        // ============================================
        init3DCards() {
            document.querySelectorAll('.card-3d').forEach(card => {
                card.addEventListener('mousemove', (e) => {
                    const rect = card.getBoundingClientRect();
                    const x = e.clientX - rect.left;
                    const y = e.clientY - rect.top;
                    const centerX = rect.width / 2;
                    const centerY = rect.height / 2;
                    
                    // Calculate rotation (max ±10 degrees)
                    const rotateX = ((y - centerY) / centerY) * -8;
                    const rotateY = ((x - centerX) / centerX) * 8;
                    
                    // Set CSS custom properties for 3D effect
                    card.style.setProperty('--mouse-x', rotateY);
                    card.style.setProperty('--mouse-y', rotateX);
                    card.style.setProperty('--mouse-x-percent', `${(x / rect.width) * 100}%`);
                    card.style.setProperty('--mouse-y-percent', `${(y / rect.height) * 100}%`);
                    
                    // Apply transform to inner element
                    const inner = card.querySelector('.card-3d-inner');
                    if (inner) {
                        inner.style.transform = `rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale3d(1.02, 1.02, 1.02)`;
                    }
                });

                card.addEventListener('mouseleave', () => {
                    const inner = card.querySelector('.card-3d-inner');
                    if (inner) {
                        inner.style.transform = 'rotateX(0deg) rotateY(0deg) scale3d(1, 1, 1)';
                    }
                    card.style.setProperty('--mouse-x', 0);
                    card.style.setProperty('--mouse-y', 0);
                });
            });
        },

        // ============================================
        // MAGNETIC BUTTONS
        // ============================================
        initMagneticButtons() {
            document.querySelectorAll('.btn-magnetic').forEach(btn => {
                btn.addEventListener('mousemove', (e) => {
                    const rect = btn.getBoundingClientRect();
                    const x = e.clientX - rect.left - rect.width / 2;
                    const y = e.clientY - rect.top - rect.height / 2;
                    
                    // Magnetic pull (max 10px)
                    const pullX = x * 0.3;
                    const pullY = y * 0.3;
                    
                    btn.style.transform = `translate(${pullX}px, ${pullY}px)`;
                });

                btn.addEventListener('mouseleave', () => {
                    btn.style.transform = 'translate(0, 0)';
                });
            });
        },

        // ============================================
        // PARTICLE SYSTEM
        // ============================================
        initParticles() {
            const container = document.querySelector('.particle-container');
            if (!container) return;

            const particleCount = Math.min(Math.floor(window.innerWidth / 20), 80);

            for (let i = 0; i < particleCount; i++) {
                this.createParticle(container);
            }
        },

        createParticle(container) {
            const particle = document.createElement('div');
            particle.className = 'particle';
            
            const size = Math.random() * 4 + 2;
            const x = Math.random() * 100;
            const duration = Math.random() * 15 + 10;
            const delay = Math.random() * 20;
            const opacity = Math.random() * 0.4 + 0.1;
            
            particle.style.cssText = `
                left: ${x}%;
                width: ${size}px;
                height: ${size}px;
                animation-duration: ${duration}s;
                animation-delay: ${delay}s;
                opacity: ${opacity};
            `;
            
            container.appendChild(particle);
            this.particles.push(particle);
        },

        // ============================================
        // PARALLAX EFFECT
        // ============================================
        initParallax() {
            document.querySelectorAll('[data-parallax]').forEach(el => {
                const speed = parseFloat(el.dataset.parallax) || 0.1;
                
                document.addEventListener('mousemove', (e) => {
                    const x = (e.clientX / window.innerWidth - 0.5) * speed * 40;
                    const y = (e.clientY / window.innerHeight - 0.5) * speed * 40;
                    
                    el.style.transform = `translate(${x}px, ${y}px)`;
                });
            });
        },

        // ============================================
        // SCROLL REVEAL ANIMATIONS
        // ============================================
        initScrollReveal() {
            if (!('IntersectionObserver' in window)) return;

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const el = entry.target;
                        const anim = el.dataset.reveal || 'fadeInUp';
                        const delay = parseInt(el.dataset.revealDelay) || 0;
                        
                        setTimeout(() => {
                            el.classList.add(`reveal-${anim}`, 'revealed');
                        }, delay);
                        
                        observer.unobserve(el);
                    }
                });
            }, {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            });

            document.querySelectorAll('[data-reveal]').forEach(el => {
                el.style.opacity = '0';
                observer.observe(el);
            });
        },

        // ============================================
        // ANIMATED COUNTERS
        // ============================================
        initCounters() {
            document.querySelectorAll('[data-count-premium]').forEach(el => {
                const target = parseFloat(el.dataset.countPremium);
                const duration = parseInt(el.dataset.countDuration) || 2000;
                const decimals = parseInt(el.dataset.countDecimals) || 0;
                const prefix = el.dataset.countPrefix || '';
                const suffix = el.dataset.countSuffix || '';
                
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            this.animateCounter(el, target, duration, decimals, prefix, suffix);
                            observer.unobserve(el);
                        }
                    });
                }, { threshold: 0.5 });

                observer.observe(el);
            });
        },

        animateCounter(el, target, duration, decimals, prefix, suffix) {
            const start = performance.now();
            
            const update = (currentTime) => {
                const elapsed = currentTime - start;
                const progress = Math.min(elapsed / duration, 1);
                
                // Ease out cubic
                const eased = 1 - Math.pow(1 - progress, 3);
                const current = eased * target;
                
                el.textContent = prefix + current.toFixed(decimals) + suffix;
                
                if (progress < 1) {
                    requestAnimationFrame(update);
                } else {
                    el.textContent = prefix + target.toFixed(decimals) + suffix;
                }
            };
            
            requestAnimationFrame(update);
        },

        // ============================================
        // PROGRESS RINGS
        // ============================================
        initProgressRings() {
            document.querySelectorAll('.progress-ring[data-percent]').forEach(ring => {
                const percent = parseInt(ring.dataset.percent) || 0;
                const size = parseInt(ring.dataset.size) || 120;
                const strokeWidth = parseInt(ring.dataset.strokeWidth) || 6;
                const color = ring.dataset.color || 'var(--electric-blue)';
                
                const radius = (size - strokeWidth) / 2;
                const circumference = 2 * Math.PI * radius;
                const offset = circumference - (percent / 100) * circumference;
                
                // Create SVG
                const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
                svg.setAttribute('width', size);
                svg.setAttribute('height', size);
                svg.setAttribute('viewBox', `0 0 ${size} ${size}`);
                
                const defs = document.createElementNS('http://www.w3.org/2000/svg', 'defs');
                const gradient = document.createElementNS('http://www.w3.org/2000/svg', 'linearGradient');
                gradient.setAttribute('id', `ringGrad-${Date.now()}-${Math.random()}`);
                gradient.setAttribute('x1', '0%');
                gradient.setAttribute('y1', '0%');
                gradient.setAttribute('x2', '100%');
                gradient.setAttribute('y2', '100%');
                
                const stop1 = document.createElementNS('http://www.w3.org/2000/svg', 'stop');
                stop1.setAttribute('offset', '0%');
                stop1.setAttribute('stop-color', 'var(--electric-blue)');
                
                const stop2 = document.createElementNS('http://www.w3.org/2000/svg', 'stop');
                stop2.setAttribute('offset', '100%');
                stop2.setAttribute('stop-color', 'var(--royal-purple)');
                
                gradient.appendChild(stop1);
                gradient.appendChild(stop2);
                defs.appendChild(gradient);
                svg.appendChild(defs);
                
                // Background circle
                const bgCircle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
                bgCircle.setAttribute('cx', size / 2);
                bgCircle.setAttribute('cy', size / 2);
                bgCircle.setAttribute('r', radius);
                bgCircle.setAttribute('class', 'ring-bg');
                
                // Foreground circle
                const fgCircle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
                fgCircle.setAttribute('cx', size / 2);
                fgCircle.setAttribute('cy', size / 2);
                fgCircle.setAttribute('r', radius);
                fgCircle.setAttribute('class', 'ring-fg');
                fgCircle.setAttribute('stroke', `url(#${gradient.id})`);
                fgCircle.style.strokeDasharray = circumference;
                fgCircle.style.strokeDashoffset = circumference;
                
                svg.appendChild(bgCircle);
                svg.appendChild(fgCircle);
                ring.prepend(svg);
                
                // Value text
                const valueEl = document.createElement('div');
                valueEl.className = 'ring-value';
                valueEl.textContent = '0%';
                ring.appendChild(valueEl);
                
                // Animate on scroll
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            setTimeout(() => {
                                fgCircle.style.strokeDashoffset = offset;
                                
                                let current = 0;
                                const interval = setInterval(() => {
                                    current++;
                                    valueEl.textContent = current + '%';
                                    if (current >= percent) {
                                        clearInterval(interval);
                                        valueEl.textContent = percent + '%';
                                    }
                                }, 20);
                            }, 300);
                            observer.unobserve(ring);
                        }
                    });
                }, { threshold: 0.5 });
                
                observer.observe(ring);
            });
        },

        // ============================================
        // SHOW TOAST (Enhanced)
        // ============================================
        showToast(title, message, type = 'info', duration = 5000) {
            const container = document.querySelector('.toast-container') || (() => {
                const c = document.createElement('div');
                c.className = 'toast-container';
                document.body.appendChild(c);
                return c;
            })();

            const icons = {
                success: '✓',
                error: '✕',
                warning: '⚠',
                info: 'ℹ'
            };

            const toast = document.createElement('div');
            toast.className = `toast toast-premium toast-${type}`;
            toast.innerHTML = `
                <div style="display:flex;align-items:center;gap:12px;width:100%;">
                    <span style="font-size:1.3rem;font-weight:700;width:28px;text-align:center;">
                        ${icons[type] || 'ℹ'}
                    </span>
                    <div style="flex:1;">
                        <div style="font-weight:600;font-size:0.95rem;">${this.escapeHtml(title)}</div>
                        <div style="font-size:0.85rem;color:rgba(255,255,255,0.6);margin-top:2px;">${this.escapeHtml(message)}</div>
                    </div>
                    <button style="background:none;border:none;color:rgba(255,255,255,0.4);font-size:1.2rem;cursor:pointer;padding:4px;" onclick="this.closest('.toast').remove()">✕</button>
                </div>
            `;

            container.appendChild(toast);

            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(100px) scale(0.9)';
                toast.style.transition = 'all 0.5s var(--ease-smooth)';
                setTimeout(() => toast.remove(), 500);
            }, duration);
        },

        // ============================================
        // SHOW SUCCESS (Enhanced)
        // ============================================
        showSuccess(title = 'Success!', message = 'Action completed') {
            const overlay = document.getElementById('successOverlay') || (() => {
                const o = document.createElement('div');
                o.id = 'successOverlay';
                o.className = 'success-overlay';
                o.innerHTML = `
                    <div class="success-content">
                        <div class="success-checkmark-premium">✓</div>
                        <h2 class="success-title" style="font-size:1.8rem;">${this.escapeHtml(title)}</h2>
                        <p class="success-message" style="font-size:1.1rem;color:rgba(255,255,255,0.7);">${this.escapeHtml(message)}</p>
                        <button class="btn btn-primary btn-lg btn-glow-premium" onclick="PremiumEngine.hideSuccess()" style="margin-top:20px;">Continue</button>
                    </div>
                `;
                document.body.appendChild(o);
                return o;
            })();

            overlay.querySelector('.success-title').textContent = title;
            overlay.querySelector('.success-message').textContent = message;
            overlay.classList.add('show');
            this.createConfetti();
        },

        hideSuccess() {
            const overlay = document.getElementById('successOverlay');
            if (overlay) overlay.classList.remove('show');
        },

        // ============================================
        // CONFETTI (Enhanced)
        // ============================================
        createConfetti() {
            const colors = ['#4361ee', '#7209b7', '#00b4d8', '#06d6a0', '#ffd700', '#ef476f', '#ff6b35'];
            
            for (let i = 0; i < 60; i++) {
                const confetti = document.createElement('div');
                confetti.className = 'confetti-premium';
                
                const size = Math.random() * 8 + 4;
                const x = Math.random() * 100;
                const color = colors[Math.floor(Math.random() * colors.length)];
                const duration = Math.random() * 2 + 2;
                const delay = Math.random() * 2;
                const rotation = Math.random() * 360;
                const shape = Math.random() > 0.5 ? '50%' : '0';
                
                confetti.style.cssText = `
                    left: ${x}%;
                    top: -10px;
                    width: ${size}px;
                    height: ${size}px;
                    background: ${color};
                    border-radius: ${shape};
                    animation-duration: ${duration}s;
                    animation-delay: ${delay}s;
                    transform: rotate(${rotation}deg);
                `;
                
                document.body.appendChild(confetti);
                
                setTimeout(() => confetti.remove(), (duration + delay) * 1000 + 500);
            }
        },

        // ============================================
        // UTILITIES
        // ============================================
        escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        },

        copyToClipboard(text) {
            if (navigator.clipboard) {
                navigator.clipboard.writeText(text).then(() => {
                    this.showToast('Copied!', 'Text copied to clipboard', 'success');
                });
            } else {
                const textarea = document.createElement('textarea');
                textarea.value = text;
                document.body.appendChild(textarea);
                textarea.select();
                document.execCommand('copy');
                document.body.removeChild(textarea);
                this.showToast('Copied!', 'Text copied to clipboard', 'success');
            }
        }
    };

    // ============================================
    // EXPOSE TO GLOBAL SCOPE
    // ============================================
    window.PremiumEngine = PremiumEngine;

    // Auto-initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => PremiumEngine.init());
    } else {
        PremiumEngine.init();
    }

})();
