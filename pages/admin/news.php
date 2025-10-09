<?php
require_once __DIR__ . '/../../bootstrap-fixed.php';

use App\Models\User;
use App\Models\News;

// Check if user is logged in and is admin
if (!session('user_id') || !session('is_admin')) {
    redirect('/login');
}

$page_title = "Manage News";
include APP_PATH . '/views/layouts/admin-header.php';

$userModel = new User();
$newsModel = new News();

// Handle form submissions
if ($_POST) {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create_news') {
        $title = $_POST['title'] ?? '';
        $content = $_POST['content'] ?? '';
        $category_id = $_POST['category_id'] ?? '';
        $city_id = $_POST['city_id'] ?? '';
        $is_featured = isset($_POST['is_featured']) ? 1 : 0;
        
        if (!empty($title) && !empty($content)) {
            $newsData = [
                'title' => $title,
                'content' => $content,
                'excerpt' => substr(strip_tags($content), 0, 200),
                'category_id' => $category_id,
                'city_id' => $city_id,
                'user_id' => session('user_id'),
                'is_featured' => $is_featured,
                'is_bansgaonsandesh' => 1, // Admin news
                'is_active' => 1
            ];
            
            $newsId = $newsModel->create($newsData);
            if ($newsId) {
                $success = "News article created successfully!";
            } else {
                $errors[] = "Failed to create news article.";
            }
        } else {
            $errors[] = "Title and content are required.";
        }
    } elseif ($action === 'toggle_status') {
        $newsId = (int)($_POST['news_id'] ?? 0);
        $isActive = $_POST['is_active'] === 'true' ? 1 : 0;
        
        if ($newsId > 0) {
            try {
                $newsModel->db->query("UPDATE news_articles SET is_active = ? WHERE id = ?", [$isActive, $newsId]);
                $success = "News article status updated successfully!";
            } catch (Exception $e) {
                $errors[] = "Failed to update news status: " . $e->getMessage();
            }
        }
    } elseif ($action === 'delete_news') {
        $newsId = (int)($_POST['news_id'] ?? 0);
        
        if ($newsId > 0) {
            try {
                $newsModel->db->query("UPDATE news_articles SET is_active = 0 WHERE id = ?", [$newsId]);
                $success = "News article deleted successfully!";
            } catch (Exception $e) {
                $errors[] = "Failed to delete news article: " . $e->getMessage();
            }
        }
    }
    
    // Redirect to prevent resubmission
    if (isset($success)) {
        redirect('/admin/news?success=' . urlencode($success));
    }
}

// Get success message from URL
if (isset($_GET['success'])) {
    $success = $_GET['success'];
}

