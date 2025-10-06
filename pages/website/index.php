<?php
// Include mobile header
include_once __DIR__ . '/../../app/views/layouts/mobile-header.php';

// Get data
$newsModel = new App\Models\News();
$adModel = new App\Models\Ad();

// Get featured news
$featured_news = $newsModel->getFeatured(5) ?? [];

// Get latest news
$latest_news = $newsModel->getLatest(10) ?? [];

// Get categories
$categories = $newsModel->getDb()->fetchAll("SELECT * FROM categories WHERE is_active = 1 ORDER BY name") ?? [];

// Get cities
$cities = $newsModel->getDb()->fetchAll("SELECT * FROM cities WHERE is_active = 1 ORDER BY name") ?? [];

// Get active ads by position
$top_banner_ads = $adModel->getActiveAds('top_banner');
$bottom_banner_ads = $adModel->getActiveAds('bottom_banner');
$between_news_ads = $adModel->getActiveAds('between_news');
$popup_modal_ads = $adModel->getActiveAds('popup_modal');
?>

<div class="mobile-app-container">
    <!-- Top Banner Ads -->
    <?php include __DIR__ . '/../../app/views/components/top-banner-ads.php'; ?>

    <!-- Filter Tabs -->
    <?php include __DIR__ . '/../../app/views/components/filter-tabs.php'; ?>

    <!-- Featured News -->
    <?php include __DIR__ . '/../../app/views/components/featured-news.php'; ?>

    <!-- Latest News -->
    <?php include __DIR__ . '/../../app/views/components/latest-news.php'; ?>

    <!-- Infinite Scroll Trigger -->
    <div class="mobile-infinite-scroll-trigger">
        <i class="fas fa-spinner fa-spin"></i> Loading more news...
    </div>

    <!-- Bottom Banner Ads -->
    <?php include __DIR__ . '/../../app/views/components/bottom-banner-ads.php'; ?>
</div>

<!-- Popup Modal Ads -->
<?php include __DIR__ . '/../../app/views/components/popup-modal-ads.php'; ?>

<!-- Store between-news ads data for JavaScript -->
<script>
    window.betweenNewsAds = <?php 
        $betweenAdsWithPaths = [];
        foreach ($between_news_ads as $ad) {
            $ad['image'] = $ad['image'] ? base_url('public/' . $ad['image']) : null;
            $betweenAdsWithPaths[] = $ad;
        }
        echo json_encode($betweenAdsWithPaths); 
    ?>;
</script>

<!-- Popup Modal Ads -->
<?php include __DIR__ . '/../../app/views/components/popup-modal-ads.php'; ?>

<!-- Homepage Script -->
<?php include __DIR__ . '/../../app/views/components/homepage-script.php'; ?>

<?php
// Include mobile footer
include_once __DIR__ . '/../../app/views/layouts/mobile-footer.php';
?>