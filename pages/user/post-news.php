<?php
require_once __DIR__ . '/../../bootstrap-fixed.php';

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

$page_title = "Post News";
include APP_PATH . '/views/layouts/user-header.php';

$userModel = new User();
$newsModel = new News();
$userId = session('user_id');

$errors = [];
$success = '';
$news = null;

// Check if editing existing news
$editId = $id ?? $_GET['id'] ?? null;

if ($editId) {
    $news = $newsModel->getById($editId);
    if (!$news || $news['user_id'] != $userId) {
        redirect('/my-news');
    }
}

// Get categories and cities
$categories = $newsModel->getCategories();
$cities = $newsModel->getCities();

// Handle form submission
if ($_POST) {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $category_id = $_POST['category_id'] ?? '';
    $city_id = $_POST['city_id'] ?? '';
    $external_link = trim($_POST['external_link'] ?? '');
    $action = $_POST['action'] ?? '';
    
    // Validation
    if (empty($title)) $errors[] = "Title is required.";
    if (empty($content)) $errors[] = "Content is required.";
    if (strlen($content) < 50) $errors[] = "Content must be at least 50 characters.";
    if (strlen($content) > 2000) $errors[] = "Content must be maximum 2000 characters.";
    if (empty($category_id)) $errors[] = "Category is required.";
    if (empty($city_id)) $errors[] = "City is required.";
    
    // Validate external link if provided
    if (!empty($external_link) && !filter_var($external_link, FILTER_VALIDATE_URL)) {
        $errors[] = "Please enter a valid URL for external link.";
    }
    
    // Handle image upload
    $featured_image = null;
    if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../../public/uploads/news/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $fileExtension = strtolower(pathinfo($_FILES['featured_image']['name'], PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (!in_array($fileExtension, $allowedExtensions)) {
            $errors[] = "Invalid file type. Only JPG, PNG, GIF, and WebP images are allowed.";
        } elseif ($_FILES['featured_image']['size'] > 5 * 1024 * 1024) { // 5MB limit
            $errors[] = "Image file is too large. Maximum size is 5MB.";
        } else {
            $fileName = uniqid() . '_' . time() . '.' . $fileExtension;
            $filePath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['featured_image']['tmp_name'], $filePath)) {
                $featured_image = 'uploads/news/' . $fileName;
            } else {
                $errors[] = "Failed to upload image. Please try again.";
            }
        }
    }
    
    if (empty($errors)) {
        try {
            $newsData = [
                'title' => $title,
                'content' => $content,
                'category_id' => $category_id,
                'city_id' => $city_id,
                'external_link' => $external_link,
                'user_id' => $userId,
                'is_published' => 1 // Auto-publish user news
            ];
            
            // Add featured image if uploaded
            if ($featured_image) {
                $newsData['featured_image'] = $featured_image;
            }
            
            if ($editId) {
                // Update existing news
                $newsModel->update($editId, $newsData);
                $success = "News article updated successfully!";
            } else {
                // Create new news
                $newsId = $newsModel->create($newsData);
                
                // Award points for posting news
                $pointsEarned = config('news_post_points', 10);
                $userModel->addPoints($userId, $pointsEarned);
                $userModel->recordTransaction($userId, 'earned', $pointsEarned, 'News post reward', 'news', $newsId);
                
                $success = "News article posted successfully! You earned {$pointsEarned} points!";
            }
            
            // Redirect to my-news page after successful submission
            if ($success) {
                echo '<script>setTimeout(function() { window.location.href = "' . base_url('my-news') . '"; }, 2000);</script>';
            }
            
        } catch (Exception $e) {
            $errors[] = "Failed to save news: " . $e->getMessage();
        }
    }
}

// Handle delete action
if (isset($_POST['delete']) && $editId) {
    try {
        // Delete the news article
        $newsModel->delete($editId);
        
        // Redirect to my-news with success message
        redirect('/my-news?deleted=1&message=News article deleted successfully!');
    } catch (Exception $e) {
        $errors[] = "Failed to delete news: " . $e->getMessage();
    }
}
?>

