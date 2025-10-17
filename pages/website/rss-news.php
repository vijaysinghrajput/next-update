<?php
// Include mobile header
include_once __DIR__ . '/../../app/views/layouts/mobile-header.php';

use App\Services\RSSService;

$rssService = new RSSService();
$categories = $rssService->getCategories();
$currentCategory = $_GET['category'] ?? 'top-stories';
$currentLanguage = $_GET['language'] ?? 'hindi'; // Default to Hindi

// Fetch RSS news for current category and language
$newsData = $rssService->fetchRSSFeed($currentCategory, 20, $currentLanguage);
?>

<style>
.rss-nav {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 1rem 0;
    border-radius: 0 0 25px 25px;
    margin-bottom: 1rem;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.category-pills {
    display: flex;
    overflow-x: auto;
    padding: 0 1rem;
    gap: 0.5rem;
    scrollbar-width: none;
    -ms-overflow-style: none;
}

.category-pills::-webkit-scrollbar {
    display: none;
}

.category-pill {
    background: rgba(255,255,255,0.2);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 25px;
    text-decoration: none;
    white-space: nowrap;
    font-size: 0.9rem;
    font-weight: 500;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.1);
}

.category-pill.active {
    background: rgba(255,255,255,0.9);
    color: #667eea;
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}

.rss-article {
    background: white;
    border-radius: 15px;
    margin-bottom: 1rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    overflow: hidden;
    transition: transform 0.3s ease;
}

.rss-article:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
}

.rss-content {
    padding: 1rem;
}

.rss-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #333;
    margin-bottom: 0.5rem;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.rss-description {
    color: #666;
    font-size: 0.9rem;
    line-height: 1.5;
    margin-bottom: 0.75rem;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.rss-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 0.75rem;
    border-top: 1px solid #f0f0f0;
    font-size: 0.8rem;
}

.rss-source {
    color: #667eea;
    font-weight: 500;
}

.rss-date {
    color: #999;
}

.read-more-btn {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 25px;
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 500;
    display: inline-block;
    margin-top: 0.5rem;
    transition: all 0.3s ease;
}

.read-more-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(102, 126, 234, 0.3);
    color: white;
    text-decoration: none;
}

.loading-spinner {
    text-align: center;
    padding: 2rem;
}

