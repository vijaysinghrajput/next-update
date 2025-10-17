<?php
// Application Routes

use App\Services\Router;

// Home page
Router::get('/', 'website/index');

// Website pages
Router::get('/about', 'website/about');
Router::get('/contact', 'website/contact');
Router::get('/privacy', 'website/privacy');
Router::get('/terms', 'website/terms');
Router::get('/ads-demo', 'website/ads-demo');
Router::get('/rss-news', 'website/rss-news');
Router::get('/news', 'website/news');

// Authentication
Router::get('/login', 'website/login');
Router::post('/login', 'website/login');
Router::get('/signup', 'website/signup');
Router::post('/signup', 'website/signup');
Router::get('/logout', 'user/logout');
Router::get('/forgot-password', 'website/forgot-password');
Router::post('/forgot-password', 'website/forgot-password');
Router::get('/reset-password/{token}', 'website/reset-password');
Router::post('/reset-password/{token}', 'website/reset-password');

// User pages
Router::get('/dashboard', 'user/dashboard');
Router::get('/profile', 'user/profile');
Router::post('/profile', 'user/profile');
Router::get('/my-news', 'user/my-news');
Router::get('/post-news', 'user/post-news');
Router::post('/post-news', 'user/post-news');
Router::get('/post-news/{id}/edit', 'user/post-news');
Router::post('/post-news/{id}/edit', 'user/post-news');
Router::post('/post-news/{id}/delete', 'user/delete-news');
Router::get('/kyc-verification', 'user/kyc-verification');
Router::post('/kyc-verification', 'user/kyc-verification');
Router::get('/buy-points', 'user/buy-points');
Router::post('/buy-points', 'user/buy-points');
Router::get('/post-ad', 'user/post-ad');
Router::post('/post-ad', 'user/post-ad');
Router::get('/referrals', 'user/referrals');
Router::get('/transactions', 'user/transactions');
Router::get('/notifications', 'user/notifications');

// News pages
Router::get('/news', 'website/news');
Router::get('/news/{slug}', 'website/news-detail');
Router::get('/category/{slug}', 'website/category');
Router::get('/city/{slug}', 'website/city');
Router::get('/search', 'website/search');

// Admin pages
Router::get('/admin/login', 'admin/login');
Router::post('/admin/login', 'admin/login');
Router::get('/admin', 'admin/working-dashboard');
Router::get('/admin/news', 'admin/news');
Router::post('/admin/news', 'admin/news');
Router::get('/admin/news/{id}/edit', 'admin/news-edit');
Router::post('/admin/news/{id}/edit', 'admin/news-edit');
Router::get('/admin/users', 'admin/users');
Router::get('/admin/users/{id}', 'admin/user-detail');
Router::get('/admin/categories', 'admin/categories');
Router::post('/admin/categories', 'admin/categories');
Router::get('/admin/cities', 'admin/cities');
Router::post('/admin/cities', 'admin/cities');
Router::get('/admin/kyc', 'admin/kyc');
Router::post('/admin/kyc', 'admin/kyc');
Router::get('/admin/kyc/{id}/approve', 'admin/kyc-approve');
Router::get('/admin/kyc/{id}/reject', 'admin/kyc-reject');
Router::get('/admin/payments', 'admin/payments');
Router::post('/admin/payments', 'admin/payments');
Router::get('/admin/ads', 'admin/ads');
Router::post('/admin/ads', 'admin/ads');
Router::get('/admin/settings', 'admin/settings');
Router::post('/admin/settings', 'admin/settings');

// Service Worker route - must be before other routes
Router::get('/sw.js', 'api/service-worker');

// Image serving route - must be before other routes
Router::get('/uploads/{path}', 'api/image');

// API routes
Router::get('/api/news', 'api/news');
Router::get('/api/rss-news', 'api/rss-news');
Router::get('/api/categories', 'api/categories');
Router::get('/api/cities', 'api/cities');
Router::post('/api/upload', 'api/upload');
Router::get('/api/payments', 'api/payments');
Router::post('/api/payments', 'api/payments');
Router::post('/api/track-ad', 'api/track-ad');
Router::post('/api/track-news-view', 'api/track-news-view');
Router::get('/api/ads', 'api/ads');
Router::get('/api/notifications', 'api/notifications');
?>
