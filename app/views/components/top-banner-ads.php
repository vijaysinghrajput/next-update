<?php
// Top Banner Ads Component - Using Universal Ad Card
if (empty($top_banner_ads)) return;

// Include the universal ad card component
include __DIR__ . '/ad-card.php';

// Render the ad card with top banner styling
?>
<div class="top-banner-ads">
    <?php renderAdCard($top_banner_ads, 'topBannerSlider'); ?>
</div>
