<?php
require_once __DIR__ . '/../../bootstrap.php';

use App\Models\News;

// Check if user is logged in and is admin
if (!session('user_id') || !session('is_admin')) {
    redirect('/login');
}

$newsModel = new News();

// Handle AJAX requests FIRST - before any HTML output
if (isset($_GET['action']) || (isset($_POST['action']))) {
    header('Content-Type: application/json');
    
    $action = $_GET['action'] ?? $_POST['action'] ?? '';
    
    switch ($action) {
        case 'get':
            $id = (int)$_GET['id'];
            try {
                $category = $newsModel->getDb()->fetch("SELECT * FROM categories WHERE id = ?", [$id]);
                echo json_encode(['success' => true, 'data' => $category]);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            exit;
            
        case 'update':
            $id = (int)$_POST['id'];
            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            
            if (empty($name)) {
                echo json_encode(['success' => false, 'message' => 'Category name is required.']);
                exit;
            }
            
            try {
                $newsModel->getDb()->query("UPDATE categories SET name = ?, description = ?, updated_at = NOW() WHERE id = ?", 
                    [$name, $description, $id]);
                echo json_encode(['success' => true, 'message' => 'Category updated successfully!']);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Error updating category: ' . $e->getMessage()]);
            }
            exit;
            
        case 'delete':
            $id = (int)$_POST['id'];
            try {
                // Check if category is being used by any news articles
                $usage = $newsModel->getDb()->fetch("SELECT COUNT(*) as count FROM news_articles WHERE category_id = ?", [$id]);
                if ($usage['count'] > 0) {
                    echo json_encode(['success' => false, 'message' => 'Cannot delete category. It is being used by ' . $usage['count'] . ' news article(s).']);
                    exit;
                }
                
                $newsModel->getDb()->query("DELETE FROM categories WHERE id = ?", [$id]);
                echo json_encode(['success' => true, 'message' => 'Category deleted successfully!']);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Error deleting category: ' . $e->getMessage()]);
            }
            exit;
    }
}

// Only include HTML if not an AJAX request
if (!isset($_GET['action']) && !isset($_POST['action'])) {
    $page_title = "Manage Categories";
    include APP_PATH . '/views/layouts/admin-header.php';

    // Handle form submission for adding new category
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    
        if (!empty($name)) {
            try {
                $newsModel->getDb()->query("INSERT INTO categories (name, description, created_at) VALUES (?, ?, NOW())", 
                    [$name, $description]);
                $success = "Category added successfully!";
            } catch (Exception $e) {
                $error = "Error adding category: " . $e->getMessage();
            }
        } else {
            $error = "Category name is required.";
        }
    }

    // Get all categories with error handling
    try {
        $categories = $newsModel->getDb()->fetchAll("SELECT * FROM categories ORDER BY name ASC") ?? [];
    } catch (Exception $e) {
        $categories = [];
    }
    ?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-tags me-2"></i>Manage Categories</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
        <i class="fas fa-plus me-2"></i>Add Category
    </button>
</div>

<!-- Success/Error Messages -->
<?php if (isset($success)): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (isset($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Categories Table -->
<div class="dashboard-card">
    <h5 class="mb-3">
        <i class="fas fa-list me-2"></i>All Categories
    </h5>
    
    <?php if (empty($categories)): ?>
        <div class="text-center py-4">
            <i class="fas fa-tags fa-3x text-muted mb-3"></i>
            <p class="text-muted">No categories found.</p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $category): ?>
                        <tr>
                            <td><?php echo $category['id']; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($category['name']); ?></strong>
                            </td>
                            <td><?php echo htmlspecialchars($category['description']); ?></td>
                            <td><?php echo date('M j, Y', strtotime($category['created_at'])); ?></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" onclick="editCategory(<?php echo $category['id']; ?>)">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteCategory(<?php echo $category['id']; ?>)">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus me-2"></i>Add New Category
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Category Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Add Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-edit me-2"></i>Edit Category
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editCategoryForm">
                <div class="modal-body">
                    <input type="hidden" id="edit_id" name="id">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Category Name</label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Description</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Update Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editCategory(id) {
    // Fetch category data
    fetch('?action=get&id=' + id)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.text().then(text => {
                try {
                    return JSON.parse(text);
                } catch (e) {
                    console.error('Invalid JSON response:', text);
                    throw new Error('Invalid response from server');
                }
            });
        })
        .then(data => {
            if (data.success) {
                document.getElementById('edit_id').value = data.data.id;
                document.getElementById('edit_name').value = data.data.name;
                document.getElementById('edit_description').value = data.data.description || '';
                
                // Show modal
                const modal = new bootstrap.Modal(document.getElementById('editCategoryModal'));
                modal.show();
            } else {
                showMessage('Error: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMessage('Error fetching category data', 'error');
        });
}

function deleteCategory(id) {
    if (confirm('Are you sure you want to delete this category?')) {
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('id', id);
        
        fetch('', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.text().then(text => {
                try {
                    return JSON.parse(text);
                } catch (e) {
                    console.error('Invalid JSON response:', text);
                    throw new Error('Invalid response from server');
                }
            });
        })
        .then(data => {
            if (data.success) {
                showMessage(data.message, 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showMessage('Error: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMessage('Error deleting category', 'error');
        });
    }
}

// Handle edit form submission
document.getElementById('editCategoryForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    formData.append('action', 'update');
    
    fetch('', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.text().then(text => {
            try {
                return JSON.parse(text);
            } catch (e) {
                console.error('Invalid JSON response:', text);
                throw new Error('Invalid response from server');
            }
        });
    })
    .then(data => {
        if (data.success) {
            showMessage(data.message, 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showMessage('Error: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('Error updating category', 'error');
    });
});

// Function to show messages
function showMessage(message, type) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const icon = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle';
    
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            <i class="${icon} me-2"></i>${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    // Remove existing alerts
    const existingAlerts = document.querySelectorAll('.alert');
    existingAlerts.forEach(alert => alert.remove());
    
    // Try to find container, fallback to body if not found
    let container = document.querySelector('.container-fluid') || 
                   document.querySelector('.container') || 
                   document.querySelector('main') || 
                   document.body;
    
    if (container) {
        container.insertAdjacentHTML('afterbegin', alertHtml);
    } else {
        // Fallback: create alert in body
        document.body.insertAdjacentHTML('afterbegin', alertHtml);
    }
    
    // Auto-hide after 5 seconds
    setTimeout(() => {
        const alert = document.querySelector('.alert');
        if (alert) {
            alert.remove();
        }
    }, 5000);
}
</script>

<?php include APP_PATH . '/views/layouts/admin-footer.php'; ?>

<?php } // End of HTML section ?>