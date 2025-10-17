<?php
namespace App\Services;

use Exception;
use DateTime;

class RSSService {
    
    private $categories = [
        'top-stories' => [
            'name' => 'Top Stories',
            'english' => 'https://news.google.com/rss?topic=h&hl=en-IN&gl=IN&ceid=IN:en',
            'hindi' => 'https://news.google.com/rss?topic=h&hl=hi&gl=IN&ceid=IN:hi'
        ],
        'world' => [
            'name' => 'World',
            'english' => 'https://news.google.com/rss?topic=w&hl=en-IN&gl=IN&ceid=IN:en',
            'hindi' => 'https://news.google.com/rss?topic=w&hl=hi&gl=IN&ceid=IN:hi'
        ],
        'business' => [
            'name' => 'Business',
            'english' => 'https://news.google.com/rss?topic=b&hl=en-IN&gl=IN&ceid=IN:en',
            'hindi' => 'https://news.google.com/rss?topic=b&hl=hi&gl=IN&ceid=IN:hi'
        ],
        'technology' => [
            'name' => 'Technology',
            'english' => 'https://news.google.com/rss?topic=tc&hl=en-IN&gl=IN&ceid=IN:en',
            'hindi' => 'https://news.google.com/rss?topic=tc&hl=hi&gl=IN&ceid=IN:hi'
        ],
        'entertainment' => [
            'name' => 'Entertainment',
            'english' => 'https://news.google.com/rss?topic=e&hl=en-IN&gl=IN&ceid=IN:en',
            'hindi' => 'https://news.google.com/rss?topic=e&hl=hi&gl=IN&ceid=IN:hi'
        ],
        'sports' => [
            'name' => 'Sports',
            'english' => 'https://news.google.com/rss?topic=s&hl=en-IN&gl=IN&ceid=IN:en',
            'hindi' => 'https://news.google.com/rss?topic=s&hl=hi&gl=IN&ceid=IN:hi'
        ],
        'science' => [
            'name' => 'Science',
            'english' => 'https://news.google.com/rss?topic=snc&hl=en-IN&gl=IN&ceid=IN:en',
            'hindi' => 'https://news.google.com/rss?topic=snc&hl=hi&gl=IN&ceid=IN:hi'
        ],
        'health' => [
            'name' => 'Health',
            'english' => 'https://news.google.com/rss?topic=m&hl=en-IN&gl=IN&ceid=IN:en',
            'hindi' => 'https://news.google.com/rss?topic=m&hl=hi&gl=IN&ceid=IN:hi'
        ]
    ];
    
    public function getCategories() {
        return $this->categories;
    }
    
    public function fetchRSSFeed($category = 'top-stories', $limit = 20, $language = 'hindi') {
        if (!isset($this->categories[$category])) {
            $category = 'top-stories';
        }
        
        $news = [];
        
        // Fetch English news
        if ($language === 'english' || $language === 'both') {
            $englishNews = $this->fetchSingleLanguageFeed($category, 'english', $limit);
            foreach ($englishNews as $item) {
                $item['language'] = 'english';
                $news[] = $item;
            }
        }
        
        // Fetch Hindi news
        if ($language === 'hindi' || $language === 'both') {
            $hindiNews = $this->fetchSingleLanguageFeed($category, 'hindi', $limit);
            foreach ($hindiNews as $item) {
                $item['language'] = 'hindi';
                $news[] = $item;
            }
        }
        
        // Shuffle and limit results for better mix
        if ($language === 'both') {
            shuffle($news);
            $news = array_slice($news, 0, $limit);
        }
        
        // Sort by publication date (newest first)
        usort($news, function($a, $b) {
            return strtotime($b['pubDate']) - strtotime($a['pubDate']);
        });
        
        return [
            'success' => true,
            'articles' => $news,
            'category' => $this->categories[$category]['name']
        ];
    }
    
