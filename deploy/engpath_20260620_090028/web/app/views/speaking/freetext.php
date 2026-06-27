<!-- Free Text Speaking Practice -->
<section class="page-header">
    <div class="container">
        <h1><i class="fas fa-keyboard"></i> Luyện phát âm tự do</h1>
        <p>Nhập bất kỳ đoạn văn tiếng Anh nào và nghe phát âm chuẩn</p>
    </div>
</section>

<section class="speaking-section">
    <div class="container">
        <!-- Voice Selector -->
        <div class="voice-selector-card" id="voiceSelectorCard">
            <h3><i class="fas fa-sliders-h"></i> Chọn giọng đọc</h3>
            <div class="voice-options" id="voiceOptions">
                <p style="color:var(--text-muted);"><i class="fas fa-spinner fa-spin"></i> Đang tải giọng đọc...</p>
            </div>
            <div class="voice-settings">
                <div class="setting-group">
                    <label><i class="fas fa-tachometer-alt"></i> Tốc độ</label>
                    <input type="range" id="voiceRate" min="0.5" max="1.5" step="0.1" value="0.9">
                    <span id="voiceRateVal">0.9x</span>
                </div>
                <div class="setting-group">
                    <label><i class="fas fa-music"></i> Tone</label>
                    <input type="range" id="voicePitch" min="0.5" max="1.5" step="0.1" value="1.0">
                    <span id="voicePitchVal">1.0</span>
                </div>
            </div>
        </div>

        <!-- Free Text Input -->
        <div class="freetext-card">
            <h3><i class="fas fa-pen-fancy"></i> Nhập đoạn văn bản</h3>
            <textarea id="freetextInput" class="freetext-textarea" placeholder="Type or paste any English text here...&#10;&#10;Example: The quick brown fox jumps over the lazy dog." rows="6"></textarea>

            <div class="freetext-actions">
                <button class="btn btn-primary btn-lg" id="speakFreeBtn" onclick="speakFreeText()">
                    <i class="fas fa-volume-up"></i> Nghe phát âm
                </button>
                <button class="btn btn-outline btn-lg" id="stopSpeakBtn" onclick="stopSpeaking()" style="display:none;">
                    <i class="fas fa-stop"></i> Dừng
                </button>
                <button class="btn btn-outline btn-lg" onclick="clearFreeText()">
                    <i class="fas fa-eraser"></i> Xóa
                </button>
            </div>

            <!-- Speaking progress -->
            <div id="speakingProgress" class="speaking-progress" style="display:none;">
                <div class="progress-bar-wrapper">
                    <div class="progress-bar-fill" id="speakProgressBar"></div>
                </div>
                <p id="speakingWord" class="speaking-word"></p>
            </div>
        </div>

        <!-- Sample Texts -->
        <div class="sample-texts-card">
            <h3><i class="fas fa-lightbulb"></i> Đoạn văn mẫu</h3>
            <div class="sample-texts-grid">
                <div class="sample-text-item" onclick="useSampleText(this)">
                    <span class="sample-label">📖 Giới thiệu bản thân</span>
                    <p>Hello, my name is [your name]. I am a student at [your school]. I enjoy learning English because it helps me communicate with people from all over the world.</p>
                </div>
                <div class="sample-text-item" onclick="useSampleText(this)">
                    <span class="sample-label">🌍 Du lịch</span>
                    <p>Traveling is one of the best ways to learn about different cultures. When you visit a new country, you can try local food, meet interesting people, and see beautiful places.</p>
                </div>
                <div class="sample-text-item" onclick="useSampleText(this)">
                    <span class="sample-label">💻 Công nghệ</span>
                    <p>Technology has changed the way we live and work. Smartphones allow us to stay connected, while artificial intelligence is transforming many industries around the world.</p>
                </div>
                <div class="sample-text-item" onclick="useSampleText(this)">
                    <span class="sample-label">🎓 Học tập</span>
                    <p>Education is the key to success. By studying hard and practicing regularly, you can improve your skills and achieve your goals. Never stop learning new things.</p>
                </div>
            </div>
        </div>

        <!-- Back -->
        <div style="text-align:center; margin-top:2rem;">
            <a href="<?= BASE_URL ?>/speaking" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Quay lại danh sách
            </a>
        </div>
    </div>
</section>

<script src="<?= BASE_URL ?>/js/speaking.js?v=<?= time() ?>"></script>
