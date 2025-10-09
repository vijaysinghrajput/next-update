<?php
require_once __DIR__ . '/../../bootstrap-fixed.php';

use App\Services\Session;

// Destroy the session
Session::logout();

// Redirect to home page with success message
redirect(base_url('?logged_out=1'));
?>
