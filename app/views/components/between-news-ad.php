<?php
// Between News Ad Component
// This component is used to inject ads between news items during infinite scroll
if (empty($ad)) return;
?>

<div class="ad-container between-news-ad">
    <div class="ad-content" onclick="trackAdClick(<?php echo $ad['id']; ?>)">
        <div class="ad-header">
            <span class="ad-label">Advertisement</span>
        </div>
        <?php if ($ad['image']): ?>
            <div class="image-container">
                                <img src="<?php echo base_url('public/' . $ad['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($ad['heading']); ?>" 
                                     class="ad-image">
            </div>
        <?php endif; ?>
        <div class="ad-text">
            <h5><?php echo htmlspecialchars($ad['heading']); ?></h5>
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
