<?php
// Include mobile header
include_once __DIR__ . '/../../app/views/layouts/mobile-header.php';
?>

<div class="mobile-app-container">
    <!-- Contact Page Header -->
    <div class="contact-header">
        <div class="contact-hero">
            <h1 class="contact-title">Get in Touch</h1>
            <p class="contact-subtitle">We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>
        </div>
    </div>

    <!-- Contact Information Cards -->
    <div class="contact-info-section">
        <div class="contact-card">
            <div class="contact-icon">
                <i class="fas fa-phone"></i>
            </div>
            <div class="contact-details">
                <h3>Phone</h3>
                <p>+91 9876543210</p>
                <span class="contact-label">Call us anytime</span>
            </div>
        </div>

        <div class="contact-card">
            <div class="contact-icon">
                <i class="fas fa-envelope"></i>
            </div>
            <div class="contact-details">
                <h3>Email</h3>
                <p>info@nextupdate.com</p>
                <span class="contact-label">We'll reply within 24 hours</span>
            </div>
        </div>

        <div class="contact-card">
            <div class="contact-icon">
                <i class="fab fa-whatsapp"></i>
            </div>
            <div class="contact-details">
                <h3>WhatsApp</h3>
                <p>+91 9876543210</p>
                <span class="contact-label">Chat with us instantly</span>
            </div>
        </div>

        <div class="contact-card">
            <div class="contact-icon">
                <i class="fas fa-map-marker-alt"></i>
            </div>
            <div class="contact-details">
                <h3>Address</h3>
                <p>123 Business Street<br>City, State 12345</p>
                <span class="contact-label">Visit our office</span>
            </div>
        </div>
    </div>

    <!-- Contact Form -->
    <div class="contact-form-section">
        <div class="form-container">
            <h2 class="form-title">Send us a Message</h2>
            <p class="form-subtitle">Fill out the form below and we'll get back to you</p>
            
            <form class="contact-form" id="contactForm">
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" required>
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone">
                </div>

                <div class="form-group">
                    <label for="subject">Subject</label>
                    <select id="subject" name="subject" required>
                        <option value="">Select a subject</option>
                        <option value="general">General Inquiry</option>
                        <option value="support">Technical Support</option>
                        <option value="advertising">Advertising</option>
                        <option value="partnership">Partnership</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea id="message" name="message" rows="5" placeholder="Tell us how we can help you..." required></textarea>
                </div>

                <button type="submit" class="submit-btn">
                    <span class="btn-text">Send Message</span>
                    <div class="btn-loader" style="display: none;">
                        <div class="spinner"></div>
                    </div>
                </button>
            </form>
        </div>
    </div>

    <!-- Social Media Links -->
    <div class="social-section">
        <h3 class="social-title">Follow Us</h3>
        <div class="social-links">
            <a href="#" class="social-link facebook">
                <i class="fab fa-facebook-f"></i>
                <span>Facebook</span>
            </a>
            <a href="#" class="social-link twitter">
                <i class="fab fa-twitter"></i>
                <span>Twitter</span>
            </a>
            <a href="#" class="social-link instagram">
                <i class="fab fa-instagram"></i>
                <span>Instagram</span>
            </a>
            <a href="#" class="social-link linkedin">
                <i class="fab fa-linkedin-in"></i>
                <span>LinkedIn</span>
            </a>
        </div>
    </div>
</div>

<!-- Contact Page JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const contactForm = document.getElementById('contactForm');
    const submitBtn = contactForm.querySelector('.submit-btn');
    const btnText = submitBtn.querySelector('.btn-text');
    const btnLoader = submitBtn.querySelector('.btn-loader');

    contactForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Show loading state
        submitBtn.disabled = true;
        btnText.style.display = 'none';
        btnLoader.style.display = 'flex';
        
        // Simulate form submission
        setTimeout(() => {
            // Reset form
            contactForm.reset();
            
            // Show success message
            showToast('Message sent successfully! We\'ll get back to you soon.', 'success');
            
            // Reset button
            submitBtn.disabled = false;
            btnText.style.display = 'block';
            btnLoader.style.display = 'none';
        }, 2000);
    });

    // Phone number formatting
    const phoneInput = document.getElementById('phone');
    phoneInput.addEventListener('input', function() {
        let value = this.value.replace(/\D/g, '');
        if (value.length === 10 && /^[6-9]/.test(value)) {
            this.value = '+91 ' + value;
        } else if (value.length > 0 && value.length <= 10) {
            this.value = value;
        }
    });

    // Contact card click handlers
    document.querySelectorAll('.contact-card').forEach(card => {
        card.addEventListener('click', function() {
            const contactType = this.querySelector('h3').textContent.toLowerCase();
            
            if (contactType === 'phone') {
                window.location.href = 'tel:+919876543210';
            } else if (contactType === 'email') {
                window.location.href = 'mailto:info@nextupdate.com';
            } else if (contactType === 'whatsapp') {
                window.open('https://wa.me/919876543210', '_blank');
            } else if (contactType === 'address') {
                // Open maps
                window.open('https://maps.google.com/?q=123+Business+Street+City+State+12345', '_blank');
            }
        });
    });
});

