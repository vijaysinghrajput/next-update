<?php
// Top Banner Ads Component
if (empty($top_banner_ads)) return;
?>

<div class="mobile-card ad-container top-banner-ad">
    <div id="topBannerSlider" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000">
        <?php if (count($top_banner_ads) > 1): ?>
            <div class="carousel-indicators">
                <?php foreach ($top_banner_ads as $index => $ad): ?>
                    <button type="button" data-bs-target="#topBannerSlider" data-bs-slide-to="<?php echo $index; ?>" 
                            <?php echo $index === 0 ? 'class="active" aria-current="true"' : ''; ?> 
                            aria-label="Slide <?php echo $index + 1; ?>"></button>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <div class="carousel-inner">
            <?php foreach ($top_banner_ads as $index => $ad): ?>
                <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                    <div class="ad-content" onclick="trackAdClick(<?php echo $ad['id']; ?>)">
                        <?php if ($ad['image']): ?>
                            <div class="image-container">
                                <img src="<?php echo base_url('public/' . $ad['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($ad['heading']); ?>" 
                                     class="ad-image">
                            </div>
                        <?php endif; ?>
                        <div class="ad-text">
                            <h4><?php echo htmlspecialchars($ad['heading']); ?></h4>
                            <p><?php echo htmlspecialchars($ad['description']); ?></p>
                        </div>
                        <div class="ad-actions">
                            <?php if ($ad['whatsapp_number']): ?>
                                <button class="mobile-btn mobile-btn-success" data-action="whatsapp" data-phone="<?php echo $ad['whatsapp_number']; ?>">
                                    <i class="fab fa-whatsapp"></i> WhatsApp
                                </button>
                            <?php endif; ?>
                            <?php if ($ad['call_number']): ?>
                                <button class="mobile-btn mobile-btn-primary" data-action="call" data-phone="<?php echo $ad['call_number']; ?>">
                                    <i class="fas fa-phone"></i> Call
                                </button>
                            <?php endif; ?>
                            <?php if ($ad['website_url']): ?>
                                <a href="<?php echo $ad['website_url']; ?>" class="mobile-btn mobile-btn-secondary">
                                    <i class="fas fa-globe"></i> Visit
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <?php if (count($top_banner_ads) > 1): ?>
            <button class="carousel-control-prev" type="button" data-bs-target="#topBannerSlider" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#topBannerSlider" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        <?php endif; ?>
    </div>
</div>
