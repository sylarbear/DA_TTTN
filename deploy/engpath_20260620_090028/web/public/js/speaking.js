/**
 * speaking.js - Web Speech API Integration
 * Read-aloud mode: User đọc văn bản chuẩn bị sẵn, hệ thống chấm điểm
 */

// Escape HTML utility
function escHtml(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

let recognition = null;
let isRecording = false;
let fullTranscript = '';
let lastConfidence = 0.5;

// ===== FIX: Dừng audio khi chuyển trang =====
window.addEventListener('beforeunload', function() {
    speechSynthesis.cancel();
});
// Một số browser không fire beforeunload, dùng pagehide làm fallback
window.addEventListener('pagehide', function() {
    speechSynthesis.cancel();
});

// ===== Auto-init voice selector nếu có trên trang =====
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('voiceOptions') || document.getElementById('voiceDropdown')) {
        initVoiceSelector();
    }
});

/**
 * Khởi tạo Speech Recognition
 */
function initSpeechRecognition() {
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    
    if (!SpeechRecognition) {
        alert('Trình duyệt không hỗ trợ Web Speech API. Vui lòng sử dụng Chrome hoặc Edge.');
        return false;
    }

    recognition = new SpeechRecognition();
    recognition.lang = 'en-US';
    recognition.continuous = true;
    recognition.interimResults = true;
    recognition.maxAlternatives = 1;

    recognition.onresult = function(event) {
        let interimTranscript = '';
        let finalTranscript = '';

        for (let i = event.resultIndex; i < event.results.length; i++) {
            const result = event.results[i];
            if (result.isFinal) {
                finalTranscript += result[0].transcript + ' ';
                lastConfidence = result[0].confidence;
            } else {
                interimTranscript += result[0].transcript;
            }
        }

        if (finalTranscript) {
            fullTranscript += finalTranscript;
        }

        // Hiển thị transcript (escaped to prevent XSS)
        const el = document.getElementById('transcriptText');
        if (el) {
            el.innerHTML = escHtml(fullTranscript) + 
                '<span style="color: var(--text-muted); font-style: italic;">' + escHtml(interimTranscript) + '</span>';
        }
    };

    recognition.onend = function() {
        if (isRecording) {
            try { recognition.start(); } catch(e) {}
        }
    };

    recognition.onerror = function(event) {
        console.error('Speech recognition error:', event.error);
        if (event.error === 'not-allowed') {
            alert('Vui lòng cho phép truy cập microphone trong cài đặt trình duyệt.');
        } else if (event.error !== 'no-speech' && event.error !== 'aborted') {
            // no-speech và aborted là lỗi thường, không cần alert
            console.warn('Speech error (non-critical):', event.error);
        }
    };

    return true;
}

/**
 * Bật/tắt ghi âm
 */
function toggleRecording() {
    if (!recognition && !initSpeechRecognition()) return;

    if (typeof SPEAKING_CONFIG === 'undefined') {
        console.error('SPEAKING_CONFIG not defined');
        alert('Lỗi cấu hình. Vui lòng reload trang.');
        return;
    }

    const btn = document.getElementById('recordBtn');
    const status = document.getElementById('recordingStatus');
    const transcriptArea = document.getElementById('transcriptArea');

    if (!isRecording) {
        // Bắt đầu ghi âm
        isRecording = true;
        fullTranscript = '';
        lastConfidence = 0.5;
        
        btn.classList.add('recording');
        btn.innerHTML = '<i class="fas fa-stop"></i> <span>Dừng ghi âm</span>';
        status.classList.add('active');
        status.innerHTML = '<i class="fas fa-microphone fa-3x"></i><p>Đang ghi âm... Hãy đọc bài văn bên trên</p>';
        transcriptArea.style.display = 'block';
        document.getElementById('transcriptText').innerHTML = '<em style="color:var(--text-muted);">Đang lắng nghe...</em>';

        // Highlight reference text
        const refText = document.getElementById('referenceText');
        if (refText) refText.style.borderColor = 'var(--primary)';

        try {
            recognition.start();
        } catch(e) {
            console.error('Cannot start recognition:', e);
        }
    } else {
        // Dừng ghi âm
        isRecording = false;
        try { recognition.stop(); } catch(e) {}
        
        btn.classList.remove('recording');
        btn.innerHTML = '<i class="fas fa-microphone"></i> <span>Ghi âm lại</span>';
        status.classList.remove('active');
        status.innerHTML = '<i class="fas fa-check-circle fa-3x" style="color:var(--success)"></i><p>Ghi âm hoàn tất!</p>';

        // Remove highlight
        const refText = document.getElementById('referenceText');
        if (refText) refText.style.borderColor = '';

        // Gửi chấm điểm
        if (fullTranscript.trim()) {
            submitSpeaking();
        } else {
            document.getElementById('transcriptText').innerHTML = 
                '<em style="color:var(--error);">Không nhận được giọng nói. Vui lòng thử lại.</em>';
        }
    }
}