<!-- Post News Form -->
<div class="dashboard-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="fas fa-<?php echo $editId ? 'edit' : 'plus-circle'; ?> me-2"></i>
            <?php echo $editId ? 'Edit News Article' : 'Post New Article'; ?>
        </h2>
        <a href="<?php echo base_url('my-news'); ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to My News
        </a>
    </div>
    
    <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 12px; border: none; box-shadow: 0 4px 12px rgba(40, 167, 69, 0.15);">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <i class="fas fa-check-circle" style="font-size: 24px; color: #28a745;"></i>
                </div>
                <div>
                    <h6 class="mb-1" style="color: #155724; font-weight: 600;">Success!</h6>
                    <p class="mb-0" style="color: #155724;"><?php echo $success; ?></p>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 12px; border: none; box-shadow: 0 4px 12px rgba(220, 53, 69, 0.15);">
            <div class="d-flex align-items-start">
                <div class="me-3">
                    <i class="fas fa-exclamation-triangle" style="font-size: 24px; color: #dc3545;"></i>
                </div>
                <div>
                    <h6 class="mb-2" style="color: #721c24; font-weight: 600;">Please fix the following issues:</h6>
                    <ul class="mb-0" style="color: #721c24;">
                        <?php foreach ($errors as $error): ?>
                            <li class="mb-1"><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <form method="POST" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-8">
                <div class="mb-3">
                    <label for="title" class="form-label">Article Title *</label>
                    <input type="text" class="form-control" id="title" name="title" 
                           value="<?php echo htmlspecialchars($news['title'] ?? ''); ?>" 
                           placeholder="Enter a compelling title for your news article" required>
                    <div class="form-text">Make it catchy and descriptive</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label for="featured_image" class="form-label">Featured Image</label>
                    <input type="file" class="form-control" id="featured_image" name="featured_image" accept="image/*" onchange="previewImage(this)">
                    <div class="form-text">Optional: Upload an image (JPG, PNG, GIF, WebP - Max 5MB)</div>
                    
                    <!-- Image Preview -->
                    <div id="imagePreview" class="mt-2" style="display: none;">
                        <img id="previewImg" src="" alt="Preview" class="img-thumbnail" style="max-width: 200px; max-height: 150px;">
                        <div class="mt-1">
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeImage()">
                                <i class="fas fa-times me-1"></i>Remove
                            </button>
                        </div>
                    </div>
                    
                    <!-- Current Image (for editing) -->
                    <?php if ($editId && $news['featured_image']): ?>
                        <div class="mt-2">
                            <label class="form-label">Current Image:</label>
                            <img src="<?php echo base_url($news['featured_image']); ?>" alt="Current" class="img-thumbnail" style="max-width: 200px; max-height: 150px;">
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="category_id" class="form-label">Category *</label>
                    <select class="form-select" id="category_id" name="category_id" required>
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>" 
                                    <?php echo ($news['category_id'] ?? '') == $category['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="city_id" class="form-label">City *</label>
                    <select class="form-select" id="city_id" name="city_id" required>
                        <option value="">Select City</option>
                        <?php foreach ($cities as $city): ?>
                            <option value="<?php echo $city['id']; ?>" 
                                    <?php echo ($news['city_id'] ?? '') == $city['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($city['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="mb-3">
            <label for="content" class="form-label">Article Content *</label>
            <textarea class="form-control" id="content" name="content" rows="6" 
                      placeholder="Write your news article here (50-2000 characters)" required><?php echo htmlspecialchars($news['content'] ?? ''); ?></textarea>
            <div class="form-text">
                <span id="charCount">0</span>/2000 characters
                <span class="text-success ms-2">
                    <i class="fas fa-coins me-1"></i>Earn 10 points for posting news!
                </span>
            </div>
        </div>
        
        <div class="mb-3">
            <label for="external_link" class="form-label">External Link (Optional)</label>
            <input type="url" class="form-control" id="external_link" name="external_link" 
                   value="<?php echo htmlspecialchars($news['external_link'] ?? ''); ?>" 
                   placeholder="https://example.com">
            <div class="form-text">Add a link to the original source or related content</div>
        </div>
        
        <div class="d-flex justify-content-between">
            <div>
                <?php if ($editId): ?>
                    <button type="button" class="btn btn-danger" onclick="showDeleteModal()">
                        <i class="fas fa-trash me-2"></i>Delete Article
                    </button>
                <?php endif; ?>
            </div>
            
            <div>
                <a href="<?php echo base_url('my-news'); ?>" class="btn btn-secondary me-2">
                    <i class="fas fa-times me-2"></i>Cancel
                </a>
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <i class="fas fa-<?php echo $editId ? 'save' : 'paper-plane'; ?> me-2" id="submitIcon"></i>
                    <span id="submitText"><?php echo $editId ? 'Update Article' : 'Post Article'; ?></span>
                    <span id="loadingSpinner" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true" style="display: none;"></span>
                </button>
            </div>
        </div>
        
        <!-- Hidden delete form -->
        <?php if ($editId): ?>
            <form id="deleteForm" method="POST" style="display: none;">
                <input type="hidden" name="delete" value="1">
            </form>
        <?php endif; ?>
    </form>
</div>

<!-- Guidelines -->
<div class="dashboard-card">
    <h5 class="mb-3">
        <i class="fas fa-info-circle me-2"></i>Posting Guidelines
    </h5>
    
    <div class="row">
        <div class="col-md-6">
            <h6 class="text-success">✅ Do's</h6>
            <ul class="list-unstyled">
                <li><i class="fas fa-check text-success me-2"></i>Write accurate and factual content</li>
                <li><i class="fas fa-check text-success me-2"></i>Keep content under 500 characters</li>
                <li><i class="fas fa-check text-success me-2"></i>Use clear and descriptive titles</li>
                <li><i class="fas fa-check text-success me-2"></i>Add relevant external links</li>
                <li><i class="fas fa-check text-success me-2"></i>Choose appropriate category and city</li>
            </ul>
        </div>
        <div class="col-md-6">
            <h6 class="text-danger">❌ Don'ts</h6>
            <ul class="list-unstyled">
                <li><i class="fas fa-times text-danger me-2"></i>Post fake or misleading news</li>
                <li><i class="fas fa-times text-danger me-2"></i>Use offensive or inappropriate language</li>
                <li><i class="fas fa-times text-danger me-2"></i>Spam or post irrelevant content</li>
                <li><i class="fas fa-times text-danger me-2"></i>Violate copyright or privacy</li>
                <li><i class="fas fa-times text-danger me-2"></i>Post content longer than 500 characters</li>
            </ul>
        </div>
    </div>
</div>

<script>
// Prevent duplicate submissions and show loading
let isSubmitting = false;
document.querySelector('form').addEventListener('submit', function(e) {
    if (isSubmitting) {
        e.preventDefault();
        return false;
    }
    
    isSubmitting = true;
    const submitBtn = document.getElementById('submitBtn');
    const submitIcon = document.getElementById('submitIcon');
    const submitText = document.getElementById('submitText');
    const loadingSpinner = document.getElementById('loadingSpinner');
    
    // Show loading state
    submitBtn.disabled = true;
    submitIcon.style.display = 'none';
    submitText.textContent = '<?php echo $editId ? 'Updating' : 'Posting'; ?>...';
    loadingSpinner.style.display = 'inline-block';
    
    // Re-enable after 5 seconds as fallback
    setTimeout(() => {
        isSubmitting = false;
        submitBtn.disabled = false;
        submitIcon.style.display = 'inline-block';
        submitText.textContent = '<?php echo $editId ? 'Update Article' : 'Post Article'; ?>';
        loadingSpinner.style.display = 'none';
    }, 5000);
});

// Character counter
document.getElementById('content').addEventListener('input', function() {
    const charCount = this.value.length;
    const charCountElement = document.getElementById('charCount');
    charCountElement.textContent = charCount;
    
    if (charCount < 50) {
        charCountElement.classList.add('text-danger');
        charCountElement.classList.remove('text-warning', 'text-success');
    } else if (charCount > 2000) {
        charCountElement.classList.add('text-danger');
        charCountElement.classList.remove('text-warning', 'text-success');
    } else if (charCount > 1800) {
        charCountElement.classList.add('text-warning');
        charCountElement.classList.remove('text-danger', 'text-success');
    } else {
        charCountElement.classList.add('text-success');
        charCountElement.classList.remove('text-danger', 'text-warning');
    }
});

// Auto-save draft (optional feature)
let autoSaveTimeout;
document.querySelectorAll('input, textarea, select').forEach(element => {
    element.addEventListener('input', function() {
        clearTimeout(autoSaveTimeout);
        autoSaveTimeout = setTimeout(function() {
            // Auto-save functionality could be implemented here
            console.log('Auto-saving draft...');
        }, 5000);
    });
});

// Image preview functionality
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            document.getElementById('previewImg').src = e.target.result;
            document.getElementById('imagePreview').style.display = 'block';
        };
        
        reader.readAsDataURL(input.files[0]);
    }
}

function removeImage() {
    document.getElementById('featured_image').value = '';
    document.getElementById('imagePreview').style.display = 'none';
    document.getElementById('previewImg').src = '';
}

// Delete modal function for post-news page
function showDeleteModal() {
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
                    <button type="button" class="btn btn-danger" onclick="confirmDeleteFromEdit()">
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

function confirmDeleteFromEdit() {
    document.getElementById('deleteForm').submit();
}

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const content = document.getElementById('content').value;
    if (content.length > 500) {
        e.preventDefault();
        alert('Content must be maximum 500 characters. Current length: ' + content.length);
        return false;
    }
    
    // Validate image file size
    const imageInput = document.getElementById('featured_image');
    if (imageInput.files && imageInput.files[0]) {
        const fileSize = imageInput.files[0].size;
        const maxSize = 5 * 1024 * 1024; // 5MB
        
        if (fileSize > maxSize) {
            e.preventDefault();
            alert('Image file is too large. Maximum size is 5MB.');
            return false;
        }
    }
});
</script>

<?php include APP_PATH . '/views/layouts/user-footer.php'; ?>
