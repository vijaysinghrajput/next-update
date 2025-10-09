<?php
require_once APP_PATH . '/../src/services/Database.php';
require_once APP_PATH . '/../src/helpers/AdHelper.php';

use App\Helpers\AdHelper;

// Auto-activate ads
AdHelper::autoActivateAds();

include APP_PATH . '/views/layouts/website-header.php';
?>

<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">Advertisement Display Demo</h1>
            <p class="text-muted mb-5">This page demonstrates how advertisements will be displayed in different positions on the website.</p>
        </div>
    </div>
    
    <!-- Top Banner Ad -->
    <div class="row mb-5">
        <div class="col-12">
            <h3 class="mb-3">Top Banner Ad</h3>
            <?php echo AdHelper::displayTopBanner(); ?>
        </div>
    </div>
    
    <!-- Sample News Content -->
    <div class="row mb-5">
        <div class="col-12">
            <h3 class="mb-3">News Article</h3>
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Sample News Article</h5>
                    <p class="card-text">This is a sample news article to demonstrate how ads will appear between content. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
                    <p class="card-text">Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Between News Ad -->
    <div class="row mb-5">
        <div class="col-12">
            <h3 class="mb-3">Between News Ad</h3>
            <?php echo AdHelper::displayBetweenNews(); ?>
        </div>
    </div>
    
    <!-- Another News Article -->
    <div class="row mb-5">
        <div class="col-12">
            <h3 class="mb-3">Another News Article</h3>
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Second Sample News Article</h5>
                    <p class="card-text">This is another sample news article to show the layout. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
                    <p class="card-text">Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis.</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bottom Banner Ad -->
    <div class="row mb-5">
        <div class="col-12">
            <h3 class="mb-3">Bottom Banner Ad</h3>
            <?php echo AdHelper::displayBottomBanner(); ?>
        </div>
    </div>
    
    <!-- Popup Modal Ad -->
    <div class="row">
        <div class="col-12">
            <h3 class="mb-3">Popup Modal Ad</h3>
            <p class="text-muted">Popup modal ads appear automatically after 3 seconds when the page loads. Click the button below to trigger a demo popup.</p>
            <button type="button" class="btn btn-primary" onclick="showDemoPopup()">
                Show Demo Popup Ad
            </button>
        </div>
    </div>
    
    <!-- Popup Modal Ad Content -->
    <?php echo AdHelper::displayPopupModal(); ?>
</div>

<style>
.ad-container {
    position: relative;
}

.ad-content {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: 2px dashed #dee2e6;
    transition: all 0.3s ease;
}

.ad-content:hover {
    border-color: #007bff;
    box-shadow: 0 4px 8px rgba(0,123,255,0.1);
}

.ad-heading {
    color: #495057;
    font-weight: 600;
}

.ad-description {
    color: #6c757d;
    font-size: 0.9rem;
}

.ad-label {
    margin-top: 0.5rem;
}

.ad-label small {
    background: #f8f9fa;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.7rem;
}

/* Popup modal styles */
.modal-content {
    border: none;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
}

.modal-header {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    color: white;
    border-radius: 15px 15px 0 0;
}

.modal-header .btn-close {
    filter: invert(1);
}
</style>

<script>
function showDemoPopup() {
    // Create a demo popup ad
    const modalHtml = `
        <div class="modal fade" id="demoModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Demo Advertisement</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center">
                        <img src="https://via.placeholder.com/400x200/007bff/ffffff?text=Demo+Ad+Image" class="img-fluid rounded mb-3" alt="Demo Ad">
                        <p>This is a demo popup advertisement. In real ads, this would contain actual content and links.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <a href="#" class="btn btn-primary">
                            <i class="fas fa-globe me-1"></i>Visit Website
                        </a>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing demo modal
    const existingModal = document.getElementById('demoModal');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Add new modal to body
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('demoModal'));
    modal.show();
    
    // Remove modal from DOM when hidden
    document.getElementById('demoModal').addEventListener('hidden.bs.modal', function() {
        this.remove();
    });
}
</script>

<?php include APP_PATH . '/views/layouts/website-footer.php'; ?>
