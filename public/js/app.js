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

    // === Search ===
    const searchInput = document.getElementById('searchInput');
    const searchDropdown = document.getElementById('searchDropdown');
    const searchResults = document.getElementById('searchResults');
    const searchEmpty = document.getElementById('searchEmpty');

    if (searchInput) {
        let searchTimer = null;

        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimer);
            const q = this.value.trim();

            if (q.length < 2) {
                searchDropdown.classList.remove('show');
                return;
            }

            searchTimer = setTimeout(() => {
                doSearch(q);
            }, 300);
        });

        searchInput.addEventListener('focus', function() {
            if (this.value.trim().length >= 2 && searchResults.children.length > 0) {
                searchDropdown.classList.add('show');
            }
        });

        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                searchDropdown.classList.remove('show');
                this.blur();
            }
        });
    }

    function doSearch(q) {
        fetch(BASE_URL + '/home/search?q=' + encodeURIComponent(q), {
            credentials: 'same-origin'
        })
        .then(r => r.json())
        .then(data => {
            renderSearchResults(data.results);
        })
        .catch(() => {
            searchResults.innerHTML = '';
            searchEmpty.querySelector('span').textContent = 'Lỗi kết nối. Thử lại.';
            searchEmpty.style.display = 'flex';
            searchDropdown.classList.add('show');
        });
    }

    function renderSearchResults(results) {
        searchResults.innerHTML = '';

        const groups = [
            { key: 'topics',       label: 'Khóa học',  icon: 'fa-book-open',  itemIcon: 'fa-book-open',  iconClass: 'icon-topic' },
            { key: 'vocabularies', label: 'Từ vựng',   icon: 'fa-font',       itemIcon: 'fa-font',       iconClass: 'icon-vocab' },
            { key: 'tests',        label: 'Bài kiểm tra', icon: 'fa-clipboard-check', itemIcon: 'fa-clipboard-check', iconClass: 'icon-test' },
            { key: 'lessons',      label: 'Bài học',    icon: 'fa-book',       itemIcon: 'fa-book',       iconClass: 'icon-lesson' },
        ];

        let hasResults = false;

        groups.forEach(group => {
            const items = results[group.key];
            if (!items || items.length === 0) return;
            hasResults = true;

            const groupEl = document.createElement('div');
            groupEl.className = 'search-group';
            groupEl.innerHTML = '<div class="search-group-label"><i class="fas ' + group.icon + '"></i> ' + group.label + '</div>';

            items.forEach(item => {
                const link = buildSearchLink(item);
                const itemEl = document.createElement('a');
                itemEl.className = 'search-result-item';
                itemEl.href = link;
                itemEl.innerHTML =
                    '<span class="result-icon ' + group.iconClass + '"><i class="fas ' + group.itemIcon + '"></i></span>' +
                    '<span class="result-info">' +
                        '<span class="result-title">' + escapeHtml(item.title) + '</span>' +
                        '<span class="result-meta">' + escapeHtml(item.topic_name || item.description || '') + '</span>' +
                    '</span>' +
                    (item.level ? '<span class="result-badge topic-level level-' + item.level + '">' + escapeHtml(item.level) + '</span>' : '');
                groupEl.appendChild(itemEl);
            });

            searchResults.appendChild(groupEl);
        });

        if (hasResults) {
            searchEmpty.style.display = 'none';
            searchResults.style.display = 'block';
        } else {
            searchResults.style.display = 'none';
            searchEmpty.querySelector('span').textContent = 'Không tìm thấy kết quả nào';
            searchEmpty.style.display = 'flex';
        }

        searchDropdown.classList.add('show');
    }

    function buildSearchLink(item) {
        switch (item.type) {
            case 'topic':
                return BASE_URL + '/course';
            case 'vocab':
                return BASE_URL + '/course/show/' + item.topic_id;
            case 'test':
                return BASE_URL + '/test/take/' + item.id;
            case 'lesson':
                return BASE_URL + '/course/learn/' + item.topic_id;
            default:
                return BASE_URL + '/course';
        }
    }

    function escapeHtml(str) {
        if (!str) return '';
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    // Close search dropdown on click outside
    document.addEventListener('click', function(e) {
        if (searchDropdown && !e.target.closest('.nav-search')) {
            searchDropdown.classList.remove('show');
        }
    });
});

/**
 * Toggle password visibility
 * @param {string} inputId 
 */
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    if (!input) return;
    const icon = input.parentElement.querySelector('.password-toggle i');
    if (!icon) return;
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}
