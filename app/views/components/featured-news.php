<?php
// Featured News Component
if (empty($featured_news)) return;
?>

<div class="mobile-card">
    <div class="card-header">
        <h5 class="card-title">
            <i class="fas fa-star"></i>
            Featured News
        </h5>
    </div>
    <div class="news-list">
        <?php foreach ($featured_news as $index => $news): ?>
            <div class="mobile-list-item mobile-touch-feedback" 
                 data-category="<?php echo $news['category_id']; ?>" 
                 data-is-bansgaonsandesh="<?php echo $news['is_bansgaonsandesh']; ?>" 
                 onclick="trackNewsView(<?php echo $news['id']; ?>)">
                <div class="item-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <?php if ($news['is_bansgaonsandesh']): ?>
                        <i class="fas fa-crown"></i>
                    <?php elseif ($news['is_featured']): ?>
                        <i class="fas fa-star"></i>
                    <?php else: ?>
                        <i class="fas fa-newspaper"></i>
                    <?php endif; ?>
                </div>
                <div class="item-content">
                    <?php if ($news['featured_image']): ?>
                        <div class="image-container">
                                <img src="<?php echo base_url('public/' . $news['featured_image']); ?>" 
                                     alt="<?php echo htmlspecialchars($news['title']); ?>" 
                                     class="news-image">
                        </div>
                    <?php endif; ?>
                    <div class="item-title"><?php echo htmlspecialchars($news['title']); ?></div>
                    <div class="item-subtitle">
                        <?php echo htmlspecialchars($news['excerpt'] ?? substr(strip_tags($news['content']), 0, 80) . '...'); ?>
                    </div>
                    <div class="news-meta">
                        <small class="text-muted">
                            <i class="fas fa-tag"></i> <?php echo htmlspecialchars($news['category_name']); ?> • 
                            <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($news['city_name']); ?> • 
                            <i class="fas fa-eye"></i> <?php echo $news['views'] ?? 0; ?> views
                        </small>
                    </div>
                </div>
                <div class="item-action">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