.spinner {
    border: 3px solid #f3f3f3;
    border-top: 3px solid #667eea;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    animation: spin 1s linear infinite;
    margin: 0 auto;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

@keyframes skeleton-loading {
    0% { opacity: 1; }
    50% { opacity: 0.4; }
    100% { opacity: 1; }
}

/* Enhanced Loading Skeleton Styles */
.loading-skeleton {
    padding: 1rem;
}

.skeleton-card {
    background: white;
    border-radius: 15px;
    margin-bottom: 1rem;
    padding: 0;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.skeleton-image {
    width: 100%;
    height: 200px;
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: skeleton-loading 1.5s infinite;
}

.skeleton-content {
    padding: 1rem;
}

.skeleton-line {
    height: 16px;
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: skeleton-loading 1.5s infinite;
    border-radius: 8px;
    margin-bottom: 0.75rem;
}

.skeleton-title {
    height: 20px;
    width: 90%;
}

.skeleton-text {
    height: 14px;
    width: 70%;
}

.skeleton-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 1rem;
    padding-top: 0.75rem;
    border-top: 1px solid #f0f0f0;
}

.skeleton-source {
    height: 12px;
    width: 80px;
}

.skeleton-time {
    height: 12px;
    width: 60px;
}

/* Error and Empty States */
.error-state, .empty-state {
    text-align: center;
    padding: 3rem 1rem;
    background: white;
    border-radius: 15px;
    margin: 1rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.error-icon, .empty-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
}

.error-icon {
    color: #dc3545;
}

.empty-icon {
    color: #6c757d;
}

.error-state h3, .empty-state h3 {
    color: #333;
    margin-bottom: 0.5rem;
}

.error-state p, .empty-state p {
    color: #666;
    margin-bottom: 1.5rem;
}

/* Smooth loading spinner */
.modern-spinner {
    display: inline-block;
    width: 24px;
    height: 24px;
    border: 3px solid rgba(102, 126, 234, 0.3);
    border-radius: 50%;
    border-top-color: #667eea;
    animation: spin 1s ease-in-out infinite;
}

.error-message {
    background: #fee;
    color: #c53030;
    padding: 1rem;
    border-radius: 10px;
    margin: 1rem;
    text-align: center;
}

.rss-header {
    background: white;
    padding: 1rem;
    border-radius: 15px;
    margin-bottom: 1rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.rss-header h1 {
    margin: 0;
    color: #333;
    font-size: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.rss-icon {
    color: #ff6b35;
    font-size: 1.8rem;
}

/* Language Filter Styles */
.language-filter {
    background: white;
    padding: 1rem;
    border-radius: 15px;
    margin-bottom: 1rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.filter-pills {
    display: flex;
    gap: 0.5rem;
    justify-content: center;
}

.filter-pill {
    background: #f8f9fa;
    color: #666;
    padding: 0.75rem 1.5rem;
    border-radius: 25px;
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 500;
    transition: all 0.3s ease;
    border: 2px solid transparent;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.filter-pill:hover {
    background: #e9ecef;
    text-decoration: none;
    color: #495057;
}

.filter-pill.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-color: rgba(255,255,255,0.2);
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

/* Language Badge Styles */
.language-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 15px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    margin-bottom: 0.5rem;
}

.language-badge.hindi {
    background: linear-gradient(135deg, #ff9a56 0%, #ff6b35 100%);
    color: white;
}

.language-badge.english {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    color: white;
}

/* Improved article styles */
.rss-article {
    background: white;
    border-radius: 15px;
    margin-bottom: 1rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    overflow: hidden;
    transition: transform 0.3s ease;
    border-left: 4px solid transparent;
    padding: 1.5rem;
}

.rss-article.hindi {
    border-left-color: #ff6b35;
}

.rss-article.english {
    border-left-color: #4facfe;
}

.rss-article:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 25px rgba(0,0,0,0.15);
}

.rss-content {
    width: 100%;
}

.rss-title {
    color: #333;
    font-size: 1.25rem;
    font-weight: 600;
    line-height: 1.4;
    margin-bottom: 0.75rem;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-decoration: none;
}

.rss-description {
    color: #666;
    font-size: 0.95rem;
    line-height: 1.5;
    margin-bottom: 1rem;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.rss-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: #888;
    font-size: 0.85rem;
}

.rss-source {
    font-weight: 500;
    color: #007bff;
}

.rss-date {
    opacity: 0.8;
}

.language-badge {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    background: #f8f9fa;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 500;
    margin-bottom: 0.5rem;
}

.language-badge.hindi {
    background: linear-gradient(135deg, #ff6b35, #f7931e);
    color: white;
}

.language-badge.english {
    background: linear-gradient(135deg, #4facfe, #00f2fe);
    color: white;
}
</style>

<div class="mobile-app-container">
    <!-- RSS Header -->
    <div class="rss-header">
        <h1>
            <i class="fas fa-rss rss-icon"></i>
            RSS News Feed
        </h1>
        <p class="mb-0 text-muted">Latest news from Google News RSS feeds</p>
    </div>

    <!-- Category Navigation -->
    <div class="rss-nav">
        <div class="category-pills">
            <?php foreach ($categories as $key => $category): ?>
                <a href="?category=<?= $key ?>&language=<?= $currentLanguage ?>" 
                   class="category-pill <?= $currentCategory === $key ? 'active' : '' ?>">
                    <?= htmlspecialchars($category['name']) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Language Filter -->
    <div class="language-filter">
        <div class="filter-pills">
            <a href="?category=<?= $currentCategory ?>&language=hindi" 
               class="filter-pill <?= $currentLanguage === 'hindi' ? 'active' : '' ?>">
                <i class="fas fa-language"></i> हिंदी
            </a>
            <a href="?category=<?= $currentCategory ?>&language=english" 
               class="filter-pill <?= $currentLanguage === 'english' ? 'active' : '' ?>">
                <i class="fas fa-globe-americas"></i> English
            </a>
            <a href="?category=<?= $currentCategory ?>&language=both" 
               class="filter-pill <?= $currentLanguage === 'both' ? 'active' : '' ?>">
                <i class="fas fa-globe"></i> Both
            </a>
        </div>
    </div>

    <!-- RSS News Content -->
    <div id="rss-content">
        <?php if ($newsData['success']): ?>
            <?php foreach ($newsData['articles'] as $article): ?>
                <div class="rss-article <?= isset($article['language']) ? $article['language'] : '' ?>">
                    <div class="rss-content">
                        <?php if (isset($article['language'])): ?>
                            <div class="language-badge <?= $article['language'] ?>">
                                <?= $article['language'] === 'hindi' ? 'हिंदी' : 'English' ?>
                            </div>
                        <?php endif; ?>
                        
                        <h3 class="rss-title"><?= htmlspecialchars($article['title']) ?></h3>
                        
                        <?php if ($article['description']): ?>
                            <p class="rss-description"><?= htmlspecialchars($article['description']) ?></p>
                        <?php endif; ?>
                        
                        <a href="<?= htmlspecialchars($article['link']) ?>" 
                           target="_blank" 
                           rel="noopener noreferrer" 
                           class="read-more-btn"
                           onclick="trackNewsClick('<?= htmlspecialchars(addslashes($article['title'])) ?>', 'rss'); return true;">
                            Read Full Article <i class="fas fa-external-link-alt"></i>
                        </a>
                        
                        <div class="rss-meta">
                            <span class="rss-source">
                                <i class="fas fa-newspaper"></i> <?= htmlspecialchars($article['source']) ?>
                            </span>
                            <span class="rss-date">
                                <i class="fas fa-clock"></i> <?= htmlspecialchars($article['pub_date']) ?>
                            </span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <?php if (empty($newsData['articles'])): ?>
                <div class="error-message">
                    <i class="fas fa-info-circle"></i>
                    No news articles found for this category.
                </div>
            <?php endif; ?>
            
        <?php else: ?>
            <div class="error-message">
                <i class="fas fa-exclamation-triangle"></i>
                Error loading RSS feed: <?= htmlspecialchars($newsData['error'] ?? 'Unknown error') ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Add smooth scrolling for category pills
document.addEventListener('DOMContentLoaded', function() {
    const categoryPills = document.querySelector('.category-pills');
    const activePill = document.querySelector('.category-pill.active');
    
    if (activePill && categoryPills) {
        // Scroll active pill into view
        const pillRect = activePill.getBoundingClientRect();
        const containerRect = categoryPills.getBoundingClientRect();
        
        if (pillRect.left < containerRect.left || pillRect.right > containerRect.right) {
            activePill.scrollIntoView({ 
                behavior: 'smooth', 
                block: 'nearest', 
                inline: 'center' 
            });
        }
    }
    
    // Add loading state for category and language changes
    const categoryLinks = document.querySelectorAll('.category-pill, .filter-pill');
    categoryLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            if (!this.classList.contains('active')) {
                // Show enhanced loading skeleton
                showLoadingSkeleton();
                
                // Add loading indicator to clicked element
                const originalText = this.innerHTML;
                this.innerHTML = `<div class="modern-spinner"></div> Loading...`;
                this.style.pointerEvents = 'none';
                
                // Restore after a short delay (let the page load)
                setTimeout(() => {
                    this.innerHTML = originalText;
                    this.style.pointerEvents = 'auto';
                }, 1500);
            }
        });
    });

    // Enhanced loading skeleton function
    function showLoadingSkeleton() {
        const container = document.getElementById('rss-content');
        if (container) {
            container.innerHTML = `
                <div class="loading-skeleton">
                    ${Array(6).fill(0).map(() => `
                        <div class="skeleton-card">
                            <div class="skeleton-image"></div>
                            <div class="skeleton-content">
                                <div class="skeleton-line skeleton-title"></div>
                                <div class="skeleton-line skeleton-title" style="width: 75%;"></div>
                                <div class="skeleton-line skeleton-text"></div>
                                <div class="skeleton-line skeleton-text" style="width: 85%;"></div>
                                <div class="skeleton-line skeleton-text" style="width: 60%;"></div>
                                <div class="skeleton-meta">
                                    <div class="skeleton-line skeleton-source"></div>
                                    <div class="skeleton-line skeleton-time"></div>
                                </div>
                            </div>
                        </div>
                    `).join('')}
                </div>
            `;
        }
    }

    // Show error state
    function showError(message) {
        const container = document.getElementById('rss-content');
        if (container) {
            container.innerHTML = `
                <div class="error-state">
                    <div class="error-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h3>Something went wrong</h3>
                    <p>${message}</p>
                    <button class="btn btn-primary" onclick="location.reload()">
                        <i class="fas fa-redo me-2"></i>Try Again
                    </button>
                </div>
            `;
        }
    }

    // Show empty state
    function showEmpty() {
        const container = document.getElementById('rss-content');
        if (container) {
            container.innerHTML = `
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-newspaper"></i>
                    </div>
                    <h3>No news found</h3>
                    <p>Try selecting a different category or language.</p>
                    <button class="btn btn-outline-primary" onclick="location.href='?category=top-stories&language=hindi'">
                        <i class="fas fa-home me-2"></i>Back to Top Stories
                    </button>
                </div>
            `;
        }
    }

    // Add pull-to-refresh for mobile
    let startY = 0;
    let pullDistance = 0;
    const pullThreshold = 100;
    
    document.addEventListener('touchstart', function(e) {
        if (window.scrollY === 0) {
            startY = e.touches[0].clientY;
        }
    });
    
    document.addEventListener('touchmove', function(e) {
        if (window.scrollY === 0 && startY) {
            pullDistance = e.touches[0].clientY - startY;
            if (pullDistance > 0 && pullDistance < pullThreshold * 2) {
                e.preventDefault();
                const header = document.querySelector('.mobile-app-header');
                if (header) {
                    header.style.transform = `translateY(${Math.min(pullDistance / 2, 50)}px)`;
                }
            }
        }
    });
    
    document.addEventListener('touchend', function(e) {
        if (pullDistance > pullThreshold) {
            showToast('Refreshing news...', 'info');
            setTimeout(() => location.reload(), 500);
        }
        
        // Reset
        const header = document.querySelector('.mobile-app-header');
        if (header) {
            header.style.transform = 'translateY(0)';
        }
        startY = 0;
        pullDistance = 0;
    });
    
    // Handle external link clicks for mobile app
    const externalLinks = document.querySelectorAll('a[target="_blank"]');
    externalLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // For mobile app integration
            if (window.Android) {
                e.preventDefault();
                window.Android.openExternalUrl(this.href);
            } else if (window.webkit && window.webkit.messageHandlers) {
                e.preventDefault();
                window.webkit.messageHandlers.openExternal.postMessage(this.href);
            }
            // Otherwise, let default behavior happen (open in new tab)
        });
    });
});