/**
 * Gửi transcript để chấm điểm
 */
function submitSpeaking() {
    const scoreResult = document.getElementById('scoreResult');
    
    scoreResult.style.display = 'block';
    scoreResult.innerHTML = '<div style="text-align:center;padding:2rem;"><i class="fas fa-spinner fa-spin fa-2x"></i><p style="margin-top:1rem;">Đang chấm điểm...</p></div>';
    scoreResult.scrollIntoView({ behavior: 'smooth' });

    console.log('[Speaking] Submitting...', { 
        promptId: SPEAKING_CONFIG.promptId, 
        transcript: fullTranscript.trim().substring(0, 100) + '...',
        confidence: lastConfidence 
    });

    fetch(SPEAKING_CONFIG.submitUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'same-origin',
        body: JSON.stringify({
            prompt_id: SPEAKING_CONFIG.promptId,
            transcript: fullTranscript.trim(),
            confidence: lastConfidence
        })
    })
    .then(response => {
        console.log('[Speaking] Response status:', response.status);
        if (!response.ok) {
            throw new Error('Server error: ' + response.status);
        }
        return response.text();
    })
    .then(text => {
        console.log('[Speaking] Response body:', text.substring(0, 200));
        let data;
        try {
            data = JSON.parse(text);
        } catch(e) {
            console.error('[Speaking] Invalid JSON:', text.substring(0, 200));
            throw new Error('Server trả về dữ liệu không hợp lệ.');
        }
        if (data.success) {
            displayScores(data.scores);
        } else {
            scoreResult.innerHTML = '<div style="text-align:center;padding:1rem;color:var(--error);"><i class="fas fa-exclamation-circle fa-2x"></i><p style="margin-top:0.5rem;">' + escHtml(data.error || 'Có lỗi xảy ra') + '</p></div>';
        }
    })
    .catch(err => {
        console.error('[Speaking] Error:', err);
        scoreResult.innerHTML = '<div style="text-align:center;padding:1rem;color:var(--error);"><i class="fas fa-exclamation-circle fa-2x"></i><p style="margin-top:0.5rem;">Lỗi: ' + escHtml(err.message) + '</p><button class="btn btn-primary btn-sm" onclick="submitSpeaking()" style="margin-top:1rem;"><i class="fas fa-redo"></i> Thử lại</button></div>';
    });
}

/**
 * Hiển thị điểm số
 */
function displayScores(scores) {
    const scoreResult = document.getElementById('scoreResult');
    const baseUrl = SPEAKING_CONFIG.baseUrl || '';
    
    scoreResult.innerHTML = `
        <h3><i class="fas fa-star"></i> Kết quả chấm điểm</h3>
        <div class="scores-grid">
            <div class="score-item">
                <div class="score-ring" style="border-color:${getScoreColor(scores.overall_score)}">
                    <span>${scores.overall_score}</span>
                </div>
                <label>Tổng điểm</label>
            </div>
            <div class="score-item">
                <div class="score-ring" style="border-color:${getScoreColor(scores.accuracy_score)}">
                    <span>${scores.accuracy_score}</span>
                </div>
                <label>Accuracy</label>
            </div>
            <div class="score-item">
                <div class="score-ring" style="border-color:${getScoreColor(scores.fluency_score)}">
                    <span>${scores.fluency_score}</span>
                </div>
                <label>Fluency</label>
            </div>
            <div class="score-item">
                <div class="score-ring" style="border-color:${getScoreColor(scores.pronunciation_score)}">
                    <span>${scores.pronunciation_score}</span>
                </div>
                <label>Pronunciation</label>
            </div>
        </div>
        <div class="feedback-text">${escHtml(scores.feedback || '')}</div>
        <div class="pronunciation-heatmap" style="margin:1.5rem 0;">
            <h4><i class="fas fa-highlighter"></i> Phân tích phát âm</h4>
            <div id="heatmapDisplay" style="margin:0.5rem 0;line-height:2;"></div>
            <div class="heatmap-legend">
                <span class="leg-good">Đọc đúng</span>
                <span class="leg-ok">Gần đúng</span>
                <span class="leg-miss">Bị thiếu</span>
            </div>
        </div>
        <div class="practice-actions">
            <button class="btn btn-primary" onclick="resetPractice()">
                <i class="fas fa-redo"></i> Thử lại
            </button>
            <a href="${baseUrl}/speaking" class="btn btn-outline">
                <i class="fas fa-list"></i> Chọn đề khác
            </a>
        </div>
    `;
    scoreResult.style.display = 'block';

    // Generate pronunciation heatmap
    generateHeatmap();
}

