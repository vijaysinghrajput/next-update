<?php
require_once __DIR__ . '/../../bootstrap.php';

use App\Models\User;
use App\Models\News;

// Check if user is logged in
if (!session('user_id')) {
    redirect('/login');
}

// Redirect admin users to admin dashboard
if (session('is_admin')) {
    redirect('/admin');
}

$page_title = "My News";
include APP_PATH . '/views/layouts/user-header.php';

$userModel = new User();
$newsModel = new News();
$userId = session('user_id');

// Check for delete success message
$deleteSuccess = '';
$deleteError = '';
if (isset($_GET['deleted']) && $_GET['deleted'] == '1') {
    $message = $_GET['message'] ?? '';
    $deleteSuccess = 'News article deleted successfully! ' . $message;
}
if (isset($_GET['error'])) {
    $deleteError = $_GET['error'];
}

// Get user's news
$userNews = $newsModel->getByUser($userId, 20);

// Get user stats
$userStats = $userModel->getStats($userId);
?>

<!-- My News Overview -->
<div class="dashboard-card mb-4">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h2 class="mb-2">
                <i class="fas fa-newspaper me-2"></i>My News Articles
            </h2>
            <p class="mb-0 text-muted">Manage your published news articles and track their performance.</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="<?php echo base_url('post-news'); ?>" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Post New Article
            </a>
        </div>
    </div>
</div>

<?php if ($deleteSuccess): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i><?php echo $deleteSuccess; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if ($deleteError): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($deleteError); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- News Stats -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="stats-card">
            <i class="fas fa-newspaper text-primary"></i>
            <h4><?php echo count($userNews); ?></h4>
            <p>Total Articles</p>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="stats-card">
            <i class="fas fa-eye text-info"></i>
            <h4><?php echo array_sum(array_column($userNews, 'views')); ?></h4>
            <p>Total Views</p>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="stats-card">
            <i class="fas fa-check-circle text-success"></i>
            <h4><?php echo count(array_filter($userNews, function($news) { return $news['is_published']; })); ?></h4>
            <p>Published</p>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="stats-card">
            <i class="fas fa-clock text-warning"></i>
            <h4><?php echo count(array_filter($userNews, function($news) { return !$news['is_published']; })); ?></h4>
            <p>Pending</p>
        </div>
    </div>
</div>

<!-- News Articles -->
<div class="dashboard-card">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">
            <i class="fas fa-list me-2"></i>Your Articles
        </h5>
        <div class="btn-group" role="group">
            <input type="radio" class="btn-check" name="statusFilter" id="all" autocomplete="off" checked onchange="filterNews('all')">
            <label class="btn btn-outline-secondary" for="all">All</label>
            
            <input type="radio" class="btn-check" name="statusFilter" id="published" autocomplete="off" onchange="filterNews('published')">
            <label class="btn btn-outline-success" for="published">Published</label>
            
            <input type="radio" class="btn-check" name="statusFilter" id="pending" autocomplete="off" onchange="filterNews('pending')">
            <label class="btn btn-outline-warning" for="pending">Pending</label>
        </div>
    </div>
    
    <?php if (empty($userNews)): ?>
        <div class="text-center py-5">
            <i class="fas fa-newspaper fa-3x text-muted mb-3"></i>
            <h6 class="text-muted">No articles yet</h6>
            <p class="text-muted">Start sharing your local news with the community!</p>
            <a href="<?php echo base_url('post-news'); ?>" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Post Your First Article
            </a>
        </div>
    <?php else: ?>
        <div class="row" id="newsGrid">
            <?php foreach ($userNews as $news): ?>
                <div class="col-lg-6 mb-4 news-item" data-status="<?php echo $news['is_published'] ? 'published' : 'pending'; ?>">
                    <div class="card h-100">
                        <?php if ($news['featured_image']): ?>
                            <img src="<?php echo base_url($news['featured_image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($news['title']); ?>" style="height: 200px; object-fit: cover;">
                        <?php else: ?>
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                <i class="fas fa-image fa-3x text-muted"></i>
                            </div>
                        <?php endif; ?>
                        
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="card-title mb-0"><?php echo htmlspecialchars($news['title']); ?></h6>
                                <span class="badge bg-<?php echo $news['is_published'] ? 'success' : 'warning'; ?>">
                                    <?php echo $news['is_published'] ? 'Published' : 'Pending'; ?>
                                </span>
                            </div>
                            
                            <p class="card-text text-muted small flex-grow-1">
                                <?php echo htmlspecialchars(substr($news['excerpt'] ?: $news['content'], 0, 100)) . '...'; ?>
                            </p>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="fas fa-eye me-1"></i><?php echo $news['views']; ?> views
                                    <span class="ms-2">
                                        <i class="fas fa-calendar me-1"></i><?php echo date('M j, Y', strtotime($news['created_at'])); ?>
                                    </span>
                                </small>
                                
                                <div class="btn-group btn-group-sm">
                                    <a href="<?php echo base_url('news/' . $news['slug']); ?>" class="btn btn-outline-primary" target="_blank">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                    <a href="<?php echo base_url('post-news/' . $news['id'] . '/edit'); ?>" class="btn btn-outline-secondary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-outline-danger" onclick="deleteNews(<?php echo $news['id']; ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Quick Actions -->
