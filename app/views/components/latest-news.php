<?php
// Latest News Component - Mobile App Style
if (empty($latest_news)) return;
?>

<div class="latest-news-section">
    <div class="section-header">
        <h2 class="section-title">Latest Stories</h2>
        <div class="section-subtitle">Stay updated with the latest news</div>
    </div>
    
    <div class="latest-news-grid" id="newsList">
        <?php foreach ($latest_news as $index => $news): ?>
            <div class="latest-card" 
                 data-category="<?php echo $news['category_id']; ?>" 
                 data-is-bansgaonsandesh="<?php echo $news['is_bansgaonsandesh']; ?>"
                 data-news-id="<?php echo $news['id']; ?>"
                 data-news-title="<?php echo htmlspecialchars($news['title']); ?>"
                 data-news-excerpt="<?php echo htmlspecialchars($news['excerpt'] ?? substr(strip_tags($news['content']), 0, 150) . '...'); ?>"
                 data-news-image="<?php echo $news['featured_image'] ? base_url('public/' . $news['featured_image']) : ''; ?>"
                 onclick="trackNewsView(<?php echo $news['id']; ?>, '<?php echo $news['slug'] ?? ''; ?>')">
                
                <?php if ($news['featured_image']): ?>
                    <div class="latest-image-container">
                        <img src="<?php echo base_url('public/' . $news['featured_image']); ?>" 
                             alt="<?php echo htmlspecialchars($news['title']); ?>" 
                             class="latest-image">
                        <div class="latest-badge">
                            <?php if ($news['is_bansgaonsandesh']): ?>
                                <span class="badge-premium">Premium</span>
                            <?php elseif ($news['is_featured']): ?>
                                <span class="badge-featured">Featured</span>
                            <?php else: ?>
                                <span class="badge-latest">Latest</span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <div class="latest-content">
                    <div class="latest-category">
                        <?php echo htmlspecialchars($news['category_name']); ?>
                    </div>
                    
                    <h3 class="latest-title"><?php echo htmlspecialchars($news['title']); ?></h3>
                    
                    <p class="latest-excerpt">
                        <?php echo htmlspecialchars($news['excerpt'] ?? substr(strip_tags($news['content']), 0, 120) . '...'); ?>
                    </p>
                    
                    <div class="latest-meta">
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
                
                <!-- Share Button -->
                <div class="news-share-actions">
                    <button class="share-news-btn" onclick="event.stopPropagation(); shareNewsFromCard(<?php echo $news['id']; ?>)">
                        <i class="fas fa-share-alt"></i>
                    </button>
                </div>
                
            </div>
        <?php endforeach; ?>
    </div>
</div>
