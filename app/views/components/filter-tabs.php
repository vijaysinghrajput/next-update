<?php
// Category Slider Component - Mobile App Style
if (empty($categories)) return;
?>

<div class="category-slider-container">
    <div class="category-slider" id="categorySlider">
        <div class="category-item active" data-filter="all">
            <div class="category-icon">
                <i class="fas fa-home"></i>
            </div>
            <span class="category-name">All</span>
        </div>
        
        <div class="category-item" data-filter="featured">
            <div class="category-icon">
                <i class="fas fa-star"></i>
            </div>
            <span class="category-name">Featured</span>
        </div>
        
        <div class="category-item" data-filter="bansgaonsandesh">
            <div class="category-icon">
                <i class="fas fa-crown"></i>
            </div>
            <span class="category-name"><?php echo config('admin_channel_name'); ?></span>
        </div>
        
        <?php foreach ($categories as $category): ?>
            <div class="category-item" data-filter="category-<?php echo $category['id']; ?>">
                <div class="category-icon">
                    <i class="fas fa-tag"></i>
                </div>
                <span class="category-name"><?php echo htmlspecialchars($category['name']); ?></span>
            </div>
        <?php endforeach; ?>
    </div>
</div>