    private function fetchSingleLanguageFeed($category, $language, $limit) {
        $url = $this->categories[$category][$language];
        
        try {
            // Use cURL to fetch RSS feed
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            
            $data = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode !== 200 || !$data) {
                throw new Exception('Failed to fetch RSS feed');
            }
            
            // Parse XML
            $xml = simplexml_load_string($data);
            
            if (!$xml) {
                throw new Exception('Failed to parse RSS XML');
            }
            
            $articles = [];
            $count = 0;
            
            foreach ($xml->channel->item as $item) {
                if ($count >= $limit) break;
                
                // Extract image from multiple sources
                $image = null;
                
                // Check for enclosure tag (common in RSS feeds)
                if (isset($item->enclosure) && isset($item->enclosure['url'])) {
                    $enclosureUrl = (string) $item->enclosure['url'];
                    if (preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $enclosureUrl)) {
                        $image = $enclosureUrl;
                    }
                }
                
                // Check for media:content or media:thumbnail
                if (!$image && isset($item->children('media', true)->content)) {
                    $mediaContent = $item->children('media', true)->content;
                    if (isset($mediaContent['url'])) {
                        $image = (string) $mediaContent['url'];
                    }
                }
                
                if (!$image && isset($item->children('media', true)->thumbnail)) {
                    $mediaThumbnail = $item->children('media', true)->thumbnail;
                    if (isset($mediaThumbnail['url'])) {
                        $image = (string) $mediaThumbnail['url'];
                    }
                }
                
                // Fallback to description extraction
                if (!$image) {
                    $image = $this->extractImage((string) $item->description);
                }
                
                // Generate a placeholder if no image found
                if (!$image) {
                    $image = $this->generatePlaceholderImage($this->categories[$category]['name']);
                }
                
                $article = [
                    'title' => (string) $item->title,
                    'description' => $this->cleanDescription((string) $item->description),
                    'link' => (string) $item->link,
                    'pubDate' => (string) $item->pubDate,
                    'pub_date' => $this->formatDate((string) $item->pubDate),
                    'source' => $this->extractSource((string) $item->description),
                    'image' => $image,
                    'category' => $this->categories[$category]['name']
                ];
                
                $articles[] = $article;
                $count++;
            }
            
            return $articles;
            
        } catch (Exception $e) {
            return [];
        }
    }
    
    private function cleanDescription($description) {
        // Remove HTML tags and clean up description
        $clean = strip_tags($description);
        $clean = html_entity_decode($clean);
        
        // Remove extra whitespace
        $clean = preg_replace('/\s+/', ' ', $clean);
        
        // Limit to 200 characters
        if (strlen($clean) > 200) {
            $clean = substr($clean, 0, 200) . '...';
        }
        
        return trim($clean);
    }
    
    private function extractSource($description) {
        // Try to extract source from description
        preg_match('/<a[^>]*>(.*?)<\/a>/', $description, $matches);
        if (isset($matches[1])) {
            return strip_tags($matches[1]);
        }
        return 'Google News';
    }
    
    private function extractImage($description) {
        // Try multiple methods to extract image from Google News RSS
        
        // Method 1: Standard img src
        preg_match('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/', $description, $matches);
        if (isset($matches[1])) {
            return $this->cleanImageUrl($matches[1]);
        }
        
        // Method 2: Look for image URLs in the content
        preg_match('/https?:\/\/[^\s]+\.(?:jpg|jpeg|png|gif|webp)(?:\?[^\s]*)?/i', $description, $matches);
        if (isset($matches[0])) {
            return $this->cleanImageUrl($matches[0]);
        }
        
        // Method 3: Extract from Google News specific format
        preg_match('/url\(([^)]+)\)/', $description, $matches);
        if (isset($matches[1])) {
            $url = trim($matches[1], '"\'');
            if (filter_var($url, FILTER_VALIDATE_URL)) {
                return $this->cleanImageUrl($url);
            }
        }
        
        // Method 4: Look for enclosure tag (some RSS feeds use this)
        return null;
    }
    
    private function cleanImageUrl($url) {
        // Clean and validate image URL
        $url = html_entity_decode($url);
        $url = trim($url);
        
        // Remove any surrounding quotes
        $url = trim($url, '"\'');
        
        // Validate URL
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            return $url;
        }
        
        return null;
    }
    
    private function generatePlaceholderImage($category) {
        // Generate a placeholder image URL using a service like Picsum or create a data URL
        $colors = [
            'Top Stories' => 'ff6b6b',
            'World' => '4ecdc4', 
            'Business' => '45b7d1',
            'Technology' => '96ceb4',
            'Entertainment' => 'feca57',
            'Sports' => 'ff9ff3',
            'Science' => '54a0ff',
            'Health' => '5f27cd'
        ];
        
        $color = $colors[$category] ?? '667eea';
        $encodedCategory = urlencode($category);
        
        // Use a placeholder service
        return "https://via.placeholder.com/400x200/{$color}/ffffff?text={$encodedCategory}";
    }
    
    private function formatDate($dateString) {
        try {
            $date = new DateTime($dateString);
            return $date->format('M j, Y g:i A');
        } catch (Exception $e) {
            return date('M j, Y g:i A');
        }
    }
    
    public function getAllCategories($limit = 10) {
        $allNews = [];
        
        foreach ($this->categories as $key => $category) {
            $result = $this->fetchRSSFeed($key, $limit);
            if ($result['success']) {
                $allNews[$key] = [
                    'name' => $category['name'],
                    'articles' => $result['articles']
                ];
            }
        }
        
        return $allNews;
    }
}