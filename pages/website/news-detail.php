<?php
// News Detail Page - Mobile App Optimized
require_once __DIR__ . '/../../bootstrap-fixed.php';

use App\Models\News;
use App\Models\Ad;

// Get news ID/slug from URL parameter (Router provides this as $slug variable)
$newsSlug = $slug ?? $_GET['slug'] ?? '';
if (empty($newsSlug)) {
    header('Location: ' . base_url());
    exit;
}

$newsModel = new News();
$adModel = new Ad();

// Try to get news by slug first, then by ID
$news = $newsModel->getBySlug($newsSlug);
if (!$news) {
    // Try by ID if slug doesn't work
    $news = $newsModel->getById((int)$newsSlug);
}

if (!$news) {
    header('HTTP/1.0 404 Not Found');
    include __DIR__ . '/../errors/404.php';
    exit;
}

// Increment views
$newsModel->incrementViews($news['id']);

// Get related news
$related_news = $newsModel->getRelated($news['id'], $news['category_id'], 5);

// Get ads
$top_banner_ads = $adModel->getActiveAds('top_banner');
$bottom_banner_ads = $adModel->getActiveAds('bottom_banner');

// Set page data
$page_title = htmlspecialchars($news['title']);
$show_back_button = true;
$show_search = false;

// Include mobile header
include_once __DIR__ . '/../../app/views/layouts/mobile-header.php';
?>

