<?php
// Between News Ad Component - Using Universal Ad Card
if (empty($ad)) return;

// Include the universal ad card component
include __DIR__ . '/ad-card.php';

// Render single ad as array for consistency
$singleAd = [$ad];
?>
<div class="between-news-ad">
    <div class="ad-header">
        <span class="ad-label">Advertisement</span>
    </div>
    <?php renderAdCard($singleAd, 'betweenNewsAd'); ?>
</div>