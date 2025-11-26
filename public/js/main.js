/**
 * CouponHub - Main JavaScript
 */

// DOM Ready
document.addEventListener('DOMContentLoaded', function() {
    initTheme();
    initTooltips();
    initLazyLoading();
});

// Theme handling
function initTheme() {
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark' || (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark');
    }
}

// Tooltip initialization
function initTooltips() {
    const tooltips = document.querySelectorAll('[data-tooltip]');
    tooltips.forEach(el => {
        el.addEventListener('mouseenter', function() {
            const tooltip = document.createElement('div');
            tooltip.className = 'absolute bg-gray-800 text-white text-xs px-2 py-1 rounded z-50';
            tooltip.textContent = this.dataset.tooltip;
            tooltip.style.bottom = '100%';
            tooltip.style.left = '50%';
            tooltip.style.transform = 'translateX(-50%)';
            tooltip.style.marginBottom = '5px';
            this.style.position = 'relative';
            this.appendChild(tooltip);
        });
        el.addEventListener('mouseleave', function() {
            const tooltip = this.querySelector('.absolute');
            if (tooltip) tooltip.remove();
        });
    });
}

// Lazy loading for images
function initLazyLoading() {
    if ('loading' in HTMLImageElement.prototype) {
        const images = document.querySelectorAll('img[data-src]');
        images.forEach(img => {
            img.src = img.dataset.src;
        });
    } else {
        // Fallback for older browsers
        const script = document.createElement('script');
        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/lozad.js/1.16.0/lozad.min.js';
        script.onload = function() {
            const observer = lozad();
            observer.observe();
        };
        document.body.appendChild(script);
    }
}

// Copy to clipboard function
function copyToClipboard(text, successCallback) {
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text).then(function() {
            if (successCallback) successCallback();
        });
    } else {
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = text;
        textArea.style.position = 'fixed';
        textArea.style.left = '-999999px';
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        try {
            document.execCommand('copy');
            if (successCallback) successCallback();
        } catch (err) {
            console.error('Failed to copy:', err);
        }
        textArea.remove();
    }
}

// Show toast notification
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `fixed bottom-5 right-5 px-6 py-3 rounded-lg text-white font-medium z-50 ${type === 'success' ? 'bg-green-500' : 'bg-red-500'}`;
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transition = 'opacity 0.3s';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Debounce function for search
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Format number with commas
function formatNumber(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

// Countdown timer
function startCountdown(elementId, endDate) {
    const countdown = document.getElementById(elementId);
    if (!countdown) return;
    
    const end = new Date(endDate).getTime();
    
    const timer = setInterval(function() {
        const now = new Date().getTime();
        const distance = end - now;
        
        if (distance < 0) {
            clearInterval(timer);
            countdown.innerHTML = '<span class="text-red-500">Expired</span>';
            return;
        }
        
        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
        
        countdown.querySelector('#days').textContent = days;
        countdown.querySelector('#hours').textContent = hours;
        countdown.querySelector('#minutes').textContent = minutes;
        countdown.querySelector('#seconds').textContent = seconds;
    }, 1000);
}

// Smooth scroll to element
function smoothScrollTo(elementId) {
    const element = document.getElementById(elementId);
    if (element) {
        element.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

// Form validation helper
function validateForm(formId, rules) {
    const form = document.getElementById(formId);
    if (!form) return false;
    
    let isValid = true;
    const errors = [];
    
    for (const [field, rule] of Object.entries(rules)) {
        const input = form.querySelector(`[name="${field}"]`);
        if (!input) continue;
        
        const value = input.value.trim();
        
        if (rule.required && !value) {
            errors.push(`${rule.label || field} is required`);
            isValid = false;
            input.classList.add('border-red-500');
        } else if (rule.email && value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
            errors.push(`Please enter a valid email address`);
            isValid = false;
            input.classList.add('border-red-500');
        } else if (rule.minLength && value.length < rule.minLength) {
            errors.push(`${rule.label || field} must be at least ${rule.minLength} characters`);
            isValid = false;
            input.classList.add('border-red-500');
        } else {
            input.classList.remove('border-red-500');
        }
    }
    
    return { isValid, errors };
}

// API helper function
async function apiCall(url, options = {}) {
    try {
        const response = await fetch(url, {
            ...options,
            headers: {
                'Content-Type': 'application/json',
                ...options.headers
            }
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        return await response.json();
    } catch (error) {
        console.error('API call failed:', error);
        throw error;
    }
}

// Track coupon click
async function trackCouponClick(couponId) {
    try {
        await fetch(`/api/coupon/click/${couponId}`, {
            method: 'POST'
        });
    } catch (error) {
        // Silently fail
    }
}

// Initialize on page load
window.CouponHub = {
    copyToClipboard,
    showToast,
    debounce,
    formatNumber,
    startCountdown,
    smoothScrollTo,
    validateForm,
    apiCall,
    trackCouponClick
};