<div class="mobile-app-container">
    <!-- Top Banner Ads -->
    <?php if (!empty($top_banner_ads)): ?>
        <div class="ad-banner-container top-banner">
            <?php foreach ($top_banner_ads as $ad): ?>
                <div class="banner-ad" onclick="trackAdClick(<?php echo $ad['id']; ?>)">
                    <?php if ($ad['image_url']): ?>
                        <img src="<?php echo base_url('public/' . $ad['image_url']); ?>" 
                             alt="<?php echo htmlspecialchars($ad['title']); ?>" 
                             class="ad-banner-image">
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- News Detail Header -->
    <div class="news-detail-header">
        <div class="news-header-nav">
            <button class="nav-back-btn" onclick="history.back()">
                <i class="fas fa-arrow-left"></i>
            </button>
            <div class="nav-actions">
                <button class="nav-action-btn" onclick="shareNews()">
                    <i class="fas fa-share-alt"></i>
                </button>
                <button class="nav-action-btn" onclick="bookmarkNews()">
                    <i class="far fa-bookmark"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- News Content -->
    <article class="news-detail-content">
        <!-- Category Badge -->
        <div class="news-detail-badges">
            <span class="category-badge" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <?php echo htmlspecialchars($news['category_name'] ?? 'News'); ?>
            </span>
            <?php if ($news['is_featured']): ?>
                <span class="featured-badge">
                    <i class="fas fa-star"></i> Featured
                </span>
            <?php endif; ?>
            <?php if ($news['is_bansgaonsandesh']): ?>
                <span class="official-badge">
                    <i class="fas fa-check-circle"></i> <?php echo config('admin_channel_name'); ?>
                </span>
            <?php endif; ?>
        </div>

        <!-- News Title -->
        <h1 class="news-detail-title"><?php echo htmlspecialchars($news['title']); ?></h1>

        <!-- News Meta -->
        <div class="news-detail-meta">
            <div class="meta-item">
                <i class="fas fa-user meta-icon"></i>
                <span><?php echo htmlspecialchars($news['author_name'] ?? 'Admin'); ?></span>
            </div>
            <div class="meta-item">
                <i class="fas fa-clock meta-icon"></i>
                <span><?php echo date('M d, Y \a\t h:i A', strtotime($news['created_at'])); ?></span>
            </div>
            <div class="meta-item">
                <i class="fas fa-map-marker-alt meta-icon"></i>
                <span><?php echo htmlspecialchars($news['city_name'] ?? 'Local'); ?></span>
            </div>
            <div class="meta-item">
                <i class="fas fa-eye meta-icon"></i>
                <span><?php echo number_format($news['views'] + 1); ?> views</span>
            </div>
        </div>

        <!-- Featured Image -->
        <?php if ($news['featured_image']): ?>
            <div class="news-detail-image-container">
                <img src="<?php echo base_url('public/' . $news['featured_image']); ?>" 
                     alt="<?php echo htmlspecialchars($news['title']); ?>" 
                     class="news-detail-image"
                     onclick="openImageViewer(this)">
                <div class="image-overlay">
                    <button class="image-expand-btn">
                        <i class="fas fa-expand-alt"></i>
                    </button>
                </div>
            </div>
        <?php endif; ?>

        <!-- News Content -->
        <div class="news-detail-body">
            <?php if ($news['excerpt']): ?>
                <div class="news-excerpt">
                    <?php echo nl2br(htmlspecialchars($news['excerpt'])); ?>
                </div>
            <?php endif; ?>
            
            <div class="news-content">
                <?php echo $news['content']; ?>
            </div>
        </div>

        <!-- Social Actions -->
        <div class="news-detail-actions">
            <button class="action-btn share-btn" onclick="shareNews()">
                <i class="fas fa-share-alt"></i>
                <span>Share</span>
            </button>
            <button class="action-btn like-btn" onclick="likeNews(<?php echo $news['id']; ?>)">
                <i class="far fa-heart"></i>
                <span>Like</span>
            </button>
            <button class="action-btn bookmark-btn" onclick="bookmarkNews(<?php echo $news['id']; ?>)">
                <i class="far fa-bookmark"></i>
                <span>Save</span>
            </button>
            <button class="action-btn comment-btn" onclick="openComments()">
                <i class="far fa-comment"></i>
                <span>Comment</span>
            </button>
        </div>

        <!-- Tags -->
        <?php if ($news['tags']): ?>
            <div class="news-tags">
                <div class="tags-title">Tags:</div>
                <div class="tags-list">
                    <?php 
                    $tags = explode(',', $news['tags']);
                    foreach ($tags as $tag): 
                        $tag = trim($tag);
                        if ($tag):
                    ?>
                        <span class="news-tag">#<?php echo htmlspecialchars($tag); ?></span>
                    <?php 
                        endif;
                    endforeach; 
                    ?>
                </div>
            </div>
        <?php endif; ?>
    </article>

    <!-- Related News Section -->
    <?php if (!empty($related_news)): ?>
        <section class="related-news-section">
            <div class="section-header">
                <h2 class="section-title">Related Stories</h2>
                <div class="section-subtitle">More news you might like</div>
            </div>
            
            <div class="related-news-grid">
                <?php foreach ($related_news as $related): ?>
                    <div class="related-news-card" onclick="window.location.href='<?php echo base_url('news/' . ($related['slug'] ?? $related['id'])); ?>'">
                        <?php if ($related['featured_image']): ?>
                            <div class="related-image-container">
                                <img src="<?php echo base_url('public/' . $related['featured_image']); ?>" 
                                     alt="<?php echo htmlspecialchars($related['title']); ?>" 
                                     class="related-image">
                            </div>
                        <?php endif; ?>
                        <div class="related-content">
                            <div class="related-category">
                                <?php echo htmlspecialchars($related['category_name'] ?? 'News'); ?>
                            </div>
                            <h3 class="related-title"><?php echo htmlspecialchars($related['title']); ?></h3>
                            <div class="related-meta">
                                <span class="related-time">
                                    <i class="fas fa-clock"></i>
                                    <?php echo date('M d', strtotime($related['created_at'])); ?>
                                </span>
                                <span class="related-views">
                                    <i class="fas fa-eye"></i>
                                    <?php echo number_format($related['views']); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

    <!-- Bottom Banner Ads -->
    <?php if (!empty($bottom_banner_ads)): ?>
        <div class="ad-banner-container bottom-banner">
            <?php foreach ($bottom_banner_ads as $ad): ?>
                <div class="banner-ad" onclick="trackAdClick(<?php echo $ad['id']; ?>)">
                    <?php if ($ad['image_url']): ?>
                        <img src="<?php echo base_url('public/' . $ad['image_url']); ?>" 
                             alt="<?php echo htmlspecialchars($ad['title']); ?>" 
                             class="ad-banner-image">
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Back to Top Button -->
    <button class="back-to-top-btn" onclick="scrollToTop()">
        <i class="fas fa-chevron-up"></i>
    </button>
