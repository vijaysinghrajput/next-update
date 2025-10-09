<?php
// Featured News Component - Mobile App Style
if (empty($featured_news)) return;
?>

<div class="featured-news-section">
    <div class="section-header">
        <h2 class="section-title">Featured Stories</h2>
        <div class="section-subtitle">Top stories you shouldn't miss</div>
    </div>
    
    <div class="featured-news-grid">
        <?php foreach ($featured_news as $index => $news): ?>
            <div class="featured-card" 
                 data-category="<?php echo $news['category_id']; ?>" 
                 data-is-bansgaonsandesh="<?php echo $news['is_bansgaonsandesh']; ?>" 
                 onclick="trackNewsView(<?php echo $news['id']; ?>)">
                
                <?php if ($news['featured_image']): ?>
                    <div class="featured-image-container">
                        <img src="<?php echo base_url('public/' . $news['featured_image']); ?>" 
                             alt="<?php echo htmlspecialchars($news['title']); ?>" 
                             class="featured-image">
                        <div class="featured-badge">
                            <?php if ($news['is_bansgaonsandesh']): ?>
                                <span class="badge-premium">Premium</span>
                            <?php else: ?>
                                <span class="badge-featured">Featured</span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <div class="featured-content">
                    <div class="featured-category">
                        <?php echo htmlspecialchars($news['category_name']); ?>
                    </div>
                    
                    <h3 class="featured-title"><?php echo htmlspecialchars($news['title']); ?></h3>
                    
                    <p class="featured-excerpt">
                        <?php echo htmlspecialchars($news['excerpt'] ?? substr(strip_tags($news['content']), 0, 120) . '...'); ?>
                    </p>
                    
                    <div class="featured-meta">
                        <div class="meta-item">
                            <span class="meta-icon">📍</span>
                            <span><?php echo htmlspecialchars($news['city_name']); ?></span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-icon">👁️</span>
                            <span><?php echo $news['views'] ?? 0; ?> views</span>
                        </div>
                        <div class="meta-time">
                            <?php echo date('M j', strtotime($news['created_at'])); ?>
                        </div>
                    </div>
                </div>
                
            </div>
        <?php endforeach; ?>
    </div>
</div>
