        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Mobile webview optimizations
        document.addEventListener('DOMContentLoaded', function() {
            // Prevent zoom on input focus
            const inputs = document.querySelectorAll('input, textarea, select');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    if (window.innerWidth <= 768) {
                        setTimeout(() => {
                            window.scrollTo(0, 0);
                        }, 300);
                    }
                });
            });
            
            // Add touch feedback for buttons
            const buttons = document.querySelectorAll('.btn, .nav-link');
            buttons.forEach(button => {
                button.addEventListener('touchstart', function() {
                    this.style.transform = 'scale(0.95)';
                });
                button.addEventListener('touchend', function() {
                    this.style.transform = 'scale(1)';
                });
            });
        });
        
        // Toggle sidebar on mobile
        function toggleSidebar() {
            const sidebar = document.getElementById('userSidebar');
            const overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.toggle('show');
            if (overlay) {
                overlay.classList.toggle('show');
            }
        }
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('userSidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const toggleBtn = event.target.closest('[onclick="toggleSidebar()"]');
            
            if (window.innerWidth <= 768 && !sidebar.contains(event.target) && !toggleBtn) {
                sidebar.classList.remove('show');
                if (overlay) {
                    overlay.classList.remove('show');
                }
            }
        });
        
        // Close sidebar when clicking overlay
        document.addEventListener('click', function(event) {
            if (event.target.id === 'sidebarOverlay') {
                const sidebar = document.getElementById('userSidebar');
                const overlay = document.getElementById('sidebarOverlay');
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
            }
        });
        
        // Auto-hide alerts (but not persistent ones)
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert:not(.alert-persistent)');
            alerts.forEach(alert => {
                if (alert.classList.contains('alert-success') || alert.classList.contains('alert-info')) {
                    alert.style.transition = 'opacity 0.5s';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 500);
                }
            });
        }, 5000);
        
        // Copy referral code
        function copyReferralCode() {
            const referralCode = document.getElementById('referralCode');
            if (referralCode) {
                referralCode.select();
                referralCode.setSelectionRange(0, 99999);
                document.execCommand('copy');
                
                const button = event.target.closest('button');
                const originalHTML = button.innerHTML;
                button.innerHTML = '<i class="fas fa-check"></i> Copied!';
                button.classList.remove('btn-outline-secondary');
                button.classList.add('btn-success');
                
                setTimeout(() => {
                    button.innerHTML = originalHTML;
                    button.classList.remove('btn-success');
                    button.classList.add('btn-outline-secondary');
                }, 2000);
            }
        }
        
        // Mobile optimization
        if (window.innerWidth <= 768) {
            document.body.classList.add('mobile-optimized');
        }
        
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
        
        // Auto-refresh notifications count (if needed)
        function updateNotificationCount() {
            // This would typically make an AJAX call to get notification count
            // For now, we'll just show a placeholder
        }
        
        // Update notification count every 30 seconds
        setInterval(updateNotificationCount, 30000);
    </script>
</body>
</html>
