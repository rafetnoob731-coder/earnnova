/**
 * EARNNOVA - Mobile-First Engine v1.0.0
 * Bottom Navigation | PWA Install | Touch Optimization | App-Like Experience
 */

(function() {
    'use strict';

    const MobileEngine = {
        initialized: false,
        deferredPrompt: null,
        isInstalled: false,
        installPromptShown: false,

        // ============================================
        // INITIALIZATION
        // ============================================
        init() {
            if (this.initialized) return;
            this.initialized = true;

            // Only initialize on mobile/webapp
            if (window.innerWidth > 1024 && !window.matchMedia('(display-mode: standalone)').matches) {
                return;
            }

            this.injectBottomNav();
            this.injectMobileHeader();
            this.initPWA();
            this.initTouchOptimizations();
            this.initSmoothScroll();
            this.initPageTransitions();
            this.preventPullToRefresh();
            this.initActiveNavState();

            console.log('[MobileEngine] Initialized');
        },

        // ============================================
        // BOTTOM NAVIGATION
        // ============================================
        injectBottomNav() {
            if (document.querySelector('.mobile-bottom-nav')) return;
            if (document.body.classList.contains('no-mobile-nav')) return;

            const currentPage = window.location.pathname.split('/').pop() || 'dashboard.php';
            
            const navItems = [
                { icon: '🏠', label: 'Home', href: '/dashboard.php', id: 'dashboard' },
                { icon: '📺', label: 'Earn', href: '/ads.php', id: 'ads' },
                { icon: '⭐', label: 'Missions', href: '/missions.php', id: 'missions', center: true },
                { icon: '👥', label: 'Refer', href: '/referral.php', id: 'referral' },
                { icon: '👤', label: 'Profile', href: '/profile.php', id: 'profile' },
            ];

            const nav = document.createElement('nav');
            nav.className = 'mobile-bottom-nav';
            nav.setAttribute('role', 'navigation');
            nav.setAttribute('aria-label', 'Main navigation');

            navItems.forEach(item => {
                const isActive = currentPage === item.href.split('/').pop() || 
                                (item.id === 'dashboard' && (currentPage === '' || currentPage === 'index.php'));
                
                const link = document.createElement('a');
                link.className = `mobile-nav-item${isActive ? ' active' : ''}${item.center ? ' nav-center' : ''}`;
                link.href = item.href;
                link.setAttribute('aria-current', isActive ? 'page' : 'false');
                
                if (item.center) {
                    link.innerHTML = `
                        <span class="nav-icon-wrapper">
                            <span class="nav-icon">${item.icon}</span>
                        </span>
                        <span class="nav-label">${item.label}</span>
                    `;
                } else {
                    link.innerHTML = `
                        <span class="nav-icon">${item.icon}</span>
                        <span class="nav-label">${item.label}</span>
                    `;
                }

                // Haptic feedback on tap
                link.addEventListener('click', (e) => {
                    if (navigator.vibrate) navigator.vibrate(10);
                });

                nav.appendChild(link);
            });

            document.body.appendChild(nav);
        },

        // ============================================
        // MOBILE HEADER
        // ============================================
        injectMobileHeader() {
            if (document.querySelector('.mobile-header')) return;

            const pageTitle = document.querySelector('.page-title')?.textContent || 
                            document.title.split(' - ').pop() || 'Dashboard';

            const header = document.createElement('header');
            header.className = 'mobile-header';
            header.innerHTML = `
                <div class="mobile-header-title">
                    <span class="header-logo">E</span>
                    <span>${this.escapeHtml(pageTitle)}</span>
                </div>
                <div class="mobile-header-actions">
                    <button class="mobile-header-btn" id="notifBtn" aria-label="Notifications">
                        🔔
                        <span class="badge-dot" id="notifDot" style="display:none;"></span>
                    </button>
                    <button class="mobile-header-btn" id="themeBtnMobile" aria-label="Toggle theme">
                        ☀️
                    </button>
                </div>
            `;

            // Insert after body start
            document.body.insertBefore(header, document.body.firstChild);

            // Theme toggle
            document.getElementById('themeBtnMobile')?.addEventListener('click', () => {
                if (window.EARNNOVA) {
                    EARNNOVA.toggleTheme();
                    this.updateThemeIcon();
                }
            });

            // Notification button
            document.getElementById('notifBtn')?.addEventListener('click', () => {
                if (window.EARNNOVA) {
                    EARNNOVA.showToast('Notifications', 'No new notifications', 'info');
                }
            });

            this.updateThemeIcon();
        },

        updateThemeIcon() {
            const btn = document.getElementById('themeBtnMobile');
            if (btn) {
                const theme = localStorage.getItem('earnnova-theme') || 'dark';
                btn.textContent = theme === 'dark' ? '☀️' : '🌙';
            }
        },

        // ============================================
        // PWA INSTALL SUPPORT
        // ============================================
        initPWA() {
            // Check if already installed
            if (window.matchMedia('(display-mode: standalone)').matches) {
                this.isInstalled = true;
                return;
            }

            // Listen for install prompt
            window.addEventListener('beforeinstallprompt', (e) => {
                e.preventDefault();
                this.deferredPrompt = e;
                
                // Show install prompt after a delay
                if (!this.installPromptShown) {
                    setTimeout(() => this.showInstallPrompt(), 10000);
                }
            });

            // Listen for successful install
            window.addEventListener('appinstalled', () => {
                this.isInstalled = true;
                this.hideInstallPrompt();
                if (window.EARNNOVA) {
                    EARNNOVA.showToast('Installed!', 'EARNNOVA is now installed on your device', 'success');
                }
            });
        },

        showInstallPrompt() {
            if (this.isInstalled || !this.deferredPrompt || this.installPromptShown) return;
            this.installPromptShown = true;

            const existing = document.querySelector('.mobile-install-prompt');
            if (existing) return;

            const prompt = document.createElement('div');
            prompt.className = 'mobile-install-prompt show';
            prompt.innerHTML = `
                <div class="install-icon">E</div>
                <div class="install-text">
                    <div class="install-title">Install EARNNOVA</div>
                    <div class="install-subtitle">Get the app-like experience</div>
                </div>
                <div class="install-actions">
                    <button class="install-btn dismiss" id="dismissInstall">Later</button>
                    <button class="install-btn primary" id="installBtn">Install</button>
                </div>
            `;

            document.body.appendChild(prompt);

            document.getElementById('installBtn')?.addEventListener('click', async () => {
                if (this.deferredPrompt) {
                    this.deferredPrompt.prompt();
                    const result = await this.deferredPrompt.userChoice;
                    if (result.outcome === 'accepted') {
                        console.log('[PWA] User accepted install');
                    }
                    this.deferredPrompt = null;
                }
                this.hideInstallPrompt();
            });

            document.getElementById('dismissInstall')?.addEventListener('click', () => {
                this.hideInstallPrompt();
                // Show again after 7 days
                localStorage.setItem('earnnova-install-dismissed', Date.now().toString());
            });
        },

        hideInstallPrompt() {
            const prompt = document.querySelector('.mobile-install-prompt');
            if (prompt) {
                prompt.style.transform = 'translateY(100px)';
                prompt.style.opacity = '0';
                prompt.style.transition = 'all 0.3s ease';
                setTimeout(() => prompt.remove(), 300);
            }
        },

        // ============================================
        // TOUCH OPTIMIZATIONS
        // ============================================
        initTouchOptimizations() {
            // Prevent double-tap zoom
            document.addEventListener('touchend', (e) => {
                const now = Date.now();
                if (now - (this.lastTouch || 0) < 300) {
                    e.preventDefault();
                }
                this.lastTouch = now;
            }, { passive: false });

            // Add touch feedback to all interactive elements
            document.querySelectorAll('a, button, .clickable').forEach(el => {
                el.addEventListener('touchstart', () => {
                    el.style.transition = 'transform 0.15s ease';
                }, { passive: true });
            });

            // Smooth scrolling
            document.addEventListener('touchmove', () => {}, { passive: true });
        },

        // ============================================
        // SMOOTH SCROLL
        // ============================================
        initSmoothScroll() {
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', (e) => {
                    const target = document.querySelector(anchor.getAttribute('href'));
                    if (target) {
                        e.preventDefault();
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });
        },

        // ============================================
        // PAGE TRANSITIONS
        // ============================================
        initPageTransitions() {
            // Add fade-in animation on page load
            document.body.style.animation = 'mobileFadeIn 0.3s ease';
            
            const style = document.createElement('style');
            style.textContent = `
                @keyframes mobileFadeIn {
                    from { opacity: 0; transform: translateY(8px); }
                    to { opacity: 1; transform: translateY(0); }
                }
            `;
            document.head.appendChild(style);
        },

        // ============================================
        // PREVENT PULL-TO-REFRESH
        // ============================================
        preventPullToRefresh() {
            let lastTouchY = 0;
            
            document.addEventListener('touchstart', (e) => {
                lastTouchY = e.touches[0].clientY;
            }, { passive: true });

            document.addEventListener('touchmove', (e) => {
                const touchY = e.touches[0].clientY;
                const scrollY = window.scrollY;
                
                // Prevent pull-to-refresh when at top
                if (scrollY === 0 && touchY > lastTouchY) {
                    e.preventDefault();
                }
                lastTouchY = touchY;
            }, { passive: false });
        },

        // ============================================
        // ACTIVE NAV STATE
        // ============================================
        initActiveNavState() {
            // Update active state on page load
            const currentPath = window.location.pathname;
            document.querySelectorAll('.mobile-nav-item').forEach(item => {
                const href = item.getAttribute('href');
                if (href && currentPath.includes(href)) {
                    document.querySelectorAll('.mobile-nav-item').forEach(i => i.classList.remove('active'));
                    item.classList.add('active');
                    item.setAttribute('aria-current', 'page');
                }
            });

            // Update mobile header title
            const titleEl = document.querySelector('.mobile-header-title span:last-child');
            if (titleEl) {
                const pageTitle = document.title.split(' - ').pop() || 'Dashboard';
                titleEl.textContent = pageTitle;
            }
        },

        // ============================================
        // UTILITY: TOAST
        // ============================================
        showToast(title, message, type = 'info', duration = 4000) {
            const container = document.querySelector('.mobile-toast-container') || (() => {
                const c = document.createElement('div');
                c.className = 'mobile-toast-container';
                document.body.appendChild(c);
                return c;
            })();

            const icons = {
                success: '✅',
                error: '❌',
                warning: '⚠️',
                info: 'ℹ️'
            };

            const toast = document.createElement('div');
            toast.className = `mobile-toast toast-${type}`;
            toast.innerHTML = `
                <span class="toast-icon">${icons[type] || 'ℹ️'}</span>
                <div class="toast-text">
                    <div class="toast-title">${this.escapeHtml(title)}</div>
                    <div class="toast-message">${this.escapeHtml(message)}</div>
                </div>
            `;

            container.appendChild(toast);

            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(-10px) scale(0.95)';
                toast.style.transition = 'all 0.3s ease';
                setTimeout(() => toast.remove(), 300);
            }, duration);
        },

        // ============================================
        // UTILITY: SUCCESS OVERLAY
        // ============================================
        showSuccess(title = 'Success!', message = 'Action completed') {
            const overlay = document.createElement('div');
            overlay.className = 'mobile-success-overlay show';
            overlay.innerHTML = `
                <div class="mobile-success-content">
                    <div class="mobile-success-icon">✓</div>
                    <h2 class="mobile-success-title">${this.escapeHtml(title)}</h2>
                    <p class="mobile-success-message">${this.escapeHtml(message)}</p>
                    <button class="mobile-btn mobile-btn-primary" onclick="this.closest('.mobile-success-overlay').remove()">
                        Continue
                    </button>
                </div>
            `;
            document.body.appendChild(overlay);

            overlay.addEventListener('click', (e) => {
                if (e.target === overlay) overlay.remove();
            });

            // Auto-close after 5s
            setTimeout(() => {
                if (overlay.parentNode) overlay.remove();
            }, 5000);
        },

        // ============================================
        // UTILITIES
        // ============================================
        escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    };

    // ============================================
    // EXPOSE
    // ============================================
    window.MobileEngine = MobileEngine;

    // Auto-initialize
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => MobileEngine.init());
    } else {
        MobileEngine.init();
    }

})();