/**
 * Tạo Pronunciation Heatmap
 * So sánh từng từ trong sample với transcript
 */
function generateHeatmap() {
    const heatmapEl = document.getElementById('heatmapDisplay');
    if (!heatmapEl) return;

    const sampleText = SPEAKING_CONFIG.sampleAnswer || '';
    const transcript = fullTranscript.trim();
    
    const sampleWords = sampleText.toLowerCase().replace(/[^a-z0-9\s]/g, '').split(/\s+/);
    const transcriptWords = transcript.toLowerCase().replace(/[^a-z0-9\s]/g, '').split(/\s+/);
    const transcriptSet = new Set(transcriptWords);
    const originalWords = sampleText.split(/\s+/);

    let html = '';
    originalWords.forEach((word, i) => {
        const clean = word.toLowerCase().replace(/[^a-z0-9]/g, '');
        let cls = 'match-miss';
        if (transcriptSet.has(clean)) {
            cls = 'match-good';
        } else {
            // Check partial match (first 3+ chars)
            for (const tw of transcriptWords) {
                if (clean.length >= 3 && tw.length >= 3 && (tw.startsWith(clean.substring(0,3)) || clean.startsWith(tw.substring(0,3)))) {
                    cls = 'match-ok';
                    break;
                }
            }
        }
        html += `<span class="heatmap-word ${cls}">${word}</span> `;
    });
    
    heatmapEl.innerHTML = html;
}

/**
 * Lấy màu theo điểm
 */
function getScoreColor(score) {
    if (score >= 80) return '#43e97b';
    if (score >= 60) return '#4facfe';
    if (score >= 40) return '#fa8c16';
    return '#f5576c';
}

/**
 * Reset để thử lại
 */
function resetPractice() {
    fullTranscript = '';
    lastConfidence = 0.5;
    
    const transcriptText = document.getElementById('transcriptText');
    if (transcriptText) transcriptText.innerHTML = '';
    
    const transcriptArea = document.getElementById('transcriptArea');
    if (transcriptArea) transcriptArea.style.display = 'none';
    
    const scoreResult = document.getElementById('scoreResult');
    if (scoreResult) scoreResult.style.display = 'none';
    
    const status = document.getElementById('recordingStatus');
    if (status) {
        status.classList.remove('active');
        status.innerHTML = '<i class="fas fa-microphone fa-2x"></i><p>Nhấn để bắt đầu đọc</p>';
    }
    
    const btn = document.getElementById('recordBtn');
    if (btn) {
        btn.classList.remove('recording');
        btn.innerHTML = '<i class="fas fa-microphone"></i> <span>Ghi âm</span>';
    }

    const refText = document.getElementById('referenceText');
    if (refText) refText.style.borderColor = '';
}

/**
 * Phát câu mẫu bằng TTS (dùng giọng đã chọn)
 */
function speakSample() {
    if (typeof SPEAKING_CONFIG === 'undefined') return;
    speakText(SPEAKING_CONFIG.sampleAnswer);
}

// =============================================
// VOICE SELECTOR & FREE TEXT TTS
// =============================================

let availableVoices = [];
let selectedVoice = null;

/**
 * Khởi tạo Voice Selector - Lọc 5 giọng English hay nhất
 */