<div class="dashboard-card">
    <h5 class="mb-3">
        <i class="fas fa-bolt me-2"></i>Quick Actions
    </h5>
    
    <div class="row">
        <div class="col-md-4 mb-2">
            <a href="<?php echo base_url('post-news'); ?>" class="btn btn-primary w-100">
                <i class="fas fa-plus me-2"></i>Post New Article
            </a>
        </div>
        <div class="col-md-4 mb-2">
            <a href="<?php echo base_url('dashboard'); ?>" class="btn btn-outline-secondary w-100">
                <i class="fas fa-tachometer-alt me-2"></i>Back to Dashboard
            </a>
        </div>
        <div class="col-md-4 mb-2">
            <a href="<?php echo base_url('news'); ?>" class="btn btn-outline-info w-100" target="_blank">
                <i class="fas fa-external-link-alt me-2"></i>View All News
            </a>
        </div>
    </div>
</div>

<script>
function filterNews(status) {
    const newsItems = document.querySelectorAll('.news-item');
    
    newsItems.forEach(item => {
        if (status === 'all' || item.dataset.status === status) {
            item.style.display = '';
        } else {
            item.style.display = 'none';
        }
    });
}

function deleteNews(newsId) {
    // Create a beautiful confirmation modal
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.id = 'deleteModal';
    modal.innerHTML = `
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle me-2"></i>Delete Article
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <i class="fas fa-trash-alt fa-3x text-danger mb-3"></i>
                        <h6>Are you sure you want to delete this article?</h6>
                        <p class="text-muted mb-0">This action cannot be undone and you will lose the points earned from this article.</p>
                    </div>
                    <div class="alert alert-warning">
                        <i class="fas fa-coins me-2"></i>
                        <strong>Note:</strong> You will lose the 10 points you earned for posting this article.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancel
                    </button>
                    <button type="button" class="btn btn-danger" onclick="confirmDelete(${newsId})">
                        <i class="fas fa-trash me-2"></i>Yes, Delete Article
                    </button>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    const bootstrapModal = new bootstrap.Modal(modal);
    bootstrapModal.show();
    
    // Remove modal from DOM when hidden
    modal.addEventListener('hidden.bs.modal', function() {
        document.body.removeChild(modal);
    });
}

function confirmDelete(newsId) {
    // Create a form to submit the delete request
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '<?php echo base_url('post-news'); ?>/' + newsId + '/delete';
    
    const deleteInput = document.createElement('input');
    deleteInput.type = 'hidden';
    deleteInput.name = 'delete';
    deleteInput.value = '1';
    
    form.appendChild(deleteInput);
    document.body.appendChild(form);
    form.submit();
}

// Auto-refresh stats every 30 seconds
setInterval(function() {
    // This would typically make an AJAX call to refresh the stats
    // For now, we'll just show a placeholder
}, 30000);
</script>

<?php include APP_PATH . '/views/layouts/user-footer.php'; ?>
