/**
 * CoursePlayer — Quản lý trải nghiệm học trong trang khóa học
 * Xử lý: load bài học, navigation, mark complete, quiz, sidebar, notes, discussion, reviews
 */
window.CoursePlayer = (function () {
    var DATA  = window.COURSE_DATA || {};
    var state = {
        courseId:           DATA.courseId,
        currentLessonId:    null,
        completedIds:       new Set(DATA.completedLessonIds || []),
        lessonMap:          {},
        lessonListFlat:     [],
        prevLessonId:       null,
        nextLessonId:       null,
        _initialized:       false,
    };

    function init() {
        if (state._initialized) return;
        state._initialized = true;

        var list = DATA.lessonList || [];
        state.lessonListFlat = list;
        list.forEach(function (l, i) { state.lessonMap[l.id] = i; });

        restoreSidebarState();

        // Wire sidebar lesson clicks
        document.querySelectorAll('.sidebar-item[data-type="lesson"]').forEach(function (item) {
            item.addEventListener('click', function () {
                loadLesson(parseInt(this.dataset.id));
            });
        });

        // Wire sidebar quiz clicks
        document.querySelectorAll('.sidebar-item[data-type="quiz"]').forEach(function (item) {
            item.addEventListener('click', function () {
                loadQuiz(parseInt(this.dataset.id));
            });
        });

        // Fallback cho :has() selector (not supported in older browsers)
        document.addEventListener('change', function (e) {
            if (e.target.matches('.quiz-option input[type="radio"]')) {
                var label = e.target.closest('.quiz-option');
                if (!label) return;
                // Remove .checked from all sibling labels
                var form = label.closest('.quiz-options');
                if (form) form.querySelectorAll('.quiz-option').forEach(function (l) { l.classList.remove('checked'); });
                label.classList.add('checked');
            }
        });

        // Course search
        var searchInput = document.getElementById('courseSearch');
        if (searchInput) {
            var searchTimer;
            searchInput.addEventListener('input', function () {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function () { filterSidebar(searchInput.value); }, 300);
            });
        }

        // Animate progress ring
        animateProgressRing(DATA.completionPercent || 0);

        // Update sidebar
        updateSidebarCompletion();
        updateChapterProgressBars();
        loadAllNotesIndicators();

        // Handle browser back/forward
        window.addEventListener('popstate', function (e) {
            if (e.state && e.state.lessonId) {
                loadLesson(e.state.lessonId, true);
            }
        });
    }

    // ─── Lesson Loading ───────────────────────────────────────────

    async function loadLesson(lessonId, silent) {
        if (!silent) showLoading();

        // Update sidebar active
        document.querySelectorAll('.sidebar-item').forEach(function (i) { i.classList.remove('active'); });
        var sidebarItem = document.querySelector('.sidebar-item[data-type="lesson"][data-id="' + lessonId + '"]');
        if (sidebarItem) {
            sidebarItem.classList.add('active');
            scrollSidebarToItem(sidebarItem);
        }

        try {
            var res  = await fetch(DATA.baseUrl + '/course/loadLesson/' + lessonId);
            var data = await res.json();

            if (!data.success) { showError(data.error || 'Không thể tải bài học.'); return; }

            // Inject into courseContent
            var display = document.getElementById('courseContent');
            if (display) display.innerHTML = data.html;

            // Hide overview/resume
            var resume   = document.getElementById('contentResume');
            var overview = document.getElementById('contentOverview');
            var loading  = document.getElementById('contentLoading');
            if (resume) resume.style.display = 'none';
            if (overview) overview.style.display = 'none';
            if (loading) loading.style.display = 'none';

            // Update state
            state.currentLessonId = data.lesson_id;
            state.prevLessonId    = data.prev_lesson_id;
            state.nextLessonId    = data.next_lesson_id;
            if (data.is_completed) state.completedIds.add(data.lesson_id);

            updateNavBar();
            updateSidebarCompletion();
            updateSidebarActive();
            updateChapterProgressBars();
            enhanceMediaPlayers();

            if (!silent) {
                window.history.pushState({ lessonId: lessonId }, '', DATA.baseUrl + '/course/show/' + state.courseId + '?lesson=' + lessonId);
            }
        } catch (e) {
            console.error('Failed to load lesson:', e);
            showError('Lỗi kết nối. Vui lòng thử lại.');
        }
    }

    function navigateLesson(direction) {
        var targetId = direction === 'next' ? state.nextLessonId : state.prevLessonId;
        if (targetId) loadLesson(targetId);
    }

    // ─── Completion Toggle ────────────────────────────────────────

    async function toggleComplete(lessonId, courseId) {
        try {
            var res  = await fetch(DATA.baseUrl + '/course/toggleLessonComplete', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ lesson_id: lessonId, course_id: courseId || state.courseId }),
            });
            var data = await res.json();
            if (!data.success) return;

            if (data.completed) {
                state.completedIds.add(lessonId);
                showToast('Đã hoàn thành! +10 XP', 'success');
            } else {
                state.completedIds.delete(lessonId);
                showToast('Đã bỏ đánh dấu hoàn thành', 'info');
            }

            updateSidebarCompletion();
            updateChapterProgressBars();
            updateNavCompleteBtn();
            animateProgressRing(data.completion_percent);

            var completeBtn = document.querySelector('.lesson-complete-btn');
            if (completeBtn) completeBtn.classList.toggle('is-completed', data.completed);

            if (data.can_take_final) {
                showToast('Bạn đã mở khóa Bài thi cuối khóa!', 'success');
                updateFinalExamState(true);
            }
        } catch (e) {
            console.error('Toggle complete failed:', e);
        }
    }

    function toggleCurrentComplete() {
        if (state.currentLessonId) toggleComplete(state.currentLessonId, state.courseId);
    }

    // ─── Quiz ─────────────────────────────────────────────────────

    async function loadQuiz(quizId) {
        showLoading();
        try {
            var res  = await fetch(DATA.baseUrl + '/course/loadQuiz/' + quizId);
            var data = await res.json();

            if (!data.success && data.redirect) { window.location.href = data.redirect; return; }
            if (!data.success) { showError(data.error || 'Không thể tải quiz.'); return; }

            var display = document.getElementById('courseContent');
            if (display) display.innerHTML = data.html;

            // Enhance audio players in quiz
            enhanceMediaPlayers();

            // Hide overview/resume/loading
            var resume   = document.getElementById('contentResume');
            var overview = document.getElementById('contentOverview');
            var loading  = document.getElementById('contentLoading');
            if (resume) resume.style.display = 'none';
            if (overview) overview.style.display = 'none';
            if (loading) loading.style.display = 'none';

            var navBar = document.getElementById('lessonNavBar');
            if (navBar) navBar.style.display = 'none';

            initQuizTimer();

            document.querySelectorAll('.sidebar-item').forEach(function (i) { i.classList.remove('active'); });
            var sidebarItem = document.querySelector('.sidebar-item[data-type="quiz"][data-id="' + quizId + '"]');
            if (sidebarItem) { sidebarItem.classList.add('active'); scrollSidebarToItem(sidebarItem); }

        } catch (e) {
            console.error('Failed to load quiz:', e);
            showError('Lỗi kết nối. Vui lòng thử lại.');
        }
    }

    async function submitQuiz() {
        var form = document.getElementById('quizForm');
        if (!form) return;

        var testIdInput = form.querySelector('[name="test_id"]');
        if (!testIdInput) { showError('Lỗi: Không tìm thấy mã bài kiểm tra.'); return; }
        var testId  = parseInt(testIdInput.value);
        var answers = {};

        form.querySelectorAll('[name^="q["]').forEach(function (input) {
            var match = input.name.match(/q\[(\d+)\]/);
            if (match) {
                if (input.type === 'radio') {
                    if (input.checked) answers[match[1]] = input.value;
                } else {
                    answers[match[1]] = input.value;
                }
            }
        });

        var submitBtn = form.querySelector('.quiz-actions button[type="button"]');
        if (submitBtn) { submitBtn.disabled = true; submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang nộp...'; }

        try {
            var res  = await fetch(DATA.baseUrl + '/course/submitQuiz', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ test_id: testId, answers: answers }),
            });
            var data = await res.json();
            if (!data.success) { showError(data.error || 'Lỗi khi nộp bài.'); return; }

            showQuizResult(data);

            if (data.passed) {
                var quizItem = document.querySelector('.sidebar-item[data-type="quiz"][data-id="' + testId + '"]');
                if (quizItem) quizItem.classList.add('done');
                updateChapterProgressBars();
            }

            if (data.course_completed) {
                showToast('Chúc mừng! Bạn đã hoàn thành khóa học!', 'success');
                if (data.certificate) {
                    setTimeout(function () {
                        if (confirm('Bạn đã nhận được ' + data.certificate + '! Xem chứng chỉ ngay?')) {
                            window.location.href = DATA.baseUrl + '/course/certificate/' + state.courseId;
                        }
                    }, 1500);
                }
            }
        } catch (e) {
            console.error('Submit quiz failed:', e);
            showError('Lỗi kết nối. Vui lòng thử lại.');
        }
    }

    function showQuizResult(data) {
        var container = document.getElementById('quizResult');
        var quizForm = document.getElementById('quizForm');
        if (!container) return;

        if (quizForm) quizForm.style.display = 'none';
        var passed = data.passed;
        var pct    = data.percentage;

        container.style.display = 'block';
        var html = '<div class="quiz-result-card ' + (passed ? 'quiz-passed' : 'quiz-failed') + '">';
        html += '<div class="quiz-result-icon">' + (passed ? '<i class="fas fa-trophy"></i>' : '<i class="fas fa-redo"></i>') + '</div>';
        html += '<h3>' + (passed ? 'Chúc mừng! Bạn đã vượt qua!' : 'Chưa đạt') + '</h3>';
        html += '<div class="quiz-result-score"><span class="quiz-score-big">' + pct + '%</span>';
        html += '<span class="quiz-score-detail">' + data.score + '/' + data.total + ' điểm</span></div>';
        html += '<p>Bạn cần đạt 70% để qua.</p>';
        html += '<div class="quiz-result-actions">';
        if (!passed) html += '<button class="btn btn-primary" onclick="CoursePlayer.retryQuiz()"><i class="fas fa-redo"></i> Làm lại</button>';
        if (data.course_completed) {
            html += '<a href="' + DATA.baseUrl + '/course/certificate/' + state.courseId + '" class="btn btn-primary"><i class="fas fa-certificate"></i> Xem chứng chỉ</a>';
        } else {
            html += '<button class="btn btn-outline" onclick="CoursePlayer.loadNextAfterQuiz()"><i class="fas fa-arrow-right"></i> Tiếp tục học</button>';
        }
        html += '</div></div>';
        container.innerHTML = html;
    }

    function retryQuiz() {
        var quizResult = document.getElementById('quizResult');
        var quizForm   = document.getElementById('quizForm');
        if (quizResult) quizResult.style.display = 'none';
        if (quizForm) quizForm.style.display = '';
    }

    function loadNextAfterQuiz() {
        if (state.nextLessonId) {
            loadLesson(state.nextLessonId);
        } else {
            var display = document.getElementById('courseContent');
            if (display) display.innerHTML = '<div class="content-welcome"><i class="fas fa-check-circle" style="color:#22c55e"></i><h2>Bạn đã hoàn thành tất cả bài học!</h2><p>Hãy làm bài thi cuối khóa để nhận chứng chỉ.</p></div>';
        }
    }

    // ─── Quiz Timer ───────────────────────────────────────────────

    var quizTimerInterval = null;

    function initQuizTimer() {
        if (quizTimerInterval) clearInterval(quizTimerInterval);
        var quizDiv = document.querySelector('.quiz-inline');
        var timerEl = document.getElementById('timerDisplay');
        if (!quizDiv || !timerEl) return;

        var totalMin = parseInt(quizDiv.dataset.timer) || 0;
        if (totalMin <= 0) return;

        var remaining = totalMin * 60;

        function update() {
            var m = Math.floor(remaining / 60);
            var s = remaining % 60;
            timerEl.textContent = m + ':' + String(s).padStart(2, '0');
            if (remaining <= 300) timerEl.style.color = '#ef4444';
            if (remaining <= 0) { clearInterval(quizTimerInterval); submitQuiz(); }
            remaining--;
        }
        update();
        quizTimerInterval = setInterval(update, 1000);
    }

    // ─── Sidebar Management ───────────────────────────────────────

    function toggleWeek(header) {
        var items = header.nextElementSibling;
        if (!items) return;
        var isOpen = items.style.display !== 'none';
        items.style.display = isOpen ? 'none' : 'block';
        header.parentElement.classList.toggle('open', !isOpen);
        saveSidebarState();
    }

    function saveSidebarState() {
        try {
            var openWeeks = [];
            document.querySelectorAll('.sidebar-week').forEach(function (week, i) {
                var items = week.querySelector('.sidebar-week-items');
                if (items && items.style.display !== 'none') openWeeks.push(i);
            });
            localStorage.setItem('course_sidebar_' + state.courseId, JSON.stringify(openWeeks));
        } catch (e) { /* ignore */ }
    }

    function restoreSidebarState() {
        try {
            var saved = localStorage.getItem('course_sidebar_' + state.courseId);
            if (!saved) return;
            var openWeeks = JSON.parse(saved);
            var weeks = document.querySelectorAll('.sidebar-week');
            openWeeks.forEach(function (idx) {
                if (weeks[idx]) {
                    var items = weeks[idx].querySelector('.sidebar-week-items');
                    if (items) { items.style.display = 'block'; weeks[idx].classList.add('open'); }
                }
            });
        } catch (e) { /* ignore */ }
    }

    function scrollSidebarToItem(item) {
        item.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function updateSidebarCompletion() {
        document.querySelectorAll('.sidebar-item[data-type="lesson"]').forEach(function (item) {
            var id = parseInt(item.dataset.id);
            if (state.completedIds.has(id)) {
                item.classList.add('done');
                var icon = item.querySelector('.sidebar-item-icon');
                if (icon) icon.innerHTML = '<i class="fas fa-check-circle"></i>';
            } else {
                item.classList.remove('done');
                var icon = item.querySelector('.sidebar-item-icon');
                if (icon) icon.innerHTML = '<i class="fas fa-circle"></i>';
            }
        });
    }

    function updateSidebarActive() {
        document.querySelectorAll('.sidebar-item').forEach(function (i) { i.classList.remove('active'); });
        if (state.currentLessonId) {
            var item = document.querySelector('.sidebar-item[data-id="' + state.currentLessonId + '"]');
            if (item) item.classList.add('active');
        }
    }

    function updateChapterProgressBars() {
        document.querySelectorAll('.sidebar-week').forEach(function (week) {
            var items = week.querySelectorAll('.sidebar-item[data-type="lesson"]');
            var completed = 0;
            items.forEach(function (item) {
                if (state.completedIds.has(parseInt(item.dataset.id))) completed++;
            });
            var pct = items.length > 0 ? Math.round(completed / items.length * 100) : 0;
            var fill = week.querySelector('.week-mini-fill');
            if (fill) fill.style.width = pct + '%';
        });
    }

    function updateFinalExamState(available) {
        var finalWeek = document.querySelector('.sidebar-final');
        if (!finalWeek) return;
        if (available) {
            finalWeek.classList.add('current');
            var indicator = finalWeek.querySelector('.week-indicator');
            if (indicator) indicator.innerHTML = '<i class="fas fa-star"></i>';
        }
    }

    function filterSidebar(query) {
        var q = query.toLowerCase().trim();
        document.querySelectorAll('.sidebar-item').forEach(function (item) {
            var title = (item.dataset.title || '').toLowerCase();
            if (!q) { item.style.display = ''; }
            else if (title.indexOf(q) !== -1) {
                item.style.display = '';
                var items = item.closest('.sidebar-week-items');
                if (items) items.style.display = 'block';
            } else { item.style.display = 'none'; }
        });
        document.querySelectorAll('.sidebar-week').forEach(function (week) {
            var visible = week.querySelectorAll('.sidebar-item:not([style*="display: none"])');
            var total   = week.querySelectorAll('.sidebar-item');
            if (!q) { week.style.display = ''; }
            else if (visible.length === 0 && total.length > 0) { week.style.display = 'none'; }
            else { week.style.display = ''; }
        });
    }

    function toggleMobileSidebar() {
        var sidebar = document.getElementById('courseSidebar');
        if (sidebar) sidebar.classList.toggle('mobile-open');
    }

    // ─── Navigation Bar ───────────────────────────────────────────

    function updateNavBar() {
        var navBar  = document.getElementById('lessonNavBar');
        var prevBtn = document.getElementById('prevLessonBtn');
        var nextBtn = document.getElementById('nextLessonBtn');
        if (!navBar) return;
        navBar.style.display = 'flex';
        if (prevBtn) prevBtn.disabled = !state.prevLessonId;
        if (nextBtn) nextBtn.disabled = !state.nextLessonId;
        updateNavCompleteBtn();
    }

    function updateNavCompleteBtn() {
        var btn = document.getElementById('navCompleteBtn');
        if (!btn) return;
        var isComplete = state.currentLessonId && state.completedIds.has(state.currentLessonId);
        btn.classList.toggle('is-completed', isComplete);
    }

    // ─── Progress Ring Animation ──────────────────────────────────

    function animateProgressRing(targetPercent) {
        var circle = document.getElementById('progressCircle');
        var text   = document.getElementById('progressPercent');
        if (!circle) return;

        var duration = 1200;
        var start    = performance.now();

        function step(now) {
            var elapsed = now - start;
            var progress = Math.min(elapsed / duration, 1);
            var eased = 1 - Math.pow(1 - progress, 3);
            var current = Math.round(targetPercent * eased);
            circle.setAttribute('stroke-dasharray', current + ', 100');
            if (text) text.textContent = current + '%';
            if (progress < 1) requestAnimationFrame(step);
        }
        requestAnimationFrame(step);
    }

    // ─── Media Player Enhancements ────────────────────────────────

    function enhanceMediaPlayers() {
        document.querySelectorAll('video[data-enhanced="true"], audio[data-enhanced="true"]').forEach(function (el) {
            if (el.dataset.enhancedDone) return;
            el.dataset.enhancedDone = '1';

            var wrapper = document.createElement('div');
            wrapper.className = 'enhanced-media-wrapper';
            el.parentNode.insertBefore(wrapper, el);
            wrapper.appendChild(el);

            var speedCtrl = document.createElement('div');
            speedCtrl.className = 'media-speed-control';
            var speeds = ['0.5', '1', '1.25', '1.5', '2'];
            speeds.forEach(function (s) {
                var btn = document.createElement('button');
                btn.className = 'speed-btn' + (s === '1' ? ' active' : '');
                btn.dataset.speed = s;
                btn.textContent = s + 'x';
                btn.addEventListener('click', function () {
                    el.playbackRate = parseFloat(s);
                    speedCtrl.querySelectorAll('.speed-btn').forEach(function (b) { b.classList.remove('active'); });
                    btn.classList.add('active');
                });
                speedCtrl.appendChild(btn);
            });
            wrapper.appendChild(speedCtrl);
        });
    }

    // ─── TTS cho Listening Test ──────────────────────────────────

    function speakPassage(btn) {
        if (!('speechSynthesis' in window)) {
            showToast('Trình duyệt không hỗ trợ đọc văn bản.', 'error');
            return;
        }
        var passage = btn.dataset.passage;
        if (!passage) return;

        if (window.speechSynthesis.speaking) {
            window.speechSynthesis.cancel();
            btn.innerHTML = '<i class="fas fa-volume-up"></i> Nghe';
            return;
        }

        var utterance = new SpeechSynthesisUtterance(passage);
        utterance.lang = 'en-US';
        utterance.rate = 0.9;
        utterance.pitch = 1;

        // Chọn giọng đọc tiếng Anh tự nhiên nhất
        var voices = window.speechSynthesis.getVoices();
        var best = null;
        for (var i = 0; i < voices.length; i++) {
            var v = voices[i];
            if (v.lang.indexOf('en') !== 0) continue;
            // Ưu tiên cao nhất: giọng Neural/Online (Microsoft Edge)
            if (v.name.indexOf('Online') !== -1 || v.name.indexOf('Natural') !== -1) {
                best = v; break;
            }
            // Ưu tiên nhì: Google voices
            if (v.name.indexOf('Google') !== -1 && !best) best = v;
            // Ưu tiên ba: Microsoft voices
            if (v.name.indexOf('Microsoft') !== -1 && !best) best = v;
            // Fallback: bất kỳ giọng en-US nào
            if (!best && v.lang === 'en-US') best = v;
            if (!best) best = v;
        }
        if (best) utterance.voice = best;

        utterance.onend = function () {
            btn.innerHTML = '<i class="fas fa-volume-up"></i> Nghe';
        };
        utterance.onerror = function () {
            btn.innerHTML = '<i class="fas fa-volume-up"></i> Nghe';
        };

        btn.innerHTML = '<i class="fas fa-stop"></i> Dừng';
        window.speechSynthesis.speak(utterance);
    }

    // ─── Toast Notifications ──────────────────────────────────────

    function showToast(message, type) {
        var container = document.getElementById('toastContainer');
        if (!container) return;

        var icons = { success: 'fa-check-circle', info: 'fa-info-circle', error: 'fa-exclamation-circle' };
        var toast = document.createElement('div');
        toast.className = 'course-toast toast-' + type;
        toast.innerHTML = '<i class="fas ' + (icons[type] || icons.info) + '"></i> ' + message;
        container.appendChild(toast);

        requestAnimationFrame(function () { toast.classList.add('show'); });
        setTimeout(function () { toast.classList.remove('show'); setTimeout(function () { toast.remove(); }, 400); }, 3500);
    }

    // ─── Loading / Error States ───────────────────────────────────

    function showLoading() {
        var resume   = document.getElementById('contentResume');
        var overview = document.getElementById('contentOverview');
        var loading  = document.getElementById('contentLoading');

        if (resume) resume.style.display = 'none';
        if (overview) overview.style.display = 'none';
        if (loading) loading.style.display = 'flex';

        // Clear dynamic content
        var display = document.getElementById('courseContent');
        if (display) display.innerHTML = '';
    }

    function showError(message) {
        var loading = document.getElementById('contentLoading');
        if (loading) loading.style.display = 'none';

        var display = document.getElementById('courseContent');
        if (display) {
            // Overlay error on top of existing content instead of destroying it
            var existing = display.querySelector('.content-error');
            if (existing) existing.remove();
            var errorDiv = document.createElement('div');
            errorDiv.className = 'content-error';
            errorDiv.style.display = 'block';
            errorDiv.innerHTML = '<i class="fas fa-exclamation-triangle"></i><p>' + message + '</p><button class="btn btn-outline" onclick="this.parentElement.remove()">Đóng</button>';
            display.appendChild(errorDiv);
        }
    }

    // ─── Note-taking (localStorage) ────────────────────────────────

    function toggleNotes(lessonId) {
        var body = document.getElementById('notesBody-' + lessonId);
        if (!body) return;
        var isOpen = body.style.display !== 'none';
        body.style.display = isOpen ? 'none' : 'block';
        if (!isOpen) {
            var textarea = document.getElementById('notesText-' + lessonId);
            if (textarea) textarea.value = localStorage.getItem('course_notes_' + state.courseId + '_' + lessonId) || '';
        }
    }

    function saveNotes(lessonId, text) {
        var key = 'course_notes_' + state.courseId + '_' + lessonId;
        localStorage.setItem(key, text);
        var saved = document.getElementById('notesSaved-' + lessonId);
        if (saved) {
            saved.style.opacity = '1';
            clearTimeout(saved._timeout);
            saved._timeout = setTimeout(function () { saved.style.opacity = '0'; }, 1500);
        }
        updateNotesIndicator(lessonId, text.length > 0);
    }

    function updateNotesIndicator(lessonId, hasNotes) {
        var item = document.querySelector('.sidebar-item[data-type="lesson"][data-id="' + lessonId + '"]');
        if (!item) return;
        var indicator = item.querySelector('.notes-indicator');
        if (hasNotes) {
            if (!indicator) {
                indicator = document.createElement('span');
                indicator.className = 'notes-indicator';
                indicator.innerHTML = '<i class="fas fa-pencil-alt"></i>';
                var textEl = item.querySelector('.sidebar-item-text');
                if (textEl) textEl.appendChild(indicator);
            }
        } else {
            if (indicator) indicator.remove();
        }
    }

    function loadAllNotesIndicators() {
        document.querySelectorAll('.sidebar-item[data-type="lesson"]').forEach(function (item) {
            var lessonId = parseInt(item.dataset.id);
            var key = 'course_notes_' + state.courseId + '_' + lessonId;
            var notes = localStorage.getItem(key);
            if (notes && notes.trim()) updateNotesIndicator(lessonId, true);
        });
    }

    // ─── Discussion ───────────────────────────────────────────────

    async function loadDiscussion(lessonId) {
        var loader    = document.getElementById('discussionLoader-' + lessonId);
        var container = document.getElementById('discussionContainer-' + lessonId);
        if (!container) return;
        if (loader) loader.style.display = 'none';
        container.style.display = 'block';
        container.innerHTML = '<div class="discussion-section"><div style="text-align:center;padding:24px"><div class="loading-spinner"></div></div></div>';

        try {
            var res  = await fetch(DATA.baseUrl + '/course/loadDiscussion/' + lessonId);
            var data = await res.json();
            container.innerHTML = data.success ? data.html : '<p class="discussion-error">Không thể tải thảo luận.</p>';
        } catch (e) {
            container.innerHTML = '<p class="discussion-error">Lỗi kết nối.</p>';
        }
    }

    async function postComment(lessonId) {
        var input = document.getElementById('discussionInput-' + lessonId);
        if (!input) return;
        var content = input.value.trim();
        if (!content) return;

        try {
            var res  = await fetch(DATA.baseUrl + '/course/postComment', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ lesson_id: lessonId, content: content }),
            });
            var data = await res.json();
            if (data.success) { loadDiscussion(lessonId); showToast('Bình luận đã được gửi!', 'success'); }
            else { showToast(data.error || 'Lỗi khi gửi bình luận', 'error'); }
        } catch (e) { showToast('Lỗi kết nối.', 'error'); }
    }

    function showReplyForm(commentId) {
        var form = document.getElementById('replyForm-' + commentId);
        if (form) {
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
            var textarea = form.querySelector('textarea');
            if (textarea && form.style.display === 'block') textarea.focus();
        }
    }

    async function postReply(parentId, btn) {
        var form     = btn.closest('.discussion-reply-form');
        var content  = form.querySelector('textarea').value.trim();
        if (!content) return;

        var wrapper  = btn.closest('.discussion-section');
        var match    = wrapper && wrapper.id ? wrapper.id.match(/discussion-(\d+)/) : null;
        var lessonId = match ? parseInt(match[1]) : null;
        if (!lessonId) return;

        try {
            var res  = await fetch(DATA.baseUrl + '/course/postComment', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ lesson_id: lessonId, content: content, parent_id: parentId }),
            });
            var data = await res.json();
            if (data.success) { loadDiscussion(lessonId); showToast('Trả lời đã được gửi!', 'success'); }
            else { showToast(data.error || 'Lỗi khi gửi trả lời', 'error'); }
        } catch (e) { showToast('Lỗi kết nối.', 'error'); }
    }

    // ─── Course Reviews ────────────────────────────────────────────

    var _reviewRating = 0;

    async function loadReviews(courseId) {
        var container = document.getElementById('reviewsContainer');
        if (!container) return;
        if (container.style.display === 'block') { container.style.display = 'none'; return; }
        container.style.display = 'block';
        container.innerHTML = '<div style="text-align:center;padding:24px"><div class="loading-spinner"></div></div>';

        try {
            var res  = await fetch(DATA.baseUrl + '/course/reviews/' + courseId);
            var data = await res.json();
            if (data.success) {
                container.innerHTML = data.html;
                _reviewRating = data.my_review ? data.my_review.rating : 0;
            }
        } catch (e) { container.innerHTML = '<p style="text-align:center;color:#ef4444">Lỗi tải đánh giá.</p>'; }
    }

    function setRating(star, courseId) {
        _reviewRating = star;
        var input = document.getElementById('reviewStarsInput');
        if (input) {
            input.querySelectorAll('.star-input').forEach(function (s, i) {
                if (i < star) s.classList.add('active'); else s.classList.remove('active');
            });
        }
        autoSaveReview(courseId);
    }

    function autoSaveReview(courseId) {
        // Save review draft to localStorage
        var textarea = document.getElementById('reviewText');
        if (textarea) {
            localStorage.setItem('review_draft_' + courseId, textarea.value);
        }
        var el = document.getElementById('reviewSaved');
        if (el) {
            el.style.display = 'inline';
            clearTimeout(window._reviewSaveTimeout);
            window._reviewSaveTimeout = setTimeout(function () { el.style.display = 'none'; }, 2000);
        }
    }

    async function submitReview(courseId) {
        if (_reviewRating === 0) { showToast('Vui lòng chọn số sao đánh giá.', 'error'); return; }
        var text = document.getElementById('reviewText');
        text = text ? text.value : '';

        try {
            var res  = await fetch(DATA.baseUrl + '/course/submitReview', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ course_id: courseId, rating: _reviewRating, review_text: text }),
            });
            var data = await res.json();
            if (data.success) { showToast('Cảm ơn bạn đã đánh giá!', 'success'); loadReviews(courseId); }
            else { showToast(data.error || 'Lỗi gửi đánh giá.', 'error'); }
        } catch (e) { showToast('Lỗi kết nối.', 'error'); }
    }

    // ═══════════════════════════════════════════════════════════════
    // INIT & PUBLIC API
    // ═══════════════════════════════════════════════════════════════

    init();

    return {
        loadLesson:     loadLesson,
        navigateLesson: navigateLesson,
        toggleComplete: toggleComplete,
        toggleCurrentComplete: toggleCurrentComplete,
        loadQuiz:       loadQuiz,
        submitQuiz:     submitQuiz,
        retryQuiz:      retryQuiz,
        loadNextAfterQuiz: loadNextAfterQuiz,
        toggleWeek:     toggleWeek,
        toggleMobileSidebar: toggleMobileSidebar,
        toggleNotes:    toggleNotes,
        saveNotes:      saveNotes,
        loadDiscussion: loadDiscussion,
        postComment:    postComment,
        showReplyForm:  showReplyForm,
        postReply:      postReply,
        loadReviews:    loadReviews,
        setRating:      setRating,
        autoSaveReview: autoSaveReview,
        submitReview:   submitReview,
        speakPassage:   speakPassage,
        init:           init,
    };
})();
