<?php
// Admin Layout Component
// This component provides the admin layout for all admin pages

// Check if user is logged in and is admin
if (!session('user_id') || !session('is_admin')) {
    redirect('/login');
}

// Set page title if not already set
if (!isset($page_title)) {
    $page_title = "Admin Panel";
}

// Include admin header
include APP_PATH . '/views/layouts/admin-header.php';
?>

<!-- Admin Content will be inserted here -->
<?php if (isset($admin_content)): ?>
    <?php echo $admin_content; ?>
<?php endif; ?>

<?php
// Include admin footer
include APP_PATH . '/views/layouts/admin-footer.php';
?>
