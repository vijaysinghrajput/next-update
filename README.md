# Next Update - Local News App

A modern, responsive local news application built with CodeIgniter 3, optimized for mobile webviews. Features user-generated content, referral system, points-based advertising, and admin management.

## Features

### 🏠 **Home & News**
- Responsive mobile-first design
- Featured news section
- Category and city-wise filtering
- Search functionality
- News article views with related content

### 👥 **User Management**
- User registration and login
- Password reset functionality
- User profiles and dashboards
- Referral system with points
- City-based user organization

### 📰 **News System**
- User-generated news posts
- Admin news channel (Bansgaonsandesh)
- Category and city filtering
- Image uploads for news articles
- View tracking and analytics

### 🎯 **Points & Referrals**
- Welcome bonus points
- Referral rewards system
- Points-based advertising
- Transaction history
- User statistics

### 🛠 **Admin Panel**
- News management
- User management
- Category and city management
- Analytics dashboard
- Content moderation

## Installation

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- Composer (optional)

### Quick Setup

1. **Clone/Download the project**
   ```bash
   git clone <repository-url>
   cd nextupdate
   ```

2. **Database Setup**
   ```bash
   # Run the setup script
   php setup.php
   
   # Or manually import the database
   mysql -u root -p < database_schema.sql
   ```

3. **Configure Database**
   Edit `application/config/database.php`:
   ```php
   'hostname' => 'localhost',
   'username' => 'your_username',
   'password' => 'your_password',
   'database' => 'local_news_app',
   ```

4. **Set Permissions**
   ```bash
   chmod 755 uploads/
   chmod 644 application/config/*
   ```

5. **Access the Application**
   Open your browser and navigate to:
   ```
   http://localhost/nextupdate
   ```

### Default Admin Account
- **Email:** admin@bansgaonsandesh.com
- **Password:** admin123

## Configuration

### App Configuration
Edit `application/config/app_config.php` to customize:
- App name and branding
- Contact information
- Points system settings
- Upload configurations
- Social media links

### Key Settings
```php
$config['app_name'] = 'Next Update';
$config['admin_channel_name'] = 'Bansgaonsandesh';
$config['welcome_points'] = 10;
$config['referral_points'] = 10;
$config['ad_cost_per_day'] = 50;
```

## Mobile WebView Optimization

The app is specifically optimized for mobile webviews with:
- Touch-friendly interface
- Responsive design for all screen sizes
- Fast loading and smooth scrolling
- WebView-specific JavaScript handlers
- Mobile-optimized forms and navigation

## API Endpoints

### News
- `GET /` - Home page with latest news
- `GET /news/{id}` - Individual news article
- `GET /category/{slug}` - News by category
- `GET /city/{slug}` - News by city

### Authentication
- `GET /login` - Login page
- `POST /login` - User login
- `GET /signup` - Registration page
- `POST /signup` - User registration
- `GET /logout` - User logout
- `GET /forgot-password` - Password reset request
- `POST /forgot-password` - Send reset email
- `GET /reset-password/{token}` - Reset password form
- `POST /reset-password/{token}` - Update password

### User Dashboard
- `GET /dashboard` - User dashboard
- `GET /my-news` - User's news posts
- `GET /post-news` - Create news form
- `GET /my-ads` - User's advertisements
- `GET /create-ad` - Create ad form
- `GET /referrals` - Referral statistics

### Admin Panel
- `GET /admin` - Admin dashboard
- `GET /admin/news` - News management
- `GET /admin/users` - User management
- `GET /admin/categories` - Category management
- `GET /admin/cities` - City management

## Database Schema

### Core Tables
- `users` - User accounts and profiles
- `news_articles` - News posts and articles
- `categories` - News categories
- `cities` - Available cities
- `user_ads` - User advertisements
- `referrals` - Referral tracking
- `point_transactions` - Points history
- `password_reset_tokens` - Password reset tokens

## Features in Detail

### Referral System
- Users get unique referral codes
- 10 points for each successful referral
- Referral tracking and statistics
- Bonus points for new users

### Points System
- Welcome bonus: 10 points
- Referral bonus: 10 points per referral
- News posting: 5 points per post
- Ad posting: 50 points per day
- Transaction history tracking

### News Management
- Two types of news: User-generated and Admin (Bansgaonsandesh)
- Category and city filtering
- Featured news system
- Image uploads
- View tracking
- Related news suggestions

### Mobile Optimization
- Bootstrap 5 responsive framework
- Touch-friendly navigation
- Optimized images and loading
- WebView-specific features
- Mobile-first CSS design

## Security Features

- Password hashing with PHP's password_hash()
- CSRF protection
- SQL injection prevention
- XSS protection
- File upload security
- Session management
- Input validation and sanitization

## Browser Support

- Chrome (Mobile & Desktop)
- Safari (Mobile & Desktop)
- Firefox (Mobile & Desktop)
- Edge (Mobile & Desktop)
- WebView (Android & iOS)

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Support

For support and questions:
- Email: info@nextupdate.com
- Phone: +91 9876543210

## Changelog

### Version 1.0.0
- Initial release
- User authentication system
- News posting and management
- Referral and points system
- Admin panel
- Mobile-optimized design
- WebView compatibility

---

**Made with ❤️ for local communities**
