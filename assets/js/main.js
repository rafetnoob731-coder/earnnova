/**
 * EARNNOVA - Premium Frontend Framework
 * Version: 1.0.0
 */

// ============================================
// EARNNOVA Global Object
// ============================================
const EARNNOVA = {
    config: {
        siteName: 'EARNNOVA',
        theme: localStorage.getItem('earnnova-theme') || 'dark',
        apiEndpoint: '/api',
        cooldownSeconds: 30,
        dailyAdLimit: 20,
        defaultReward: 0.01
    },

    state: {
        user: null,
        balance: 0,
        referralBalance: 0,
        todayEarnings: 0,
        adsWatched: 0,
        isCooldown: false,
        cooldownTimer: null
    },

    elements: {},

    // ============================================
    // Initialization
    // ============================================
    init() {
        this.cacheElements();
        this.initTheme();
        this.initSidebar();
        this.initToasts();
        this.initAnimations();
        this.initScrollAnimations();
        this.loadUserState();
    },

    cacheElements() {
        this.elements = {
            body: document.body,
            sidebar: document.querySelector('.sidebar'),
            sidebarToggle: document.querySelector('.sidebar-toggle'),
            themeToggle: document.querySelector('.theme-toggle'),
            toastContainer: document.querySelector('.toast-container'),
            mainContent: document.querySelector('.main-content'),
            successOverlay: document.querySelector('.success-overlay'),
            modals: document.querySelectorAll('.modal-overlay')
        };

        // Create toast container if not exists
        if (!this.elements.toastContainer) {
            this.elements.toastContainer = document.createElement('div');
            this.elements.toastContainer.className = 'toast-container';
            document.body.appendChild(this.elements.toastContainer);
        }

        // Create success overlay if not exists
        if (!this.elements.successOverlay) {
            const overlay = document.createElement('div');
            overlay.className = 'success-overlay';
            overlay.innerHTML = `
                <div class="success-content">
                    <div class="success-checkmark">✓</div>
                    <h2 class="success-title">Success!</h2>
                    <p class="success-message">Reward credited successfully</p>
                    <button class="btn btn-primary" onclick="EARNNOVA.hideSuccess()">Continue</button>
                </div>
            `;
            document.body.appendChild(overlay);
            this.elements.successOverlay = overlay;
        }
    },

    // ============================================
    // Theme System
    // ============================================
    initTheme() {
        document.documentElement.setAttribute('data-theme', this.config.theme);
        
        if (this.elements.themeToggle) {
            this.elements.themeToggle.addEventListener('click', () => this.toggleTheme());
            this.elements.themeToggle.innerHTML = this.config.theme === 'dark' ? '☀️' : '🌙';
        }
    },

    toggleTheme() {
        this.config.theme = this.config.theme === 'dark' ? 'light' : 'dark';
        localStorage.setItem('earnnova-theme', this.config.theme);
        document.documentElement.setAttribute('data-theme', this.config.theme);
        
        if (this.elements.themeToggle) {
            this.elements.themeToggle.innerHTML = this.config.theme === 'dark' ? '☀️' : '🌙';
        }
    },

    // ============================================
    // Sidebar
    // ============================================
    initSidebar() {
        // Toggle sidebar on mobile
        if (this.elements.sidebarToggle) {
            this.elements.sidebarToggle.addEventListener('click', () => {
                this.elements.sidebar.classList.toggle('open');
            });
        }

        // Close sidebar on outside click (mobile)
        document.addEventListener('click', (e) => {
            if (window.innerWidth <= 1024) {
                if (!e.target.closest('.sidebar') && !e.target.closest('.sidebar-toggle')) {
                    this.elements.sidebar?.classList.remove('open');
                }
            }
        });

        // Submenu toggle
        document.querySelectorAll('.nav-item.has-submenu').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                this.classList.toggle('expanded');
                const submenu = this.nextElementSibling;
                if (submenu && submenu.classList.contains('nav-submenu')) {
                    submenu.classList.toggle('open');
                }
            });
        });

        // Active route
        const currentPath = window.location.pathname;
        document.querySelectorAll('.nav-item').forEach(item => {
            const href = item.getAttribute('href');
            if (href && currentPath.includes(href)) {
                item.classList.add('active');
            }
        });
    },

    // ============================================
    // Toast Notifications
    // ============================================
    initToasts() {
        // Global error handler for AJAX
        window.addEventListener('unhandledrejection', (event) => {
            this.showToast('Error', event.reason?.message || 'An unexpected error occurred', 'error');
        });
    },

    showToast(title, message, type = 'info', duration = 5000) {
        const icons = {
            success: '✅',
            error: '❌',
            warning: '⚠️',
            info: 'ℹ️'
        };

        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.innerHTML = `
            <span class="toast-icon">${icons[type] || 'ℹ️'}</span>
            <div class="toast-content">
                <div class="toast-title">${this.escapeHtml(title)}</div>
                <div class="toast-message">${this.escapeHtml(message)}</div>
            </div>
            <button class="toast-close" onclick="this.parentElement.remove()">&times;</button>
        `;

        this.elements.toastContainer.appendChild(toast);

        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(100px)';
            toast.style.transition = 'all 0.3s ease';
            setTimeout(() => toast.remove(), 300);
        }, duration);
    },

    // ============================================
    // Success Animation
    // ============================================
    showSuccess(title = 'Success!', message = 'Reward credited successfully') {
        const overlay = this.elements.successOverlay;
        overlay.querySelector('.success-title').textContent = title;
        overlay.querySelector('.success-message').textContent = message;
        overlay.classList.add('show');
        this.createConfetti();
    },

    hideSuccess() {
        this.elements.successOverlay.classList.remove('show');
    },

    createConfetti() {
        const colors = ['#4361ee', '#7209b7', '#00b4d8', '#06d6a0', '#ffd166', '#ef476f'];
        const container = document.body;

        for (let i = 0; i < 50; i++) {
            const confetti = document.createElement('div');
            confetti.className = 'confetti-piece';
            confetti.style.left = Math.random() * 100 + '%';
            confetti.style.top = '-10px';
            confetti.style.background = colors[Math.floor(Math.random() * colors.length)];
            confetti.style.width = Math.random() * 8 + 4 + 'px';
            confetti.style.height = Math.random() * 8 + 4 + 'px';
            confetti.style.borderRadius = Math.random() > 0.5 ? '50%' : '0';
            confetti.style.animationDuration = Math.random() * 2 + 2 + 's';
            confetti.style.animationDelay = Math.random() * 2 + 's';
            container.appendChild(confetti);

            setTimeout(() => confetti.remove(), 5000);
        }
    },

    // ============================================
    // Animations
    // ============================================
    initAnimations() {
        // Animate elements on page load
        document.querySelectorAll('[data-animate]').forEach(el => {
            const animation = el.dataset.animate || 'fade-in';
            el.classList.add(`animate-${animation}`);
        });
    },

    initScrollAnimations() {
        // Intersection Observer for scroll animations
        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const el = entry.target;
                        const animation = el.dataset.scrollAnimate || 'slide-in-up';
                        el.classList.add(`animate-${animation}`);
                        observer.unobserve(el);
                    }
                });
            }, { threshold: 0.1, rootMargin: '50px' });

            document.querySelectorAll('[data-scroll-animate]').forEach(el => {
                observer.observe(el);
            });
        }
    },

    // ============================================
    // User State
    // ============================================
    loadUserState() {
        // This would be loaded from server/API in production
        // For now, check if user data exists in DOM
        const userDataEl = document.getElementById('user-data');
        if (userDataEl) {
            try {
                this.state.user = JSON.parse(userDataEl.textContent);
                this.updateUI();
            } catch (e) {
                console.error('Failed to parse user data:', e);
            }
        }
    },

    updateUI() {
        const user = this.state.user;
        if (!user) return;

        // Update balance displays
        document.querySelectorAll('[data-balance]').forEach(el => {
            el.textContent = '$' + parseFloat(user.balance || 0).toFixed(2);
        });

        document.querySelectorAll('[data-referral-balance]').forEach(el => {
            el.textContent = '$' + parseFloat(user.referral_balance || 0).toFixed(2);
        });

        document.querySelectorAll('[data-username]').forEach(el => {
            el.textContent = user.username || 'User';
        });

        document.querySelectorAll('[data-email]').forEach(el => {
            el.textContent = user.email || '';
        });

        // Account status
        document.querySelectorAll('[data-status]').forEach(el => {
            const status = user.activation_status || 'inactive';
            el.textContent = status.charAt(0).toUpperCase() + status.slice(1);
            el.className = `badge badge-${status === 'active' ? 'success' : 'warning'}`;
        });
    },

    // ============================================
    // Ad System
    // ============================================
    adSystem: {
        isProcessing: false,

        watchAd(adType, rewardAmount) {
            if (EARNNOVA.adSystem.isProcessing) return;
            if (EARNNOVA.state.isCooldown) {
                EARNNOVA.showToast('Cooldown', 'Please wait for cooldown to end', 'warning');
                return;
            }

            EARNNOVA.adSystem.isProcessing = true;
            EARNNOVA.showToast('Watching Ad', 'Loading advertisement...', 'info');

            // Simulate ad watching (in production, this would call the actual ad network)
            EARNNOVA.adSystem.showAd(adType, () => {
                // Ad completed callback
                EARNNOVA.adSystem.completeAd(adType, rewardAmount);
            }, (error) => {
                // Ad error callback
                EARNNOVA.adSystem.isProcessing = false;
                EARNNOVA.showToast('Error', 'Failed to load advertisement', 'error');
            });
        },

        showAd(type, onComplete, onError) {
            switch(type) {
                case 'rewarded_interstitial':
                    // Use the provided ad code
                    if (typeof show_9622450 === 'function') {
                        show_9622450().then(() => {
                            onComplete();
                        }).catch((e) => {
                            onError(e);
                        });
                    } else {
                        // Fallback simulation
                        setTimeout(onComplete, 3000);
                    }
                    break;

                case 'rewarded_popup':
                    if (typeof show_9622450 === 'function') {
                        show_9622450('pop').then(() => {
                            onComplete();
                        }).catch((e) => {
                            onError(e);
                        });
                    } else {
                        setTimeout(onComplete, 3000);
                    }
                    break;

                case 'banner':
                case 'interstitial':
                default:
                    setTimeout(onComplete, 2000);
                    break;
            }
        },

        completeAd(adType, rewardAmount) {
            // Send reward request to server
            fetch('/api/rewards.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': EARNNOVA.getCSRFToken()
                },
                body: JSON.stringify({
                    action: 'claim_reward',
                    ad_type: adType,
                    reward_amount: rewardAmount
                })
            })
            .then(response => response.json())
            .then(data => {
                EARNNOVA.adSystem.isProcessing = false;

                if (data.success) {
                    // Update balance
                    if (data.new_balance) {
                        EARNNOVA.state.balance = data.new_balance;
                        document.querySelectorAll('[data-balance]').forEach(el => {
                            el.textContent = '$' + parseFloat(data.new_balance).toFixed(2);
                        });
                    }

                    // Show success
                    EARNNOVA.showSuccess(
                        'Reward Earned!',
                        `You earned $${parseFloat(rewardAmount).toFixed(4)}`
                    );

                    // Start cooldown
                    EARNNOVA.adSystem.startCooldown();

                    // Update ad count
                    EARNNOVA.state.adsWatched++;
                    document.querySelectorAll('[data-ads-watched]').forEach(el => {
                        el.textContent = EARNNOVA.state.adsWatched;
                    });
                } else {
                    EARNNOVA.showToast('Error', data.message || 'Failed to claim reward', 'error');
                }
            })
            .catch(error => {
                EARNNOVA.adSystem.isProcessing = false;
                EARNNOVA.showToast('Error', 'Network error, please try again', 'error');
            });
        },

        startCooldown() {
            EARNNOVA.state.isCooldown = true;
            let remaining = EARNNOVA.config.cooldownSeconds;
            
            const updateCooldown = () => {
                document.querySelectorAll('[data-cooldown]').forEach(el => {
                    el.textContent = `${remaining}s`;
                });

                if (remaining <= 0) {
                    EARNNOVA.state.isCooldown = false;
                    document.querySelectorAll('[data-cooldown]').forEach(el => {
                        el.textContent = 'Ready';
                        el.closest('.cooldown-timer')?.classList.add('hidden');
                    });
                    clearInterval(EARNNOVA.state.cooldownTimer);
                }
                remaining--;
            };

            updateCooldown();
            EARNNOVA.state.cooldownTimer = setInterval(updateCooldown, 1000);
        }
    },

    // ============================================
    // Utility Functions
    // ============================================
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    },

    getCSRFToken() {
        return document.querySelector('meta[name="csrf-token"]')?.content || '';
    },

    formatNumber(num) {
        return new Intl.NumberFormat('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 4
        }).format(num);
    },

    formatCurrency(amount) {
        return '$' + this.formatNumber(amount);
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
    },

    // ============================================
    // Modal System
    // ============================================
    openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
        }
    },

    closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('show');
            document.body.style.overflow = '';
        }
    },

    closeAllModals() {
        document.querySelectorAll('.modal-overlay.show').forEach(modal => {
            modal.classList.remove('show');
        });
        document.body.style.overflow = '';
    }
};

// ============================================
// Initialize on DOM Ready
// ============================================
document.addEventListener('DOMContentLoaded', () => {
    EARNNOVA.init();

    // Close modals on overlay click
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) {
                overlay.classList.remove('show');
                document.body.style.overflow = '';
            }
        });
    });

    // Auto-incrementing counters
    document.querySelectorAll('[data-count-to]').forEach(el => {
        const target = parseFloat(el.dataset.countTo);
        const duration = parseInt(el.dataset.countDuration) || 2000;
        const start = 0;
        const startTime = performance.now();

        function updateCount(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 3); // easeOutCubic
            const current = start + (target - start) * eased;
            
            if (target % 1 === 0) {
                el.textContent = Math.floor(current).toLocaleString();
            } else {
                el.textContent = current.toFixed(2);
            }

            if (progress < 1) {
                requestAnimationFrame(updateCount);
            }
        }

        requestAnimationFrame(updateCount);
    });
});

// Make EARNNOVA globally accessible
window.EARNNOVA = EARNNOVA;
