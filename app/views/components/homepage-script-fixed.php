<?php
// Fixed Homepage JavaScript Component - No Multiple Loading Issues
?>

<script>
$(document).ready(function() {
    let currentFilter = 'all';
    let currentPage = 1;
    let isLoading = false;
    let hasMoreNews = true;

    // Category slider functionality
    $('.category-item').on('click', function() {
        const filter = $(this).data('filter');
        currentFilter = filter;
        currentPage = 1;
        hasMoreNews = true;
        
        // Update active state
        $('.category-item').removeClass('active');
        $(this).addClass('active');
        
        // Smooth scroll to center the selected item
        const container = $('#categorySlider')[0];
        const item = this;
        const containerWidth = container.offsetWidth;
        const itemOffset = item.offsetLeft;
        const itemWidth = item.offsetWidth;
        const scrollLeft = itemOffset - (containerWidth / 2) + (itemWidth / 2);
        
        container.scrollTo({
            left: scrollLeft,
            behavior: 'smooth'
        });
        
        // Filter existing news
        filterNews(filter);
        
        // Load new news for this filter
        loadMoreNews();
    });

    // Filter existing news based on category
    function filterNews(filter) {
        const $newsItems = $('.latest-card, .featured-card');
        
        $newsItems.each(function() {
            const $item = $(this);
            const categoryId = $item.data('category');
            const isBansgaonsandesh = $item.data('is-bansgaonsandesh');
            
            let show = false;
            
            if (filter === 'all') {
                show = true;
            } else if (filter === 'featured') {
                show = $item.hasClass('featured-card');
            } else if (filter === 'bansgaonsandesh') {
                show = isBansgaonsandesh;
            } else if (filter.startsWith('category-')) {
                const categoryFilter = filter.replace('category-', '');
                show = categoryId == categoryFilter;
            }
            
            if (show) {
                $item.show();
            } else {
                $item.hide();
            }
        });
    }

    // Simple loading function
    function showMainLoading(show) {
        const $loading = $('#mainLoadingSection');
        if (show) {
            $loading.show();
        } else {
            $loading.hide();
        }
    }

    // Load more news function
    function loadMoreNews() {
        if (isLoading || !hasMoreNews) return;
        
        isLoading = true;
        showMainLoading(true);
        
        $.ajax({
            url: '<?php echo base_url('api/news'); ?>',
            method: 'GET',
            data: {
                page: currentPage + 1,
                limit: 10,
                filter: currentFilter
            },
            dataType: 'json',
            success: function(response) {
                if (response.success && response.data.length > 0) {
                    currentPage++;
                    appendNews(response.data);
                } else {
                    hasMoreNews = false;
                }
            },
            error: function() {
                console.log('Error loading more news');
            },
            complete: function() {
                isLoading = false;
                showMainLoading(false);
            }
        });
    }

    // Append news to the list
    function appendNews(newsData) {
        const $newsList = $('#newsList');
        
        newsData.forEach((news, index) => {
            // Add between news ads randomly
            if (window.betweenNewsAds && window.betweenNewsAds.length > 0 && Math.random() < 0.3) {
                const randomAd = window.betweenNewsAds[Math.floor(Math.random() * window.betweenNewsAds.length)];
                const adHtml = createBetweenNewsAdHtml(randomAd);
                $newsList.append(adHtml);
            }
            
            // Add news item
            const newsHtml = createNewsItemHtml(news);
            $newsList.append(newsHtml);
        });
    }

    // Create news item HTML
    function createNewsItemHtml(news) {
        return `
            <div class="latest-card" 
                 data-category="${news.category_id}" 
                 data-is-bansgaonsandesh="${news.is_bansgaonsandesh}"
                 data-news-id="${news.id}"
                 data-news-title="${news.title}"
                 data-news-excerpt="${news.excerpt || news.content.substring(0, 120) + '...'}"
                 data-news-image="${news.featured_image || ''}"
                 onclick="trackNewsView(${news.id}, '${news.slug || ''}')">
                ${news.featured_image ? `
                    <div class="latest-image-container">
                        <img src="${news.featured_image}" alt="${news.title}" class="latest-image">
                        <div class="latest-badge">
                            ${news.is_bansgaonsandesh ? '<span class="badge-premium">Premium</span>' : 
                              news.is_featured ? '<span class="badge-featured">Featured</span>' : 
                              '<span class="badge-latest">Latest</span>'}
                        </div>
                    </div>
                ` : ''}
                <div class="latest-content">
                    <div class="latest-category">${news.category_name}</div>
                    <h3 class="latest-title">${news.title}</h3>
                    <p class="latest-excerpt">${news.excerpt || news.content.substring(0, 120) + '...'}</p>
                    <div class="latest-meta">
                        <div class="meta-item">
                            <span class="meta-icon">📍</span>
                            <span>${news.city_name}</span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-icon">👁️</span>
                            <span>${news.views || 0} views</span>
                        </div>
                        <div class="meta-time">${new Date(news.created_at).toLocaleDateString('en-US', {month: 'short', day: 'numeric'})}</div>
                    </div>
                </div>
                
                <!-- Share Button -->
                <div class="news-share-actions">
                    <button class="share-news-btn" onclick="event.stopPropagation(); shareNewsFromCard(${news.id})">
                        <i class="fas fa-share-alt"></i>
                    </button>
                </div>
                
            </div>
        `;
    }

    // Create between news ad HTML
    function createBetweenNewsAdHtml(ad) {
        return `
            <div class="between-news-ad">
                <div class="ad-header">
                    <span class="ad-label">Advertisement</span>
                </div>
                <div class="ad-card-container">
                    <div class="ad-card" onclick="trackAdClick(${ad.id})">
                        <div class="ad-tag">Advertisement</div>
                        ${ad.image ? `
                            <div class="ad-image-wrapper">
                                <img src="${ad.image}" alt="${ad.heading}" class="ad-image">
                            </div>
                        ` : ''}
                        <div class="ad-content">
                            <h3 class="ad-title">${ad.heading}</h3>
                            <p class="ad-description">${ad.description}</p>
                            <div class="ad-actions">
                                ${ad.whatsapp_number ? `<button class="action-btn whatsapp-btn" data-action="whatsapp" data-phone="${ad.whatsapp_number}"><span class="whatsapp-icon">💬</span><span>WhatsApp</span></button>` : ''}
                                ${ad.call_number ? `<button class="action-btn call-btn" data-action="call" data-phone="${ad.call_number}"><i class="fas fa-phone"></i><span>Call</span></button>` : ''}
                                ${ad.website_url ? `<a href="${ad.website_url}" class="action-btn visit-btn"><i class="fas fa-globe"></i><span>Visit</span></a>` : ''}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    // Ad click tracking
    window.trackAdClick = function(adId) {
        $.ajax({
            url: '<?php echo base_url('api/track-ad'); ?>',
            method: 'POST',
            data: { ad_id: adId },
            dataType: 'json'
        });
    };

    // Share news from card
    window.shareNewsFromCard = function(newsId) {
        const newsElement = document.querySelector(`[data-news-id="${newsId}"]`);
        if (!newsElement) return;
        
        const newsTitle = newsElement.dataset.newsTitle || '';
        const newsExcerpt = newsElement.dataset.newsExcerpt || '';
        const newsImage = newsElement.dataset.newsImage || '';
        const playStoreLink = 'https://play.google.com/store/apps/details?id=com.skyably.nextupdate';
        
        const shareText = `📰 ${newsTitle}\n\n${newsExcerpt}\n\n📱 Download Next Update App:\n${playStoreLink}`;
        
        if (navigator.share) {
            const shareData = {
                title: newsTitle,
                text: shareText,
                url: playStoreLink
            };
            
            navigator.share(shareData);
        } else {
            // Fallback - copy to clipboard
            navigator.clipboard.writeText(shareText).then(() => {
                if (window.MobileApp) {
                    window.MobileApp.showToast('News details copied to clipboard!');
                } else {
                    alert('News details copied to clipboard!');
                }
            });
        }
    };

    // News view tracking and navigation
    window.trackNewsView = function(newsId, newsSlug) {
        // Track the view
        $.ajax({
            url: '<?php echo base_url('api/track-news-view'); ?>',
            method: 'POST',
            data: { news_id: newsId },
            dataType: 'json'
        });
        
        // Navigate to news detail page
        const newsUrl = newsSlug ? 
            '<?php echo base_url('news/'); ?>' + newsSlug : 
            '<?php echo base_url('news/'); ?>' + newsId;
        
        window.location.href = newsUrl;
    };

    // Infinite scroll
    let scrollTimeout;
    $(window).on('scroll', function() {
        clearTimeout(scrollTimeout);
        scrollTimeout = setTimeout(function() {
            if ($(window).scrollTop() + $(window).height() >= $(document).height() - 100) {
                loadMoreNews();
            }
        }, 100);
    });

    // Action button handlers
    $(document).on('click', '[data-action="whatsapp"]', function(e) {
        e.stopPropagation();
        const phone = $(this).data('phone');
        if (phone) {
            window.open(`https://wa.me/${phone}`, '_blank');
        }
    });

    $(document).on('click', '[data-action="call"]', function(e) {
        e.stopPropagation();
        const phone = $(this).data('phone');
        if (phone) {
            window.open(`tel:${phone}`, '_self');
        }
    });

    // Popup ads (simplified)
    let popupAdInterval;
    let lastPopupTime = 0;

    function showRandomPopupAd() {
        const popupModal = document.getElementById('popupAdModal');
        const popupContent = document.getElementById('popupAdContent');
        
        if (popupModal && popupContent && window.popupAds && window.popupAds.length > 0) {
            const randomAd = window.popupAds[Math.floor(Math.random() * window.popupAds.length)];
            
            const popupHtml = `
                <div class="ad-card-container">
                    <div class="ad-card" onclick="trackAdClick(${randomAd.id})">
                        <div class="ad-tag">Advertisement</div>
                        ${randomAd.image ? `
                            <div class="ad-image-wrapper">
                                <img src="${randomAd.image}" alt="${randomAd.heading}" class="ad-image">
                            </div>
                        ` : ''}
                        <div class="ad-content">
                            <h3 class="ad-title">${randomAd.heading}</h3>
                            <p class="ad-description">${randomAd.description}</p>
                            <div class="ad-actions">
                                ${randomAd.whatsapp_number ? `<button class="action-btn whatsapp-btn" data-action="whatsapp" data-phone="${randomAd.whatsapp_number}"><span class="whatsapp-icon">💬</span><span>WhatsApp</span></button>` : ''}
                                ${randomAd.call_number ? `<button class="action-btn call-btn" data-action="call" data-phone="${randomAd.call_number}"><i class="fas fa-phone"></i><span>Call</span></button>` : ''}
                                ${randomAd.website_url ? `<a href="${randomAd.website_url}" class="action-btn visit-btn"><i class="fas fa-globe"></i><span>Visit</span></a>` : ''}
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            popupContent.innerHTML = popupHtml;
            const modal = new bootstrap.Modal(popupModal);
            modal.show();
            trackAdClick(randomAd.id);
        }
    }
    
    // Show first popup ad after 5 seconds
    setTimeout(function() {
        showRandomPopupAd();
        lastPopupTime = Date.now();
    }, 5000);
    
    // Set up interval to show popup ads every 2 minutes
    popupAdInterval = setInterval(function() {
        const now = Date.now();
        if (now - lastPopupTime >= 120000) {
            showRandomPopupAd();
            lastPopupTime = now;
        }
    }, 30000);
    
    // Clean up interval when page is unloaded
    window.addEventListener('beforeunload', function() {
        if (popupAdInterval) {
            clearInterval(popupAdInterval);
        }
    });
});
</script>
