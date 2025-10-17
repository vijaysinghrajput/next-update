/**
 * Mobile App JavaScript Library
 * Complete mobile app functionality for webview
 */

class MobileApp {
    constructor() {
        this.isWebView = this.detectWebView();
        this.isIOS = this.detectIOS();
        this.isAndroid = this.detectAndroid();
        this.touchStartY = 0;
        this.touchStartX = 0;
        this.pullToRefreshThreshold = 80;
        this.isPulling = false;
        this.isRefreshing = false;
        this.hasUserInteracted = false;
        
        this.init();
    }

    init() {
        this.setupTouchEvents();
        this.setupPullToRefresh();
        this.setupInfiniteScroll();
        this.setupNativeFeatures();
        this.setupHapticFeedback();
        this.setupKeyboardHandling();
        this.setupOrientationHandling();
        this.setupNetworkStatus();
        this.setupOfflineSupport();
        this.setupPerformanceOptimizations();
        
        // Initialize after DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.onDOMReady());
        } else {
            this.onDOMReady();
        }
    }

    onDOMReady() {
        this.setupMobileNavigation();
        this.setupMobileModals();
        this.setupMobileAlerts();
        this.setupMobileForms();
        this.setupMobileCards();
        this.setupMobileLists();
        this.setupMobileButtons();
        this.setupMobileLoading();
        this.setupMobileAnimations();
        this.setupMobileAccessibility();
        
        // Trigger ready event
        this.triggerEvent('mobileAppReady');
    }

    // ===== DETECTION METHODS =====
    detectWebView() {
        const userAgent = navigator.userAgent.toLowerCase();
        return userAgent.includes('wv') || 
               userAgent.includes('webview') || 
               (window.navigator.standalone === true) ||
               (window.matchMedia && window.matchMedia('(display-mode: standalone)').matches);
    }

    detectIOS() {
        return /iPad|iPhone|iPod/.test(navigator.userAgent);
    }

    detectAndroid() {
        return /Android/.test(navigator.userAgent);
    }

    // ===== TOUCH EVENTS =====
    setupTouchEvents() {
        // Add touch feedback to all interactive elements
        document.addEventListener('touchstart', (e) => {
            this.hasUserInteracted = true;
            const target = e.target.closest('.mobile-touch-feedback, .mobile-btn, .mobile-list-item, .mobile-card');
            if (target) {
                target.classList.add('mobile-touch-active');
                this.hapticFeedback('light');
            }
        }, { passive: true });

        document.addEventListener('touchend', (e) => {
            const target = e.target.closest('.mobile-touch-feedback, .mobile-btn, .mobile-list-item, .mobile-card');
            if (target) {
                setTimeout(() => {
                    target.classList.remove('mobile-touch-active');
                }, 150);
            }
        }, { passive: true });

        // Prevent zoom on double tap
        let lastTouchEnd = 0;
        document.addEventListener('touchend', (e) => {
            const now = (new Date()).getTime();
            if (now - lastTouchEnd <= 300) {
                e.preventDefault();
            }
            lastTouchEnd = now;
        }, false);

        // Handle swipe gestures
        this.setupSwipeGestures();
    }

    setupSwipeGestures() {
        let startX, startY, endX, endY;
        const minSwipeDistance = 50;

        document.addEventListener('touchstart', (e) => {
            startX = e.touches[0].clientX;
            startY = e.touches[0].clientY;
        }, { passive: true });

        document.addEventListener('touchend', (e) => {
            endX = e.changedTouches[0].clientX;
            endY = e.changedTouches[0].clientY;
            
            const deltaX = endX - startX;
            const deltaY = endY - startY;
            
            if (Math.abs(deltaX) > minSwipeDistance || Math.abs(deltaY) > minSwipeDistance) {
                if (Math.abs(deltaX) > Math.abs(deltaY)) {
                    // Horizontal swipe
                    if (deltaX > 0) {
                        this.triggerEvent('swipeRight', { deltaX, deltaY });
                    } else {
                        this.triggerEvent('swipeLeft', { deltaX, deltaY });
                    }
                } else {
                    // Vertical swipe
                    if (deltaY > 0) {
                        this.triggerEvent('swipeDown', { deltaX, deltaY });
                    } else {
                        this.triggerEvent('swipeUp', { deltaX, deltaY });
                    }
                }
            }
        }, { passive: true });
    }

    // ===== PULL TO REFRESH =====
    setupPullToRefresh() {
        const pullRefreshContainer = document.querySelector('.mobile-pull-refresh');
        if (!pullRefreshContainer) return;

        const indicator = pullRefreshContainer.querySelector('.mobile-pull-refresh-indicator');
        if (!indicator) return;

        pullRefreshContainer.addEventListener('touchstart', (e) => {
            this.touchStartY = e.touches[0].clientY;
        }, { passive: true });

        pullRefreshContainer.addEventListener('touchmove', (e) => {
            if (window.scrollY === 0) {
                const touchY = e.touches[0].clientY;
                const pullDistance = touchY - this.touchStartY;
                
                if (pullDistance > 0) {
                    e.preventDefault();
                    this.isPulling = true;
                    
                    if (pullDistance > this.pullToRefreshThreshold) {
                        indicator.classList.add('active');
                        indicator.innerHTML = '<i class="fas fa-sync-alt fa-spin"></i>';
                    } else {
                        indicator.classList.remove('active');
                        indicator.innerHTML = '<i class="fas fa-arrow-down"></i>';
                    }
                }
            }
        }, { passive: false });

        pullRefreshContainer.addEventListener('touchend', (e) => {
            if (this.isPulling && window.scrollY === 0) {
                const touchY = e.changedTouches[0].clientY;
                const pullDistance = touchY - this.touchStartY;
                
                if (pullDistance > this.pullToRefreshThreshold) {
                    this.triggerRefresh();
                } else {
                    this.resetPullToRefresh();
                }
            }
            this.isPulling = false;
        }, { passive: true });
    }

    triggerRefresh() {
        if (this.isRefreshing) return;
        
        this.isRefreshing = true;
        const indicator = document.querySelector('.mobile-pull-refresh-indicator');
        
        if (indicator) {
            indicator.classList.add('active');
            indicator.innerHTML = '<i class="fas fa-sync-alt fa-spin"></i>';
        }
        
        this.triggerEvent('pullToRefresh');
        
        // Auto reset after 2 seconds if no manual reset
        setTimeout(() => {
            this.resetPullToRefresh();
        }, 2000);
    }

    resetPullToRefresh() {
        this.isRefreshing = false;
        const indicator = document.querySelector('.mobile-pull-refresh-indicator');
        
        if (indicator) {
            indicator.classList.remove('active');
            indicator.innerHTML = '<i class="fas fa-arrow-down"></i>';
        }
    }

    // ===== INFINITE SCROLL =====
    setupInfiniteScroll() {
        const infiniteScrollContainer = document.querySelector('.mobile-infinite-scroll');
        if (!infiniteScrollContainer) return;

        const trigger = infiniteScrollContainer.querySelector('.mobile-infinite-scroll-trigger');
        if (!trigger) return;

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !this.isLoading) {
                    this.triggerInfiniteScroll();
                }
            });
        }, {
            root: null,
            rootMargin: '100px',
            threshold: 0.1
        });

        observer.observe(trigger);
    }

    triggerInfiniteScroll() {
        if (this.isLoading) return;
        
        this.isLoading = true;
        const trigger = document.querySelector('.mobile-infinite-scroll-trigger');
        
        if (trigger) {
            trigger.classList.add('loading');
            trigger.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading more...';
        }
        
        this.triggerEvent('infiniteScroll');
        
        // Auto reset after 3 seconds if no manual reset
        setTimeout(() => {
            this.resetInfiniteScroll();
        }, 3000);
    }

    resetInfiniteScroll() {
        this.isLoading = false;
        const trigger = document.querySelector('.mobile-infinite-scroll-trigger');
        
        if (trigger) {
            trigger.classList.remove('loading');
            trigger.innerHTML = '<i class="fas fa-arrow-up"></i> Pull up for more';
        }
    }

    // ===== NATIVE FEATURES =====
    setupNativeFeatures() {
        // Setup native app communication
        if (this.isWebView) {
            this.setupNativeBridge();
        }
        
        // Setup native actions
        this.setupNativeActions();
    }

    setupNativeBridge() {
        // Create native bridge for communication with native app
        window.MobileAppBridge = {
            call: (phoneNumber) => this.nativeCall(phoneNumber),
            whatsapp: (phoneNumber, message) => this.nativeWhatsApp(phoneNumber, message),
            share: (data) => this.nativeShare(data),
            openUrl: (url) => this.nativeOpenUrl(url),
            vibrate: (pattern) => this.nativeVibrate(pattern),
            showToast: (message) => this.nativeShowToast(message),
            showAlert: (title, message) => this.nativeShowAlert(title, message),
            getDeviceInfo: () => this.nativeGetDeviceInfo(),
            setStatusBarColor: (color) => this.nativeSetStatusBarColor(color),
            setNavigationBarColor: (color) => this.nativeSetNavigationBarColor(color)
        };
    }

    setupNativeActions() {
        // Handle phone calls
        document.addEventListener('click', (e) => {
            const callBtn = e.target.closest('[data-action="call"]');
            if (callBtn) {
                e.preventDefault();
                const phoneNumber = callBtn.dataset.phone || callBtn.href.replace('tel:', '');
                this.handleCall(phoneNumber);
            }
        });

        // Handle WhatsApp
        document.addEventListener('click', (e) => {
            const whatsappBtn = e.target.closest('[data-action="whatsapp"]');
            if (whatsappBtn) {
                e.preventDefault();
                const phoneNumber = whatsappBtn.dataset.phone || whatsappBtn.href.replace('https://wa.me/', '');
                const message = whatsappBtn.dataset.message || '';
                this.handleWhatsApp(phoneNumber, message);
            }
        });

        // Handle website links
        document.addEventListener('click', (e) => {
            const websiteBtn = e.target.closest('[data-action="website"]');
            if (websiteBtn) {
                e.preventDefault();
                const url = websiteBtn.dataset.url || websiteBtn.href;
                this.handleWebsite(url);
            }
        });

        // Handle share
        document.addEventListener('click', (e) => {
            const shareBtn = e.target.closest('[data-action="share"]');
            if (shareBtn) {
                e.preventDefault();
                const shareData = {
                    title: shareBtn.dataset.title || document.title,
                    text: shareBtn.dataset.text || '',
                    url: shareBtn.dataset.url || window.location.href
                };
                this.handleShare(shareData);
            }
        });
    }

    // ===== NATIVE ACTION HANDLERS =====
    handleCall(phoneNumber) {
        this.hapticFeedback('medium');
        
        if (this.isWebView) {
            // Use native bridge
            this.nativeCall(phoneNumber);
        } else {
            // Fallback to tel: link
            window.location.href = `tel:${phoneNumber}`;
        }
    }

    handleWhatsApp(phoneNumber, message = '') {
        this.hapticFeedback('light');
        
        if (this.isWebView) {
            // Use native bridge
            this.nativeWhatsApp(phoneNumber, message);
        } else {
            // Fallback to WhatsApp web
            const encodedMessage = encodeURIComponent(message);
            window.open(`https://wa.me/${phoneNumber}?text=${encodedMessage}`, '_blank');
        }
    }

    handleWebsite(url) {
        this.hapticFeedback('light');
        
        if (this.isWebView) {
            // Use native bridge
            this.nativeOpenUrl(url);
        } else {
            // Fallback to new window
            window.open(url, '_blank');
        }
    }

    handleShare(shareData) {
        this.hapticFeedback('light');
        
        // Always use Play Store link instead of current URL
        const playStoreLink = 'https://play.google.com/store/apps/details?id=com.skyably.nextupdate';
        
        // Modify shareData to use Play Store link
        const modifiedShareData = {
            ...shareData,
            url: playStoreLink,
            text: shareData.text ? `${shareData.text}\n\n📱 Download Next Update App:\n${playStoreLink}` : `📱 Download Next Update App:\n${playStoreLink}`
        };
        
        if (navigator.share && this.isWebView) {
            // Use native share
            navigator.share(modifiedShareData).catch(console.error);
        } else if (this.isWebView) {
            // Use native bridge
            this.nativeShare(modifiedShareData);
        } else {
            // Fallback to clipboard
            this.copyToClipboard(modifiedShareData.text || modifiedShareData.url);
            this.showToast('App link copied to clipboard');
        }
    }

    // ===== NATIVE BRIDGE METHODS =====
    nativeCall(phoneNumber) {
        if (window.Android && window.Android.call) {
            window.Android.call(phoneNumber);
        } else if (window.webkit && window.webkit.messageHandlers && window.webkit.messageHandlers.call) {
            window.webkit.messageHandlers.call.postMessage({ phoneNumber });
        } else {
            window.location.href = `tel:${phoneNumber}`;
        }
    }

    nativeWhatsApp(phoneNumber, message) {
        if (window.Android && window.Android.whatsapp) {
            window.Android.whatsapp(phoneNumber, message);
        } else if (window.webkit && window.webkit.messageHandlers && window.webkit.messageHandlers.whatsapp) {
            window.webkit.messageHandlers.whatsapp.postMessage({ phoneNumber, message });
        } else {
            const encodedMessage = encodeURIComponent(message);
            window.open(`https://wa.me/${phoneNumber}?text=${encodedMessage}`, '_blank');
        }
    }

    nativeShare(shareData) {
        if (window.Android && window.Android.share) {
            window.Android.share(JSON.stringify(shareData));
        } else if (window.webkit && window.webkit.messageHandlers && window.webkit.messageHandlers.share) {
            window.webkit.messageHandlers.share.postMessage(shareData);
        } else if (navigator.share) {
            navigator.share(shareData).catch(console.error);
        }
    }

    nativeOpenUrl(url) {
        if (window.Android && window.Android.openUrl) {
            window.Android.openUrl(url);
        } else if (window.webkit && window.webkit.messageHandlers && window.webkit.messageHandlers.openUrl) {
            window.webkit.messageHandlers.openUrl.postMessage({ url });
        } else {
            window.open(url, '_blank');
        }
    }

    nativeVibrate(pattern) {
        if (window.Android && window.Android.vibrate) {
            window.Android.vibrate(JSON.stringify(pattern));
        } else if (window.webkit && window.webkit.messageHandlers && window.webkit.messageHandlers.vibrate) {
            window.webkit.messageHandlers.vibrate.postMessage({ pattern });
        } else if (navigator.vibrate && this.hasUserInteracted) {
            try {
                navigator.vibrate(pattern);
            } catch (e) {
                console.log('Vibration blocked by browser');
            }
        }
    }

    nativeShowToast(message) {
        if (window.Android && window.Android.showToast) {
            window.Android.showToast(message);
        } else if (window.webkit && window.webkit.messageHandlers && window.webkit.messageHandlers.showToast) {
            window.webkit.messageHandlers.showToast.postMessage({ message });
        } else {
            this.showToast(message);
        }
    }

    nativeShowAlert(title, message) {
        if (window.Android && window.Android.showAlert) {
            window.Android.showAlert(title, message);
        } else if (window.webkit && window.webkit.messageHandlers && window.webkit.messageHandlers.showAlert) {
            window.webkit.messageHandlers.showAlert.postMessage({ title, message });
        } else {
            alert(`${title}\n\n${message}`);
        }
    }

    nativeGetDeviceInfo() {
        if (window.Android && window.Android.getDeviceInfo) {
            return JSON.parse(window.Android.getDeviceInfo());
        } else if (window.webkit && window.webkit.messageHandlers && window.webkit.messageHandlers.getDeviceInfo) {
            return new Promise((resolve) => {
                window.webkit.messageHandlers.getDeviceInfo.postMessage({});
                window.getDeviceInfoCallback = resolve;
            });
        } else {
            return {
                platform: this.isIOS ? 'ios' : this.isAndroid ? 'android' : 'web',
                webView: this.isWebView,
                userAgent: navigator.userAgent
            };
        }
    }

    nativeSetStatusBarColor(color) {
        if (window.Android && window.Android.setStatusBarColor) {
            window.Android.setStatusBarColor(color);
        } else if (window.webkit && window.webkit.messageHandlers && window.webkit.messageHandlers.setStatusBarColor) {
            window.webkit.messageHandlers.setStatusBarColor.postMessage({ color });
        }
    }

    nativeSetNavigationBarColor(color) {
        if (window.Android && window.Android.setNavigationBarColor) {
            window.Android.setNavigationBarColor(color);
        } else if (window.webkit && window.webkit.messageHandlers && window.webkit.messageHandlers.setNavigationBarColor) {
            window.webkit.messageHandlers.setNavigationBarColor.postMessage({ color });
        }
    }

    // ===== HAPTIC FEEDBACK =====
    setupHapticFeedback() {
        this.hapticPatterns = {
            light: [10],
            medium: [20],
            heavy: [30],
            success: [10, 50, 10],
            warning: [20, 50, 20],
            error: [30, 50, 30, 50, 30]
        };
    }

    hapticFeedback(type = 'light') {
        const pattern = this.hapticPatterns[type] || this.hapticPatterns.light;
        this.nativeVibrate(pattern);
    }

    // ===== KEYBOARD HANDLING =====
    setupKeyboardHandling() {
        let initialViewportHeight = window.innerHeight;
        
        window.addEventListener('resize', () => {
            const currentHeight = window.innerHeight;
            const heightDifference = initialViewportHeight - currentHeight;
            
            if (heightDifference > 150) {
                // Keyboard is open
                document.body.classList.add('keyboard-open');
                this.triggerEvent('keyboardOpen', { height: currentHeight });
            } else {
                // Keyboard is closed
                document.body.classList.remove('keyboard-open');
                this.triggerEvent('keyboardClose', { height: currentHeight });
            }
        });
    }

    // ===== ORIENTATION HANDLING =====
    setupOrientationHandling() {
        window.addEventListener('orientationchange', () => {
            setTimeout(() => {
                this.triggerEvent('orientationChange', { 
                    orientation: screen.orientation ? screen.orientation.angle : window.orientation 
                });
            }, 100);
        });
    }

    // ===== NETWORK STATUS =====
    setupNetworkStatus() {
        window.addEventListener('online', () => {
            this.triggerEvent('networkOnline');
            this.showToast('Connection restored');
        });

        window.addEventListener('offline', () => {
            this.triggerEvent('networkOffline');
            this.showToast('No internet connection');
        });
    }

    // ===== OFFLINE SUPPORT =====
    setupOfflineSupport() {
        // Register service worker if available
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js').catch(console.error);
        }
        
        // Setup offline detection
        this.isOnline = navigator.onLine;
        
        // Cache critical resources
        this.setupResourceCaching();
    }

    setupResourceCaching() {
        // Cache critical CSS and JS files
        const criticalResources = [
            '/public/assets/css/mobile-app.css',
            '/public/assets/js/mobile-app.js',
            '/public/assets/css/style.css'
        ];
        
        if ('caches' in window) {
            caches.open('mobile-app-v1').then(cache => {
                criticalResources.forEach(resource => {
                    cache.add(resource).catch(console.error);
                });
            });
        }
    }

    // ===== PERFORMANCE OPTIMIZATIONS =====
    setupPerformanceOptimizations() {
        // Lazy load images
        this.setupLazyLoading();
        
        // Debounce scroll events
        this.setupScrollOptimization();
        
        // Optimize animations
        this.setupAnimationOptimization();
    }

    setupLazyLoading() {
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        imageObserver.unobserve(img);
                    }
                });
            });

            document.querySelectorAll('img[data-src]').forEach(img => {
                imageObserver.observe(img);
            });
        }
    }

    setupScrollOptimization() {
        let scrollTimeout;
        window.addEventListener('scroll', () => {
            if (scrollTimeout) {
                clearTimeout(scrollTimeout);
            }
            scrollTimeout = setTimeout(() => {
                this.triggerEvent('scrollEnd');
            }, 150);
        }, { passive: true });
    }

    setupAnimationOptimization() {
        // Use transform and opacity for better performance
        const style = document.createElement('style');
        style.textContent = `
            .mobile-animate {
                will-change: transform, opacity;
            }
            .mobile-animate.animate-complete {
                will-change: auto;
            }
        `;
        document.head.appendChild(style);
    }

    // ===== MOBILE COMPONENTS SETUP =====
    setupMobileNavigation() {
        const navItems = document.querySelectorAll('.mobile-nav .nav-item');
        navItems.forEach(item => {
            item.addEventListener('click', (e) => {
                // Remove active class from all items
                navItems.forEach(navItem => navItem.classList.remove('active'));
                // Add active class to clicked item
                item.classList.add('active');
                
                this.hapticFeedback('light');
            });
        });
    }

    setupMobileModals() {
        // Handle modal open/close
        document.addEventListener('click', (e) => {
            const modalTrigger = e.target.closest('[data-modal]');
            if (modalTrigger) {
                e.preventDefault();
                const modalId = modalTrigger.dataset.modal;
                this.openModal(modalId);
            }
            
            const modalClose = e.target.closest('.mobile-modal-close, [data-modal-close]');
            if (modalClose) {
                e.preventDefault();
                const modal = modalClose.closest('.mobile-modal');
                if (modal) {
                    this.closeModal(modal);
                }
            }
        });
    }

    setupMobileAlerts() {
        // Auto-dismiss alerts after 5 seconds
        document.querySelectorAll('.mobile-alert').forEach(alert => {
            setTimeout(() => {
                alert.style.animation = 'alertSlideOut 0.3s ease';
                setTimeout(() => {
                    alert.remove();
                }, 300);
            }, 5000);
        });
    }

    setupMobileForms() {
        // Add mobile-specific form enhancements
        document.querySelectorAll('.mobile-form-control').forEach(input => {
            // Add focus states
            input.addEventListener('focus', () => {
                input.parentElement.classList.add('focused');
            });
            
            input.addEventListener('blur', () => {
                input.parentElement.classList.remove('focused');
            });
        });
    }

    setupMobileCards() {
        // Add card interactions
        document.querySelectorAll('.mobile-card').forEach(card => {
            card.addEventListener('click', (e) => {
                if (!e.target.closest('button, a, input, select, textarea')) {
                    this.hapticFeedback('light');
                }
            });
        });
    }

    setupMobileLists() {
        // Add list item interactions
        document.querySelectorAll('.mobile-list-item').forEach(item => {
            item.addEventListener('click', (e) => {
                this.hapticFeedback('light');
            });
        });
    }

    setupMobileButtons() {
        // Add button interactions
        document.querySelectorAll('.mobile-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                this.hapticFeedback('medium');
            });
        });
    }

    setupMobileLoading() {
        // Handle loading states
        this.loadingElements = document.querySelectorAll('.mobile-loading');
    }

    setupMobileAnimations() {
        // Add entrance animations
        const animatedElements = document.querySelectorAll('.mobile-animate');
        const animationObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-complete');
                    animationObserver.unobserve(entry.target);
                }
            });
        });

        animatedElements.forEach(el => {
            animationObserver.observe(el);
        });
    }

    setupMobileAccessibility() {
        // Add accessibility enhancements
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Tab') {
                document.body.classList.add('keyboard-navigation');
            }
        });

        document.addEventListener('mousedown', () => {
            document.body.classList.remove('keyboard-navigation');
        });
    }

    // ===== UTILITY METHODS =====
    openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'flex';
            document.body.classList.add('modal-open');
            this.hapticFeedback('light');
        }
    }

    closeModal(modal) {
        modal.style.display = 'none';
        document.body.classList.remove('modal-open');
        this.hapticFeedback('light');
    }

    showToast(message, duration = 3000) {
        const toast = document.createElement('div');
        toast.className = 'mobile-toast';
        toast.textContent = message;
        toast.style.cssText = `
            position: fixed;
            bottom: 100px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0,0,0,0.8);
            color: white;
            padding: 12px 24px;
            border-radius: 25px;
            font-size: 14px;
            z-index: 10000;
            animation: toastSlideIn 0.3s ease;
        `;
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.style.animation = 'toastSlideOut 0.3s ease';
            setTimeout(() => {
                document.body.removeChild(toast);
            }, 300);
        }, duration);
    }

    copyToClipboard(text) {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(text);
        } else {
            // Fallback for older browsers
            const textArea = document.createElement('textarea');
            textArea.value = text;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
        }
    }

    triggerEvent(eventName, data = {}) {
        const event = new CustomEvent(eventName, { detail: data });
        document.dispatchEvent(event);
    }

    // ===== PUBLIC API =====
    refresh() {
        this.triggerRefresh();
    }

    loadMore() {
        this.triggerInfiniteScroll();
    }

    showLoading(message = 'Loading...') {
        const loading = document.createElement('div');
        loading.className = 'mobile-loading-overlay';
        loading.innerHTML = `
            <div class="mobile-loading">
                <div class="mobile-spinner"></div>
                <div class="mobile-loading-text">${message}</div>
            </div>
        `;
        loading.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255,255,255,0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
        `;
        
        document.body.appendChild(loading);
        return loading;
    }

    hideLoading(loadingElement) {
        if (loadingElement && loadingElement.parentNode) {
            loadingElement.parentNode.removeChild(loadingElement);
        }
    }
}

// ===== CSS ANIMATIONS =====
const style = document.createElement('style');
style.textContent = `
    @keyframes toastSlideIn {
        from {
            opacity: 0;
            transform: translateX(-50%) translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }
    }
    
    @keyframes toastSlideOut {
        from {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }
        to {
            opacity: 0;
            transform: translateX(-50%) translateY(-20px);
        }
    }
    
    @keyframes alertSlideOut {
        from {
            opacity: 1;
            transform: translateX(0);
        }
        to {
            opacity: 0;
            transform: translateX(-20px);
        }
    }
    
    .mobile-touch-active {
        transform: scale(0.95);
        transition: transform 0.1s ease;
    }
    
    .keyboard-open .mobile-nav {
        display: none;
    }
    
    .keyboard-navigation *:focus {
        outline: 2px solid #667eea;
        outline-offset: 2px;
    }
    
    .modal-open {
        overflow: hidden;
    }
    
    .lazy {
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .lazy.loaded {
        opacity: 1;
    }
`;
document.head.appendChild(style);

// ===== INITIALIZE MOBILE APP =====
window.MobileApp = new MobileApp();

// ===== EXPORT FOR MODULE SYSTEMS =====
if (typeof module !== 'undefined' && module.exports) {
    module.exports = MobileApp;
}