function showToast(message, type = 'info') {
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `
        <div class="toast-content">
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'info-circle'}"></i>
            <span>${message}</span>
        </div>
    `;
    
    // Add to page
    document.body.appendChild(toast);
    
    // Show toast
    setTimeout(() => toast.classList.add('show'), 100);
    
    // Remove toast
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}
</script>

<!-- Contact Page Styles -->
<style>
/* Contact Page Styles */
.contact-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 40px 20px;
    text-align: center;
}

.contact-hero {
    max-width: 600px;
    margin: 0 auto;
}

.contact-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin: 0 0 16px 0;
}

.contact-subtitle {
    font-size: 1.1rem;
    opacity: 0.9;
    line-height: 1.6;
    margin: 0;
}

.contact-info-section {
    padding: 30px 20px;
    background: #f8f9fa;
}

.contact-card {
    background: white;
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    display: flex;
    align-items: center;
    gap: 20px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.contact-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.12);
}

.contact-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    flex-shrink: 0;
}

.contact-details h3 {
    font-size: 1.2rem;
    font-weight: 600;
    color: #1a1a1a;
    margin: 0 0 8px 0;
}

.contact-details p {
    font-size: 1rem;
    color: #495057;
    margin: 0 0 4px 0;
    line-height: 1.4;
}

.contact-label {
    font-size: 0.85rem;
    color: #6c757d;
    font-weight: 500;
}

.contact-form-section {
    padding: 40px 20px;
    background: white;
}

.form-container {
    max-width: 600px;
    margin: 0 auto;
}

.form-title {
    font-size: 1.8rem;
    font-weight: 700;
    color: #1a1a1a;
    text-align: center;
    margin: 0 0 8px 0;
}

.form-subtitle {
    font-size: 1rem;
    color: #6c757d;
    text-align: center;
    margin: 0 0 32px 0;
}

.contact-form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-group label {
    font-size: 0.9rem;
    font-weight: 600;
    color: #495057;
    margin-bottom: 8px;
}

.form-group input,
.form-group select,
.form-group textarea {
    padding: 12px 16px;
    border: 2px solid #e9ecef;
    border-radius: 12px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: white;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-group textarea {
    resize: vertical;
    min-height: 120px;
}

.submit-btn {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 16px 32px;
    border-radius: 12px;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    margin-top: 8px;
}

.submit-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
}

.submit-btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
    transform: none;
}

.btn-loader {
    display: flex;
    align-items: center;
    gap: 8px;
}

.spinner {
    width: 20px;
    height: 20px;
    border: 2px solid rgba(255,255,255,0.3);
    border-top: 2px solid white;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.social-section {
    padding: 40px 20px;
    background: #f8f9fa;
    text-align: center;
}

.social-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: #1a1a1a;
    margin: 0 0 24px 0;
}

.social-links {
    display: flex;
    justify-content: center;
    gap: 16px;
    flex-wrap: wrap;
}

.social-link {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    padding: 16px;
    background: white;
    border-radius: 12px;
    text-decoration: none;
    color: #495057;
    transition: all 0.3s ease;
    min-width: 80px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
}

.social-link:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 20px rgba(0,0,0,0.12);
    color: inherit;
}

.social-link i {
    font-size: 1.5rem;
}

.social-link span {
    font-size: 0.85rem;
    font-weight: 500;
}

.social-link.facebook:hover { color: #1877f2; }
.social-link.twitter:hover { color: #1da1f2; }
.social-link.instagram:hover { color: #e4405f; }
.social-link.linkedin:hover { color: #0077b5; }

.toast {
    position: fixed;
    top: 20px;
    left: 50%;
    transform: translateX(-50%);
    background: white;
    border-radius: 12px;
    box-shadow: 0 8px 30px rgba(0,0,0,0.15);
    padding: 16px 20px;
    z-index: 10000;
    opacity: 0;
    transition: all 0.3s ease;
}

.toast.show {
    opacity: 1;
    transform: translateX(-50%) translateY(0);
}

.toast-content {
    display: flex;
    align-items: center;
    gap: 12px;
    color: #495057;
}

.toast-success {
    border-left: 4px solid #28a745;
}

.toast-success i {
    color: #28a745;
}

/* Mobile responsiveness */
@media (max-width: 480px) {
    .contact-title {
        font-size: 2rem;
    }
    
    .contact-card {
        padding: 20px;
        gap: 16px;
    }
    
    .contact-icon {
        width: 50px;
        height: 50px;
        font-size: 1.2rem;
    }
    
    .form-container {
        padding: 0;
    }
    
    .social-links {
        gap: 12px;
    }
    
    .social-link {
        min-width: 70px;
        padding: 12px;
    }
}
</style>

<?php include __DIR__ . '/../../app/views/layouts/mobile-footer.php'; ?>