</div>

<!-- Image Viewer Modal -->
<div class="image-viewer-modal" id="imageViewerModal">
    <div class="image-viewer-content">
        <button class="image-viewer-close" onclick="closeImageViewer()">
            <i class="fas fa-times"></i>
        </button>
        <img src="" alt="" class="image-viewer-img" id="imageViewerImg">
    </div>
</div>

<!-- Comments Modal -->
<div class="comments-modal" id="commentsModal">
    <div class="comments-content">
        <div class="comments-header">
            <h3>Comments</h3>
            <button class="comments-close" onclick="closeComments()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="comments-body">
            <div class="comment-form">
                <textarea placeholder="Write a comment..." rows="3"></textarea>
                <button class="submit-comment-btn">Post Comment</button>
            </div>
            <div class="comments-list">
                <div class="no-comments">
                    <i class="fas fa-comment"></i>
                    <p>Be the first to comment!</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* News Detail Page Styles */
.news-detail-header {
    position: sticky;
    top: 0;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-bottom: 1px solid #eee;
    z-index: 100;
    padding: env(safe-area-inset-top) 0 0 0;
}

.news-header-nav {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 16px;
}

.nav-back-btn {
    background: #f8f9fa;
    border: none;
    border-radius: 12px;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    color: #333;
    transition: all 0.3s ease;
}

.nav-back-btn:hover {
    background: #e9ecef;
    transform: scale(1.05);
}

.nav-actions {
    display: flex;
    gap: 8px;
}

.nav-action-btn {
    background: #f8f9fa;
    border: none;
    border-radius: 12px;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    color: #333;
    transition: all 0.3s ease;
}

.nav-action-btn:hover {
    background: #e9ecef;
    transform: scale(1.05);
}

.news-detail-content {
    padding: 0 16px 20px;
    background: white;
}

.news-detail-badges {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 16px;
}

.category-badge, .featured-badge, .official-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    color: white;
}

.featured-badge {
    background: linear-gradient(135deg, #ffd700 0%, #ffb347 100%);
    color: #333;
}

.official-badge {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
}

.news-detail-title {
    font-size: 1.5rem;
    font-weight: 700;
    line-height: 1.3;
    color: #1a1a1a;
    margin-bottom: 16px;
}

.news-detail-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
    margin-bottom: 20px;
    padding-bottom: 16px;
    border-bottom: 1px solid #eee;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.85rem;
    color: #6c757d;
}

.meta-icon {
    font-size: 0.9rem;
    color: #667eea;
}

.news-detail-image-container {
    position: relative;
    margin-bottom: 24px;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}

.news-detail-image {
    width: 100%;
    height: auto;
    max-height: 300px;
    object-fit: cover;
    cursor: pointer;
    transition: transform 0.3s ease;
}

.news-detail-image:hover {
    transform: scale(1.02);
}

.image-overlay {
    position: absolute;
    top: 12px;
    right: 12px;
}

.image-expand-btn {
    background: rgba(0,0,0,0.6);
    border: none;
    border-radius: 8px;
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.9rem;
}

.news-detail-body {
    margin-bottom: 24px;
}

.news-excerpt {
    font-size: 1.1rem;
    color: #495057;
    line-height: 1.6;
    margin-bottom: 20px;
    padding: 16px;
    background: #f8f9fa;
    border-radius: 12px;
    border-left: 4px solid #667eea;
}

.news-content {
    font-size: 1rem;
    line-height: 1.7;
    color: #333;
}

.news-content p {
    margin-bottom: 16px;
}

.news-content img {
    max-width: 100%;
    height: auto;
    border-radius: 12px;
    margin: 16px 0;
}