// Get all news
$allNews = $newsModel->getDb()->fetchAll("
    SELECT n.*, u.full_name as author_name, c.name as category_name, ci.name as city_name
    FROM news_articles n
    LEFT JOIN users u ON n.user_id = u.id
    LEFT JOIN categories c ON n.category_id = c.id
    LEFT JOIN cities ci ON n.city_id = ci.id
    ORDER BY n.created_at DESC
");

// Get categories and cities
$categories = $newsModel->getCategories();
$cities = $newsModel->getCities();
?>

<div class="container my-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-newspaper me-2"></i>Manage News</h2>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createNewsModal">
                    <i class="fas fa-plus me-2"></i>Create News
                </button>
            </div>
            
            <?php if (isset($success)): ?>
                <div class="alert alert-success alert-persistent">
                    <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stats-card">
                        <i class="fas fa-newspaper text-primary"></i>
                        <h3><?php echo count($allNews); ?></h3>
                        <p class="text-muted">Total Articles</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stats-card">
                        <i class="fas fa-check-circle text-success"></i>
                        <h3><?php echo count(array_filter($allNews, fn($n) => $n['is_active'])); ?></h3>
                        <p class="text-muted">Active Articles</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stats-card">
                        <i class="fas fa-star text-warning"></i>
                        <h3><?php echo count(array_filter($allNews, fn($n) => $n['is_featured'])); ?></h3>
                        <p class="text-muted">Featured Articles</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stats-card">
                        <i class="fas fa-crown text-info"></i>
                        <h3><?php echo count(array_filter($allNews, fn($n) => $n['is_bansgaonsandesh'])); ?></h3>
                        <p class="text-muted"><?php echo config('admin_channel_name'); ?> Articles</p>
                    </div>
                </div>
            </div>
            
            <!-- News List -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i>All News Articles
                    </h5>
                    <span class="badge bg-info"><?php echo count($allNews); ?> total articles</span>
                </div>
                
                <!-- DataTables Info -->
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Advanced Table Features:</strong> Use the search box below to filter news articles instantly. Click column headers to sort. The table is fully responsive and mobile-friendly.
                </div>
                
                <div class="table-responsive">
                    <table id="newsTable" class="table table-hover table-striped display">
                        <thead class="table-dark">
                            <tr>
                                <th class="text-center">ID</th>
                                <th>Article Details</th>
                                <th class="text-center">Author</th>
                                <th class="text-center">Category</th>
                                <th class="text-center">City</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Created</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($allNews as $news): ?>
                            <tr>
                                <td class="text-center">
                                    <span class="badge bg-secondary">#<?php echo $news['id']; ?></span>
                                </td>
                                <td>
                                    <div>
                                        <div class="fw-bold"><?php echo htmlspecialchars($news['title']); ?></div>
                                        <div class="small text-muted"><?php echo htmlspecialchars(substr(strip_tags($news['content']), 0, 100)) . '...'; ?></div>
                                        <div class="mt-1">
                                            <?php if ($news['is_featured']): ?>
                                                <span class="badge bg-warning me-1">Featured</span>
                                            <?php endif; ?>
                                            <?php if ($news['is_bansgaonsandesh']): ?>
                                                <span class="badge bg-info"><?php echo config('admin_channel_name'); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="small">
                                        <div class="fw-bold"><?php echo htmlspecialchars($news['author_name']); ?></div>
                                        <div class="text-muted">User #<?php echo $news['user_id']; ?></div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary"><?php echo htmlspecialchars($news['category_name']); ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary"><?php echo htmlspecialchars($news['city_name']); ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-<?php echo $news['is_active'] ? 'success' : 'danger'; ?>">
                                        <?php echo $news['is_active'] ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <small><?php echo date('M j, Y H:i', strtotime($news['created_at'])); ?></small>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="<?php echo base_url('admin/news/' . $news['id'] . '/edit'); ?>" 
                                           class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?php echo base_url('news/' . $news['id']); ?>" 
                                           class="btn btn-sm btn-outline-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button class="btn btn-sm btn-outline-<?php echo $news['is_active'] ? 'warning' : 'success'; ?>" 
                                                onclick="toggleNewsStatus(<?php echo $news['id']; ?>, <?php echo $news['is_active'] ? 'false' : 'true'; ?>)" 
                                                title="<?php echo $news['is_active'] ? 'Deactivate' : 'Activate'; ?>">
                                            <i class="fas fa-<?php echo $news['is_active'] ? 'ban' : 'check'; ?>"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" 
                                                onclick="deleteNews(<?php echo $news['id']; ?>)" 
                                                title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create News Modal -->
<div class="modal fade" id="createNewsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create News Article</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="create_news">
                    
                    <div class="mb-3">
                        <label for="title" class="form-label">Title *</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="category_id" class="form-label">Category</label>
                                <select class="form-select" id="category_id" name="category_id">
                                    <option value="">Select Category</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category['id']; ?>">
                                            <?php echo htmlspecialchars($category['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="city_id" class="form-label">City</label>
                                <select class="form-select" id="city_id" name="city_id">
                                    <option value="">Select City</option>
                                    <?php foreach ($cities as $city): ?>
                                        <option value="<?php echo $city['id']; ?>">
                                            <?php echo htmlspecialchars($city['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="content" class="form-label">Content *</label>
                        <textarea class="form-control" id="content" name="content" rows="8" required></textarea>
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="is_featured" name="is_featured">
                        <label class="form-check-label" for="is_featured">
                            Featured Article
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create News</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include APP_PATH . '/views/layouts/admin-footer.php'; ?>

<script>
$(document).ready(function() {
    console.log('=== News DataTables Debug ===');
    console.log('jQuery loaded:', typeof $ !== 'undefined');
    console.log('jQuery version:', $.fn.jquery);
    console.log('DataTable function:', typeof $.fn.DataTable);
    console.log('Table element:', $('#newsTable').length);
    
    // Initialize DataTables for news
    if ($('#newsTable').length && typeof $.fn.DataTable !== 'undefined') {
        console.log('Initializing news DataTable...');
        $('#newsTable').DataTable({
            pageLength: 10,
            lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
            order: [[6, 'desc']], // Sort by creation date
            columnDefs: [
                {
                    targets: [0, 2, 3, 4, 5, 6, 7],
                    className: 'text-center'
                },
                {
                    targets: [7],
                    orderable: false,
                    searchable: false
                }
            ],
            language: {
                search: "Search news articles:",
                lengthMenu: "Show _MENU_ articles per page",
                info: "Showing _START_ to _END_ of _TOTAL_ articles",
                infoEmpty: "No articles available",
                infoFiltered: "(filtered from _MAX_ total articles)",
                emptyTable: "No news articles found"
            }
        });
        console.log('News DataTable initialized successfully!');
    } else {
        console.error('Failed to initialize news DataTable');
        console.error('Table found:', $('#newsTable').length);
        console.error('DataTable function available:', typeof $.fn.DataTable);
    }
});

// Toggle news status
function toggleNewsStatus(newsId, newStatus) {
    if (confirm('Are you sure you want to ' + (newStatus ? 'activate' : 'deactivate') + ' this news article?')) {
        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="toggle_status">
            <input type="hidden" name="news_id" value="${newsId}">
            <input type="hidden" name="is_active" value="${newStatus}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

// Delete news article
function deleteNews(newsId) {
    if (confirm('Are you sure you want to delete this news article? This action cannot be undone.')) {
        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="delete_news">
            <input type="hidden" name="news_id" value="${newsId}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