function initVoiceSelector() {
    const loadVoices = () => {
        const allVoices = speechSynthesis.getVoices();
        
        // Lọc giọng English
        const enVoices = allVoices.filter(v => v.lang.startsWith('en'));
        
        // Ưu tiên giọng chất lượng cao
        const preferredNames = [
            'Google US English',          // Google Female (US)
            'Google UK English Female',   // Google Female (UK)
            'Google UK English Male',     // Google Male (UK)
            'Microsoft Zira',             // Microsoft Female
            'Microsoft David',            // Microsoft Male
            'Microsoft Mark',             // Microsoft Male
            'Samantha',                   // macOS
            'Daniel',                     // macOS UK
            'Karen',                      // macOS AU
            'Alex'                        // macOS
        ];

        // Sắp xếp: ưu tiên tên quen thuộc trước
        enVoices.sort((a, b) => {
            const aIdx = preferredNames.findIndex(n => a.name.includes(n));
            const bIdx = preferredNames.findIndex(n => b.name.includes(n));
            if (aIdx !== -1 && bIdx !== -1) return aIdx - bIdx;
            if (aIdx !== -1) return -1;
            if (bIdx !== -1) return 1;
            return 0;
        });

        // Lấy tối đa 5 giọng, đảm bảo đa dạng
        availableVoices = enVoices.slice(0, 5);

        if (availableVoices.length === 0) {
            // Fallback: tạo default voice
            availableVoices = [{ name: 'Default English', lang: 'en-US', default: true }];
        }

        // Khôi phục voice đã chọn từ localStorage
        const savedVoice = localStorage.getItem('el_preferred_voice');
        selectedVoice = availableVoices.find(v => v.name === savedVoice) || availableVoices[0];

        renderVoiceOptions();
    };

    // Chrome cần sự kiện onvoiceschanged
    if (speechSynthesis.getVoices().length) {
        loadVoices();
    } else {
        speechSynthesis.onvoiceschanged = loadVoices;
    }

    // Rate & Pitch sliders
    const rateSlider = document.getElementById('voiceRate');
    const pitchSlider = document.getElementById('voicePitch');
    
    if (rateSlider) {
        rateSlider.addEventListener('input', function() {
            document.getElementById('voiceRateVal').textContent = this.value + 'x';
            localStorage.setItem('el_voice_rate', this.value);
        });
        const savedRate = localStorage.getItem('el_voice_rate');
        if (savedRate) { rateSlider.value = savedRate; document.getElementById('voiceRateVal').textContent = savedRate + 'x'; }
    }
    if (pitchSlider) {
        pitchSlider.addEventListener('input', function() {
            document.getElementById('voicePitchVal').textContent = this.value;
            localStorage.setItem('el_voice_pitch', this.value);
        });
        const savedPitch = localStorage.getItem('el_voice_pitch');
        if (savedPitch) { pitchSlider.value = savedPitch; document.getElementById('voicePitchVal').textContent = savedPitch; }
    }
}

/**
 * Render danh sách giọng đọc
 */
function renderVoiceOptions() {
    const container = document.getElementById('voiceOptions');
    const dropdown = document.getElementById('voiceDropdown');
    const flagMap = { 'en-US': '🇺🇸', 'en-GB': '🇬🇧', 'en-AU': '🇦🇺', 'en-IN': '🇮🇳', 'en-IE': '🇮🇪', 'en-ZA': '🇿🇦' };

    // Render radio buttons (for freetext page)
    if (container) {
        let html = '';
        availableVoices.forEach((voice, idx) => {
            const checked = voice === selectedVoice ? 'checked' : '';
            const flag = flagMap[voice.lang] || '🌐';
            const labelParts = voice.name.replace('Microsoft ', '').replace('Google ', '');
            html += `
                <label class="voice-option ${checked ? 'active' : ''}" data-idx="${idx}">
                    <input type="radio" name="voiceSelect" value="${idx}" ${checked} onchange="selectVoice(${idx})">
                    <span class="voice-flag">${flag}</span>
                    <span class="voice-name">${labelParts}</span>
                    <span class="voice-lang">${voice.lang}</span>
                    <button type="button" class="voice-preview" onclick="previewVoice(${idx}, event)" title="Nghe thử">
                        <i class="fas fa-play"></i>
                    </button>
                </label>
            `;
        });
        container.innerHTML = html;
    }

    // Render dropdown (for practice page compact toolbar)
    if (dropdown) {
        let opts = '';
        availableVoices.forEach((voice, idx) => {
            const flag = flagMap[voice.lang] || '🌐';
            const label = voice.name.replace('Microsoft ', '').replace('Google ', '');
            const sel = voice === selectedVoice ? 'selected' : '';
            opts += `<option value="${idx}" ${sel}>${flag} ${label} (${voice.lang})</option>`;
        });
        dropdown.innerHTML = opts;
        dropdown.addEventListener('change', function() {
            selectVoice(parseInt(this.value));
        });
    }
}

