<?php
// Bottom Banner Ads Component - Using Universal Ad Card
if (empty($bottom_banner_ads)) return;

// Include the universal ad card component
include __DIR__ . '/ad-card.php';

// Render the ad card with bottom banner styling
?>
<div class="bottom-banner-ads">
    <?php renderAdCard($bottom_banner_ads, 'bottomBannerSlider'); ?>
</div>