.news-detail-actions {
    display: flex;
    justify-content: space-around;
    padding: 20px 0;
    border-top: 1px solid #eee;
    border-bottom: 1px solid #eee;
    margin-bottom: 20px;
}

.action-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    background: none;
    border: none;
    padding: 12px;
    border-radius: 12px;
    transition: all 0.3s ease;
    color: #6c757d;
    min-width: 60px;
}

.action-btn:hover {
    background: #f8f9fa;
    color: #667eea;
    transform: translateY(-2px);
}

.action-btn i {
    font-size: 1.2rem;
}

.action-btn span {
    font-size: 0.8rem;
    font-weight: 500;
}

.news-tags {
    margin-bottom: 24px;
}

.tags-title {
    font-size: 0.9rem;
    font-weight: 600;
    color: #495057;
    margin-bottom: 8px;
}

.tags-list {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.news-tag {
    background: #e9ecef;
    color: #495057;
    padding: 4px 10px;
    border-radius: 16px;
    font-size: 0.8rem;
    font-weight: 500;
}

.related-news-section {
    background: #f8f9fa;
    padding: 24px 16px;
    margin: 0 -16px;
}

.related-news-grid {
    display: grid;
    gap: 16px;
}

.related-news-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    display: flex;
    cursor: pointer;
    transition: all 0.3s ease;
}

.related-news-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
}

.related-image-container {
    width: 100px;
    height: 80px;
    flex-shrink: 0;
}

.related-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.related-content {
    padding: 12px;
    flex: 1;
}

.related-category {
    font-size: 0.7rem;
    color: #667eea;
    font-weight: 600;
    text-transform: uppercase;
    margin-bottom: 4px;
}

.related-title {
    font-size: 0.9rem;
    font-weight: 600;
    line-height: 1.3;
    color: #333;
    margin-bottom: 8px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.related-meta {
    display: flex;
    gap: 12px;
    font-size: 0.75rem;
    color: #6c757d;
}

.related-time, .related-views {
    display: flex;
    align-items: center;
    gap: 4px;
}

.back-to-top-btn {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 50%;
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    box-shadow: 0 4px 20px rgba(102, 126, 234, 0.3);
    transition: all 0.3s ease;
    z-index: 100;
    opacity: 0;
    visibility: hidden;
}

.back-to-top-btn.visible {
    opacity: 1;
    visibility: visible;
}

.back-to-top-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 25px rgba(102, 126, 234, 0.4);
}

/* Image Viewer Modal */
.image-viewer-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.9);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}

.image-viewer-modal.active {
    opacity: 1;
    visibility: visible;
}

.image-viewer-content {
    position: relative;
    max-width: 90%;
    max-height: 90%;
}

.image-viewer-close {
    position: absolute;
    top: -50px;
    right: 0;
    background: rgba(255,255,255,0.2);
    color: white;
    border: none;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
}

.image-viewer-img {
    max-width: 100%;
    max-height: 100%;
    border-radius: 8px;
}

/* Comments Modal */
.comments-modal {
    position: fixed;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 70%;
    background: white;
    border-radius: 20px 20px 0 0;
    z-index: 1000;
    transform: translateY(100%);
    transition: transform 0.3s ease;
}

.comments-modal.active {
    transform: translateY(0);
}

.comments-content {
    height: 100%;
    display: flex;
    flex-direction: column;
}

.comments-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px;
    border-bottom: 1px solid #eee;
}

.comments-header h3 {
    margin: 0;
    font-size: 1.2rem;
    font-weight: 600;
}

.comments-close {
    background: none;
    border: none;
    font-size: 1.2rem;
    color: #666;
    padding: 8px;
}

.comments-body {
    flex: 1;
    display: flex;
    flex-direction: column;
    padding: 20px;
}

.comment-form {
    margin-bottom: 20px;
}

.comment-form textarea {
    width: 100%;
    border: 1px solid #ddd;
    border-radius: 12px;
    padding: 12px;
    font-size: 0.9rem;
    resize: none;
    margin-bottom: 12px;
}

.submit-comment-btn {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 8px;
    padding: 10px 20px;
    font-size: 0.9rem;
    font-weight: 600;
}

