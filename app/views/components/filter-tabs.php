<?php
// Filter Tabs Component
if (empty($categories)) return;
?>

<div class="mobile-card">
    <div class="card-header">
        <h5 class="card-title">
            <i class="fas fa-filter"></i>
            Filter News
        </h5>
    </div>
    <div class="filter-tabs">
        <div class="tabs-container">
            <button class="mobile-btn mobile-btn-primary mobile-btn-full" data-filter="all">
                <i class="fas fa-home"></i>
                All News
            </button>
            <button class="mobile-btn mobile-btn-secondary mobile-btn-full" data-filter="featured">
                <i class="fas fa-star"></i>
                Featured
            </button>
            <button class="mobile-btn mobile-btn-secondary mobile-btn-full" data-filter="bansgaonsandesh">
                <i class="fas fa-crown"></i>
                <?php echo config('admin_channel_name'); ?>
            </button>
            <?php foreach (array_slice($categories, 0, 4) as $category): ?>
                <button class="mobile-btn mobile-btn-secondary mobile-btn-full" data-filter="category-<?php echo $category['id']; ?>">
                    <i class="fas fa-tag"></i>
                    <?php echo htmlspecialchars($category['name']); ?>
                </button>
            <?php endforeach; ?>
        </div>
    </div>
</div>
