<?php
// Universal Ad Card Component - Clean Design
// Usage: include this file and call renderAdCard($ads, $sliderId)
if (!function_exists('renderAdCard')) {
    function renderAdCard($ads, $sliderId) {
        if (empty($ads)) return;
        ?>
        <div class="ad-card-container">
            <div id="<?php echo $sliderId; ?>" class="carousel slide" data-bs-ride="carousel" data-bs-interval="6000">
                <div class="carousel-inner">
                    <?php foreach ($ads as $index => $ad): ?>
                        <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                            <div class="ad-card" onclick="trackAdClick(<?php echo $ad['id']; ?>)">
                                <div class="ad-tag">Advertisement</div>
                                <?php if ($ad['image']): ?>
                                    <div class="ad-image-wrapper">
                                        <img src="<?php echo base_url('public/' . $ad['image']); ?>" 
                                             alt="<?php echo htmlspecialchars($ad['heading']); ?>" 
                                             class="ad-image">
                                    </div>
                                <?php endif; ?>
                                
                                <div class="ad-content">
                                    <h3 class="ad-title"><?php echo htmlspecialchars($ad['heading']); ?></h3>
                                    <p class="ad-description"><?php echo htmlspecialchars($ad['description']); ?></p>
                                    
                                    <div class="ad-actions">
                                        <?php if ($ad['whatsapp_number']): ?>
                                            <button class="action-btn whatsapp-btn" data-action="whatsapp" data-phone="<?php echo $ad['whatsapp_number']; ?>">
                                                <span class="whatsapp-icon">💬</span>
                                                <span>WhatsApp</span>
                                            </button>
                                        <?php endif; ?>
                                        <?php if ($ad['call_number']): ?>
                                            <button class="action-btn call-btn" data-action="call" data-phone="<?php echo $ad['call_number']; ?>">
                                                <i class="fas fa-phone"></i>
                                                <span>Call</span>
                                            </button>
                                        <?php endif; ?>
                                        <?php if ($ad['website_url']): ?>
                                            <a href="<?php echo $ad['website_url']; ?>" class="action-btn visit-btn">
                                                <i class="fas fa-globe"></i>
                                                <span>Visit</span>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <?php if (count($ads) > 1): ?>
                    <button class="carousel-control-prev" type="button" data-bs-target="#<?php echo $sliderId; ?>" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#<?php echo $sliderId; ?>" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }
}
?>