.comments-list {
    flex: 1;
    overflow-y: auto;
}

.no-comments {
    text-align: center;
    padding: 40px 20px;
    color: #6c757d;
}

.no-comments i {
    font-size: 2rem;
    margin-bottom: 12px;
    display: block;
}

/* Mobile Responsiveness */
@media (max-width: 480px) {
    .news-detail-title {
        font-size: 1.3rem;
    }
    
    .news-detail-meta {
        gap: 12px;
    }
    
    .meta-item {
        font-size: 0.8rem;
    }
    
    .related-news-card {
        flex-direction: column;
    }
    
    .related-image-container {
        width: 100%;
        height: 120px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize mobile app features
    if (window.MobileApp) {
        window.MobileApp.init();
    }
    
    // Back to top button
    const backToTopBtn = document.querySelector('.back-to-top-btn');
    window.addEventListener('scroll', function() {
        if (window.scrollY > 300) {
            backToTopBtn.classList.add('visible');
        } else {
            backToTopBtn.classList.remove('visible');
        }
    });
});

// News interaction functions
function shareNews() {
    const newsTitle = <?php echo json_encode($news['title']); ?>;
    const newsDescription = <?php echo json_encode($news['excerpt'] ?? substr(strip_tags($news['content']), 0, 150) . '...'); ?>;
    const newsImage = <?php echo json_encode($news['featured_image'] ? base_url('public/' . $news['featured_image']) : ''); ?>;
    const playStoreLink = 'https://play.google.com/store/apps/details?id=com.skyably.nextupdate';
    
    const shareText = `📰 ${newsTitle}\n\n${newsDescription}\n\n📱 Download Next Update App:\n${playStoreLink}`;
    
    if (navigator.share) {
        const shareData = {
            title: newsTitle,
            text: shareText,
            url: playStoreLink
        };
        
        // Add image if available
        if (newsImage) {
            shareData.files = [newsImage];
        }
        
        navigator.share(shareData);
    } else {
        // Fallback - copy to clipboard
        navigator.clipboard.writeText(shareText).then(() => {
            showToast('News details copied to clipboard!', 'success');
        });
    }
}

function likeNews(newsId) {
    // Implement like functionality
    showToast('News liked!', 'success');
}

function bookmarkNews(newsId) {
    // Implement bookmark functionality
    showToast('News bookmarked!', 'success');
}

function openComments() {
    document.getElementById('commentsModal').classList.add('active');
}

function closeComments() {
    document.getElementById('commentsModal').classList.remove('active');
}

function openImageViewer(img) {
    const modal = document.getElementById('imageViewerModal');
    const modalImg = document.getElementById('imageViewerImg');
    modalImg.src = img.src;
    modalImg.alt = img.alt;
    modal.classList.add('active');
}

function closeImageViewer() {
    document.getElementById('imageViewerModal').classList.remove('active');
}

function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

function trackAdClick(adId) {
    // Track ad clicks
    fetch('<?php echo base_url('api/track-ad'); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'ad_id=' + adId
    });
}

// Toast notification function
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `
        <div class="toast-content">
            <i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'times' : 'info-circle'}"></i>
            <span>${message}</span>
        </div>
    `;
    
    // Add toast styles
    toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'success' ? '#28a745' : type === 'error' ? '#dc3545' : '#17a2b8'};
        color: white;
        padding: 12px 16px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        z-index: 1000;
        font-size: 0.9rem;
        font-weight: 500;
        transform: translateX(100%);
        transition: transform 0.3s ease;
    `;
    
    document.body.appendChild(toast);
    
    // Animate in
    setTimeout(() => {
        toast.style.transform = 'translateX(0)';
    }, 100);
    
    // Remove after 3 seconds
    setTimeout(() => {
        toast.style.transform = 'translateX(100%)';
        setTimeout(() => {
            document.body.removeChild(toast);
        }, 300);
    }, 3000);
}
</script>

<?php
// Include mobile footer
include_once __DIR__ . '/../../app/views/layouts/mobile-footer.php';
?>