// Auto-refresh feed every 5 minutes
setInterval(function() {
    // Only refresh if user is on the page (not in background)
    if (!document.hidden) {
        console.log('Auto-refreshing RSS feed...');
        location.reload();
    }
}, 300000); // 5 minutes

// Track news clicks for analytics
function trackNewsClick(title, type) {
    // Send tracking data to API
    fetch('/api/track-news-view', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            title: title,
            type: type || 'rss',
            timestamp: new Date().toISOString()
        })
    }).catch(console.error);
}

// Native app integration helpers
function showToast(message, type = 'info') {
    // Create toast notification
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.textContent = message;
    toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 1rem 1.5rem;
        background: ${type === 'success' ? '#28a745' : '#17a2b8'};
        color: white;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        z-index: 9999;
        font-weight: 500;
        transform: translateX(100%);
        transition: transform 0.3s ease;
    `;
    
    document.body.appendChild(toast);
    
    // Animate in
    setTimeout(() => toast.style.transform = 'translateX(0)', 100);
    
    // Remove after 3 seconds
    setTimeout(() => {
        toast.style.transform = 'translateX(100%)';
        setTimeout(() => document.body.removeChild(toast), 300);
    }, 3000);
}

// Load more articles on scroll (infinite scroll)
let isLoading = false;
window.addEventListener('scroll', function() {
    if (isLoading) return;
    
    if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 1000) {
        // Load more articles
        // This could be implemented with AJAX in the future
    }
});
</script>
</div>

<?php
// Include mobile footer
include_once __DIR__ . '/../../app/views/layouts/mobile-footer.php';
?>