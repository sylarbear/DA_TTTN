<!-- Speaking Practice Page - Read Aloud Mode -->
<section class="page-header">
    <div class="container">
        <nav class="breadcrumb">
            <a href="<?= BASE_URL ?>/speaking">Luyện nói</a>
            <span>/</span>
            <span><?= htmlspecialchars($prompt['topic_name']) ?></span>
        </nav>
        <h1><i class="fas fa-microphone"></i> Luyện nói</h1>
        <span class="difficulty diff-<?= $prompt['difficulty'] ?>"><?= ucfirst($prompt['difficulty']) ?></span>
    </div>
</section>

<section class="speaking-practice">
    <div class="container">
        <div class="practice-grid">
            <!-- Left: Reference Text + Recording -->
            <div class="practice-main">
                <!-- Compact Voice Toolbar -->
                <div class="voice-toolbar" id="voiceSelectorCard">
                    <div class="voice-toolbar-left">
                        <select class="voice-dropdown" id="voiceDropdown">
                            <option value="">Đang tải giọng...</option>
                        </select>
                        <button class="btn btn-sm btn-outline" onclick="speakSample()" title="Nghe mẫu">
                            <i class="fas fa-volume-up"></i> Nghe mẫu
                        </button>
                        <button class="btn btn-sm btn-outline" onclick="stopSpeaking()" title="Dừng">
                            <i class="fas fa-stop"></i>
                        </button>
                    </div>
                    <div class="voice-toolbar-right">
                        <div class="setting-group-mini">
                            <label>Tốc độ</label>
                            <input type="range" id="voiceRate" min="0.5" max="1.5" step="0.1" value="0.9">
                            <span id="voiceRateVal">0.9x</span>
                        </div>
                        <div class="setting-group-mini">
                            <label>Tone</label>
                            <input type="range" id="voicePitch" min="0.5" max="1.5" step="0.1" value="1.0">
                            <span id="voicePitchVal">1.0</span>
                        </div>
                    </div>
                    <!-- Hidden voice options for JS compatibility -->
                    <div class="voice-options" id="voiceOptions" style="display:none;"></div>
                </div>

                <!-- Two-column: Text + Recording side by side -->
                <div class="practice-split">
                    <!-- Left: Reference Text -->
                    <div class="prompt-display">
                        <h3><i class="fas fa-book-reader"></i> Bài đọc</h3>
                        <p style="color:var(--text-muted); margin-bottom:0.8rem; font-size:0.85rem;">
                            <i class="fas fa-info-circle"></i> Đọc to đoạn văn bên dưới. Hệ thống sẽ nhận dạng và chấm điểm.
                        </p>
                        <div class="reference-text" id="referenceText">
                            <?= htmlspecialchars($prompt['sample_answer']) ?>
                        </div>
                    </div>

                    <!-- Right: Recording + Transcript -->
                    <div class="recording-panel">
                        <div class="recording-area" id="recordingArea">
                            <div class="recording-status" id="recordingStatus">
                                <i class="fas fa-microphone fa-2x"></i>
                                <p>Nhấn để bắt đầu đọc</p>
                            </div>

                            <div class="recording-controls">
                                <button class="btn-record" id="recordBtn" onclick="toggleRecording()">
                                    <i class="fas fa-microphone"></i>
                                    <span>Ghi âm</span>
                                </button>
                            </div>

                            <!-- Transcript Output -->
                            <div class="transcript-area" id="transcriptArea" style="display:none;">
                                <h4><i class="fas fa-file-alt"></i> Bạn đã đọc:</h4>
                                <div class="transcript-text" id="transcriptText"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Score Result -->
                <div class="score-result" id="scoreResult" style="display:none;"></div>
            </div>

            <!-- Right: History -->
            <div class="practice-sidebar">
                <!-- Tips -->
                <div class="sample-answer-card">
                    <div class="sample-header">
                        <h4><i class="fas fa-lightbulb"></i> Mẹo luyện nói</h4>
                    </div>
                    <div class="sample-body" style="display:block;">
                        <ul style="list-style:none; padding:0; margin:0;">
                            <li style="padding:0.4rem 0;"><i class="fas fa-check" style="color:var(--success); margin-right:0.5rem;"></i> Đọc chậm, rõ ràng</li>
                            <li style="padding:0.4rem 0;"><i class="fas fa-check" style="color:var(--success); margin-right:0.5rem;"></i> Chú ý phát âm từng từ</li>
                            <li style="padding:0.4rem 0;"><i class="fas fa-check" style="color:var(--success); margin-right:0.5rem;"></i> Nghe mẫu trước khi đọc</li>
                            <li style="padding:0.4rem 0;"><i class="fas fa-check" style="color:var(--success); margin-right:0.5rem;"></i> Thử lại nhiều lần để cải thiện</li>
                        </ul>
                    </div>
                </div>

                <!-- History -->
                <?php if (!empty($history)): ?>
                    <div class="history-card">
                        <h4><i class="fas fa-history"></i> Lịch sử luyện tập</h4>
                        <div class="history-list">
                            <?php foreach ($history as $attempt): ?>
                                <div class="history-item">
                                    <div class="history-score"><?= $attempt['overall_score'] ?></div>
                                    <div class="history-info">
                                        <small><?= date('d/m/Y H:i', strtotime($attempt['created_at'])) ?></small>
                                        <div class="history-details">
                                            <span>A:<?= $attempt['accuracy_score'] ?></span>
                                            <span>F:<?= $attempt['fluency_score'] ?></span>
                                            <span>P:<?= $attempt['pronunciation_score'] ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Config TRƯỚC khi load speaking.js -->
<script>
const SPEAKING_CONFIG = {
    promptId: <?= $prompt['id'] ?>,
    submitUrl: '<?= BASE_URL ?>/speaking/score',
    sampleAnswer: <?= json_encode($prompt['sample_answer']) ?>,
    baseUrl: '<?= BASE_URL ?>'
};
</script>
<script src="<?= BASE_URL ?>/js/speaking.js?v=<?= time() ?>"></script>