/**
 * Chọn giọng
 */
function selectVoice(idx) {
    selectedVoice = availableVoices[idx];
    localStorage.setItem('el_preferred_voice', selectedVoice.name);
    
    // Update UI
    document.querySelectorAll('.voice-option').forEach(el => el.classList.remove('active'));
    const selected = document.querySelector(`.voice-option[data-idx="${idx}"]`);
    if (selected) selected.classList.add('active');
}

/**
 * Nghe thử 1 giọng
 */
function previewVoice(idx, event) {
    event.preventDefault();
    event.stopPropagation();
    speechSynthesis.cancel();
    
    const voice = availableVoices[idx];
    const u = new SpeechSynthesisUtterance('Hello! This is how I sound. Nice to meet you!');
    u.voice = voice;
    u.lang = voice.lang;
    u.rate = 0.9;
    u.pitch = 1.0;
    speechSynthesis.speak(u);
}

/**
 * Phát text bằng giọng đã chọn
 */
function speakText(text) {
    if (!text || !text.trim()) return;
    speechSynthesis.cancel();

    const rate  = parseFloat(document.getElementById('voiceRate')?.value || 0.9);
    const pitch = parseFloat(document.getElementById('voicePitch')?.value || 1.0);

    const u = new SpeechSynthesisUtterance(text.trim());
    if (selectedVoice) {
        u.voice = selectedVoice;
        u.lang = selectedVoice.lang;
    } else {
        u.lang = 'en-US';
    }
    u.rate  = rate;
    u.pitch = pitch;

    // Progress tracking
    const progressEl = document.getElementById('speakingProgress');
    const wordEl = document.getElementById('speakingWord');
    const barEl = document.getElementById('speakProgressBar');
    const speakBtn = document.getElementById('speakFreeBtn');
    const stopBtn = document.getElementById('stopSpeakBtn');

    u.onstart = () => {
        if (progressEl) progressEl.style.display = 'block';
        if (speakBtn) speakBtn.style.display = 'none';
        if (stopBtn) stopBtn.style.display = 'inline-flex';
    };

    u.onboundary = (e) => {
        if (e.name === 'word' && wordEl) {
            const word = text.substring(e.charIndex, e.charIndex + e.charLength);
            wordEl.textContent = '🔊 ' + word;
            if (barEl) {
                const pct = Math.min(100, (e.charIndex / text.length) * 100);
                barEl.style.width = pct + '%';
            }
        }
    };

    u.onend = () => {
        if (barEl) barEl.style.width = '100%';
        if (wordEl) wordEl.textContent = '✅ Hoàn tất!';
        if (speakBtn) speakBtn.style.display = 'inline-flex';
        if (stopBtn) stopBtn.style.display = 'none';
        setTimeout(() => {
            if (progressEl) progressEl.style.display = 'none';
            if (barEl) barEl.style.width = '0%';
        }, 1500);
    };

    speechSynthesis.speak(u);
}

/**
 * Phát free text
 */
function speakFreeText() {
    const textarea = document.getElementById('freetextInput');
    if (!textarea) return;
    const text = textarea.value.trim();
    if (!text) {
        alert('Vui lòng nhập đoạn văn bản tiếng Anh.');
        textarea.focus();
        return;
    }
    speakText(text);
}

/**
 * Dừng phát
 */
function stopSpeaking() {
    speechSynthesis.cancel();
    const speakBtn = document.getElementById('speakFreeBtn');
    const stopBtn = document.getElementById('stopSpeakBtn');
    const progressEl = document.getElementById('speakingProgress');
    if (speakBtn) speakBtn.style.display = 'inline-flex';
    if (stopBtn) stopBtn.style.display = 'none';
    if (progressEl) progressEl.style.display = 'none';
}

/**
 * Dùng văn bản mẫu
 */
function useSampleText(el) {
    const p = el.querySelector('p');
    if (!p) return;
    const textarea = document.getElementById('freetextInput');
    if (textarea) {
        textarea.value = p.textContent.trim();
        textarea.focus();
    }
    // Highlight selected
    document.querySelectorAll('.sample-text-item').forEach(i => i.classList.remove('selected'));
    el.classList.add('selected');
}

/**
 * Xóa textarea
 */
function clearFreeText() {
    const textarea = document.getElementById('freetextInput');
    if (textarea) { textarea.value = ''; textarea.focus(); }
    document.querySelectorAll('.sample-text-item').forEach(i => i.classList.remove('selected'));
}

