/**
 * app.js - Global JavaScript
 * Xử lý navigation, dropdown, flash messages
 */

document.addEventListener('DOMContentLoaded', function() {
    // === Mobile Navigation Toggle ===
    const navToggle = document.getElementById('navToggle');
    const navMenu = document.getElementById('navMenu');
    
    if (navToggle) {
        navToggle.addEventListener('click', function() {
            navMenu.classList.toggle('show');
        });
    }

    // === User Dropdown ===
    const userBtn = document.getElementById('userDropdownBtn');
    const userDropdown = document.getElementById('userDropdown');
    
    if (userBtn) {
        userBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            userDropdown.classList.toggle('show');
            // Close more menu if open
            const moreMenu = document.getElementById('navMoreMenu');
            if (moreMenu) moreMenu.classList.remove('show');
        });
    }

    // === Nav More Dropdown ===
    const moreBtn = document.getElementById('navMoreBtn');
    const moreMenu = document.getElementById('navMoreMenu');

    if (moreBtn) {
        moreBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            moreMenu.classList.toggle('show');
            // Close user dropdown if open
            if (userDropdown) userDropdown.classList.remove('show');
        });
    }

    // === Close dropdowns on click outside ===
    document.addEventListener('click', function() {
        if (userDropdown) userDropdown.classList.remove('show');
        if (moreMenu) moreMenu.classList.remove('show');
    });

    // === Auto-hide Flash Messages ===
    const flash = document.getElementById('flashMessage');
    if (flash) {
        setTimeout(() => {
            flash.style.animation = 'slideIn 0.4s ease reverse';
            setTimeout(() => flash.remove(), 400);
        }, 4000);
    }

    // === Smooth Scroll for anchor links ===
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (!href || href === '#') return; // Skip bare '#' links
            e.preventDefault();
            const target = document.querySelector(href);
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });
});

/**
 * Toggle password visibility
 * @param {string} inputId 
 */
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const icon = input.parentElement.querySelector('.password-toggle i');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}
