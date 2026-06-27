<?php
/**
 * SpeakingAttempt Model
 * Quản lý lượt luyện nói + chấm điểm
 */
class SpeakingAttempt extends Model {
    protected $table = 'speaking_attempts';

    /**
     * Lấy speaking prompts theo topic
     * @param int $topicId
     * @return array
     */
    public function getPromptsByTopic($topicId) {
        $stmt = $this->db->prepare("SELECT * FROM speaking_prompts WHERE topic_id = :topic_id ORDER BY difficulty ASC");
        $stmt->execute(['topic_id' => $topicId]);
        return $stmt->fetchAll();
    }

    /**
     * Lấy speaking prompt theo ID
     * @param int $promptId
     * @return array|false
     */
    public function getPrompt($promptId) {
        $stmt = $this->db->prepare("SELECT sp.*, t.name as topic_name FROM speaking_prompts sp JOIN topics t ON sp.topic_id = t.id WHERE sp.id = :id");
        $stmt->execute(['id' => $promptId]);
        return $stmt->fetch();
    }

    /**
     * Lấy tất cả speaking prompts kèm topic
     * @return array
     */
    public function getAllPrompts() {
        $stmt = $this->db->query("
            SELECT sp.*, t.name as topic_name, t.slug as topic_slug
            FROM speaking_prompts sp
            JOIN topics t ON sp.topic_id = t.id
            ORDER BY t.sort_order ASC, sp.difficulty ASC
        ");
        return $stmt->fetchAll();
    }

    /**
     * Chấm điểm Speaking (Rule-based)
     * So sánh transcript với sample_answer
     * 
     * @param string $transcript Text từ speech-to-text
     * @param string $sampleAnswer Câu trả lời mẫu
     * @param float $confidence Confidence từ Web Speech API (0-1)
     * @return array Điểm chi tiết
     */
    public function scoreSpeaking($transcript, $sampleAnswer, $confidence = 0.5) {
        // Chuẩn hóa text
        $transcriptWords = $this->normalizeWords($transcript);
        $sampleWords = $this->normalizeWords($sampleAnswer);

        // 1. Accuracy Score: % từ khớp với sample
        $matchedWords = array_intersect($transcriptWords, $sampleWords);
        $accuracyScore = count($sampleWords) > 0 
            ? min(100, round((count($matchedWords) / count($sampleWords)) * 100))
            : 0;

        // 2. Fluency Score: Độ dài transcript so với sample (capped 100%)
        $lengthRatio = count($sampleWords) > 0 
            ? count($transcriptWords) / count($sampleWords)
            : 0;
        // Penalize nếu quá ngắn hoặc quá dài
        if ($lengthRatio >= 0.8 && $lengthRatio <= 1.3) {
            $fluencyScore = 100;
        } elseif ($lengthRatio < 0.8) {
            $fluencyScore = round($lengthRatio * 100);
        } else {
            $fluencyScore = max(50, round(100 - ($lengthRatio - 1.3) * 50));
        }

        // 3. Pronunciation Score: Word-level phonetic matching
        // Chrome's Web Speech API always returns confidence=0, so we can't rely on it.
        // Instead, we measure how accurately the STT engine parsed each word
        // (if STT recognized a word correctly, the user pronounced it clearly).
        if ($confidence > 0.01) {
            // If browser actually provides a real confidence, use it
            $pronunciationScore = round($confidence * 100);
        } else {
            // Fallback: word-level similarity scoring
            $pronScore = 0;
            $pronTotal = count($sampleWords);
            if ($pronTotal > 0) {
                foreach ($sampleWords as $sw) {
                    // Exact match = full score
                    if (in_array($sw, $transcriptWords)) {
                        $pronScore += 1.0;
                        continue;
                    }
                    // Partial match: find closest word in transcript via similar_text
                    $bestRatio = 0;
                    foreach ($transcriptWords as $tw) {
                        similar_text($sw, $tw, $percent);
                        $bestRatio = max($bestRatio, $percent / 100);
                    }
                    // Only count if reasonably close (>60% similar)
                    if ($bestRatio > 0.6) {
                        $pronScore += $bestRatio;
                    }
                }
                $pronunciationScore = min(100, round(($pronScore / $pronTotal) * 100));
            } else {
                $pronunciationScore = 0;
            }
        }

        // 4. Overall Score (weighted average)
        $overallScore = round(
            $accuracyScore * ACCURACY_WEIGHT +
            $fluencyScore * FLUENCY_WEIGHT +
            $pronunciationScore * PRONUNCIATION_WEIGHT
        );

        // 5. Tạo feedback
        $feedback = $this->generateFeedback($accuracyScore, $fluencyScore, $pronunciationScore, $transcriptWords, $sampleWords);

        return [
            'accuracy_score'      => $accuracyScore,
            'fluency_score'       => $fluencyScore,
            'pronunciation_score' => $pronunciationScore,
            'overall_score'       => $overallScore,
            'feedback'            => $feedback
        ];
    }

    /**
     * Lưu lượt speaking
     */
    public function saveAttempt($userId, $promptId, $transcript, $scores) {
        return $this->create([
            'user_id'             => $userId,
            'prompt_id'           => $promptId,
            'transcript'          => $transcript,
            'pronunciation_score' => $scores['pronunciation_score'],
            'fluency_score'       => $scores['fluency_score'],
            'accuracy_score'      => $scores['accuracy_score'],
            'overall_score'       => $scores['overall_score'],
            'feedback'            => $scores['feedback']
        ]);
    }

    /**
     * Lấy lịch sử speaking của user
     * @param int $userId
     * @return array
     */
    public function getUserAttempts($userId) {
        $stmt = $this->db->prepare("
            SELECT sa.*, sp.prompt_text, t.name as topic_name
            FROM {$this->table} sa
            JOIN speaking_prompts sp ON sa.prompt_id = sp.id
            JOIN topics t ON sp.topic_id = t.id
            WHERE sa.user_id = :user_id
            ORDER BY sa.created_at DESC
        ");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }

    /**
     * Chuẩn hóa text thành mảng từ
     * @param string $text
     * @return array
     */
    private function normalizeWords($text) {
        $text = strtolower($text);
        $text = preg_replace('/[^a-z0-9\s]/', '', $text);
        $words = preg_split('/\s+/', trim($text));
        return array_filter($words);
    }

    /**
     * Tạo feedback tự động
     */
    private function generateFeedback($accuracy, $fluency, $pronunciation, $userWords, $sampleWords) {
        $feedback = [];

        // Accuracy feedback
        if ($accuracy >= 80) {
            $feedback[] = "✅ Excellent accuracy! You covered most of the key points.";
        } elseif ($accuracy >= 60) {
            $feedback[] = "👍 Good accuracy. You mentioned many important words.";
        } elseif ($accuracy >= 40) {
            $feedback[] = "⚠️ Fair accuracy. Try to include more key vocabulary from the topic.";
        } else {
            $feedback[] = "❌ Low accuracy. Review the sample answer and practice the key phrases.";
        }

        // Fluency feedback
        if ($fluency >= 80) {
            $feedback[] = "✅ Great fluency! Your response length is appropriate.";
        } elseif ($fluency >= 60) {
            $feedback[] = "👍 Good fluency. Try to elaborate a bit more.";
        } else {
            $feedback[] = "⚠️ Your response is quite short. Try to speak in complete sentences.";
        }

        // Pronunciation feedback
        if ($pronunciation >= 80) {
            $feedback[] = "✅ Clear pronunciation! Well done!";
        } elseif ($pronunciation >= 60) {
            $feedback[] = "👍 Decent pronunciation. Keep practicing to improve clarity.";
        } else {
            $feedback[] = "⚠️ Work on your pronunciation. Speak slowly and clearly.";
        }

        // Tìm từ bị thiếu
        $missingWords = array_diff($sampleWords, $userWords);
        $importantMissing = array_slice(array_unique($missingWords), 0, 5);
        if (!empty($importantMissing)) {
            $feedback[] = "📝 Key words you might include: " . implode(', ', $importantMissing);
        }

        return implode("\n", $feedback);
    }
}
