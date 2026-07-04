<?php

/**
 * Placement Model
 * Xử lý logic bài kiểm tra đầu vào (placement test) kiểu Duolingo
 * Thuật toán adaptive IRT đơn giản hóa để ước lượng trình độ người dùng
 */
class Placement extends Model
{
    protected $table = 'placement_sessions';

    const CEFR_LEVELS = ['A1', 'A2', 'B1', 'B2', 'C1'];
    const SKILL_TYPES = ['vocabulary', 'grammar', 'reading', 'listening'];
    const K_FACTOR = 0.5;           // Learning rate cho cập nhật theta
    const MIN_QUESTIONS = 10;        // Số câu tối thiểu trước khi xét kết thúc sớm
    const STABILITY_WINDOW = 5;      // Cửa sổ kiểm tra ổn định theta
    const STABILITY_THRESHOLD = 0.3; // Ngưỡng std deviation

    /**
     * Ánh xạ self-assessment sang theta khởi tạo
     */
    private static $selfAssessmentTheta = [
        'some'     => 2.0, // "Biết một ít" → A2
        'advanced' => 4.0, // "Khá tốt / Thành thạo" → B2
    ];

    /**
     * Ánh xạ CEFR → App Level / XP
     */
    private static $cefrMapping = [
        'A1' => ['level' => 1, 'xp' => 0],
        'A2' => ['level' => 2, 'xp' => 100],
        'B1' => ['level' => 4, 'xp' => 300],
        'B2' => ['level' => 6, 'xp' => 500],
        'C1' => ['level' => 9, 'xp' => 800],
    ];

    /**
     * Số câu tối đa theo membership
     */
    public static function maxQuestions(string $membership): int
    {
        return $membership === 'pro' ? 25 : 15;
    }

    /**
     * Chuyển CEFR string → numeric (A1=1.0 ... C1=5.0)
     */
    public static function cefrToNumeric(string $cefr): float
    {
        return array_search($cefr, self::CEFR_LEVELS) + 1.0;
    }

    /**
     * Chuyển numeric (1.0-5.0) → CEFR string
     */
    public static function numericToCefr(float $theta): string
    {
        $index = (int) round($theta - 1);
        $index = max(0, min(4, $index));
        return self::CEFR_LEVELS[$index];
    }

    // ============================================
    // SESSION MANAGEMENT
    // ============================================

    /**
     * Tạo phiên placement mới
     * @param  int    $userId
     * @param  string $selfAssessment 'some' hoặc 'advanced'
     * @return int    session ID
     */
    public function startSession(int $userId, string $selfAssessment): int
    {
        $theta = self::$selfAssessmentTheta[$selfAssessment] ?? 2.0;
        $membership = $_SESSION['membership'] ?? 'free';
        $totalQuestions = self::maxQuestions($membership);

        return $this->create([
            'user_id'         => $userId,
            'status'          => 'in_progress',
            'initial_theta'   => $theta,
            'current_theta'   => $theta,
            'questions_answered' => 0,
            'correct_count'   => 0,
            'total_questions' => $totalQuestions,
        ]);
    }

    /**
     * Lấy phiên đang làm dở của user
     */
    public function getActiveSession(int $userId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM placement_sessions WHERE user_id = :uid AND status = :status ORDER BY id DESC LIMIT 1'
        );
        $stmt->execute(['uid' => $userId, 'status' => 'in_progress']);
        $session = $stmt->fetch();

        return $session ?: null;
    }

    /**
     * Lấy kết quả placement gần nhất của user
     */
    public function getLastResult(int $userId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM placement_sessions WHERE user_id = :uid AND status = :status ORDER BY completed_at DESC LIMIT 1'
        );
        $stmt->execute(['uid' => $userId, 'status' => 'completed']);
        $session = $stmt->fetch();

        return $session ?: null;
    }

    // ============================================
    // QUESTION SELECTION
    // ============================================

    /**
     * Chọn câu hỏi tiếp theo gần theta nhất
     * @param  float       $theta        Năng lực hiện tại
     * @param  array       $excludeIds   Các question_id đã hỏi
     * @param  string|null $skillType    Ưu tiên skill_type (xoay vòng)
     * @return array|null
     */
    public function selectNextQuestion(float $theta, array $excludeIds, ?string $skillType = null): ?array
    {
        $excludeIds[] = 0; // Tránh mảng rỗng gây lỗi SQL
        $placeholders = implode(',', array_fill(0, count($excludeIds), '?'));

        $sql = "SELECT *, ABS(
            (CASE cefr_level
                WHEN 'A1' THEN 1 WHEN 'A2' THEN 2 WHEN 'B1' THEN 3
                WHEN 'B2' THEN 4 WHEN 'C1' THEN 5 END
            * difficulty_weight) - ?
        ) AS distance
        FROM placement_questions
        WHERE is_active = 1 AND id NOT IN ($placeholders)";

        $params = array_merge([$theta], $excludeIds);

        if ($skillType) {
            $sql .= " AND skill_type = ?";
            $params[] = $skillType;
        }

        $sql .= " ORDER BY distance ASC, RAND() LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $question = $stmt->fetch();

        if (!$question) {
            // Fallback: thử không giới hạn skill_type
            if ($skillType) {
                return $this->selectNextQuestion($theta, $excludeIds, null);
            }
            return null;
        }

        // Parse options_json
        $question['options'] = [];
        if (!empty($question['options_json'])) {
            $parsed = json_decode($question['options_json'], true);
            $question['options'] = is_array($parsed) ? $parsed : [];
        }

        return $question;
    }

    // ============================================
    // RESPONSE PROCESSING (Thuật toán adaptive)
    // ============================================

    /**
     * Xử lý câu trả lời: chấm điểm + cập nhật theta + lưu response
     * @return array { is_correct, correct_answer, explanation, theta, questions_answered }
     */
    public function processResponse(int $sessionId, int $questionId, string $userAnswer, int $responseTimeMs = 0): array
    {
        $session = $this->find($sessionId);
        if (!$session) {
            throw new \RuntimeException("Session not found: $sessionId");
        }

        // Lấy câu hỏi
        $stmt = $this->db->prepare('SELECT * FROM placement_questions WHERE id = :id');
        $stmt->execute(['id' => $questionId]);
        $question = $stmt->fetch();
        if (!$question) {
            throw new \RuntimeException("Question not found: $questionId");
        }

        // Chấm điểm (giống logic UserAnswer::submitTest)
        $isCorrect = $this->scoreAnswer($question, $userAnswer);

        // Resolve correct_answer nếu options_json là object format {"A":"..","B":".."}
        $resolvedAnswer = $question['correct_answer'];
        if (!empty($question['options_json'])) {
            $parsed = json_decode($question['options_json'], true);
            if ($parsed && isset($parsed['A']) && isset($parsed[$resolvedAnswer])) {
                $resolvedAnswer = $parsed[$resolvedAnswer];
            }
        }

        // Cập nhật theta dùng IRT
        $thetaBefore = (float) $session['current_theta'];
        $questionDifficulty = self::cefrToNumeric($question['cefr_level']) * (float) $question['difficulty_weight'];

        // Hàm logistic: xác suất đúng dự đoán
        $expected = 1.0 / (1.0 + exp(-($thetaBefore - $questionDifficulty)));
        $actual = $isCorrect ? 1.0 : 0.0;
        $thetaAfter = $thetaBefore + self::K_FACTOR * ($actual - $expected);
        $thetaAfter = max(1.0, min(5.0, $thetaAfter));

        // Tính thứ tự câu hỏi
        $countStmt = $this->db->prepare('SELECT COUNT(*) as cnt FROM placement_responses WHERE session_id = :sid');
        $countStmt->execute(['sid' => $sessionId]);
        $order = (int) $countStmt->fetch()['cnt'] + 1;

        // Lưu response
        $this->db->prepare(
            'INSERT INTO placement_responses (session_id, question_id, user_answer, is_correct, theta_before, theta_after, response_time_ms, question_order)
             VALUES (:sid, :qid, :ua, :ic, :tb, :ta, :rt, :ord)'
        )->execute([
            'sid' => $sessionId, 'qid' => $questionId, 'ua' => $userAnswer,
            'ic' => $isCorrect ? 1 : 0, 'tb' => $thetaBefore, 'ta' => $thetaAfter,
            'rt' => $responseTimeMs, 'ord' => $order,
        ]);

        // Cập nhật session
        $newCount = $session['questions_answered'] + 1;
        $newCorrect = $session['correct_count'] + ($isCorrect ? 1 : 0);
        $this->update($sessionId, [
            'current_theta' => $thetaAfter,
            'questions_answered' => $newCount,
            'correct_count' => $newCorrect,
        ]);

        return [
            'is_correct'       => $isCorrect,
            'correct_answer'   => $resolvedAnswer,
            'explanation'      => $question['explanation'] ?? null,
            'theta'            => $thetaAfter,
            'questions_answered' => $newCount,
        ];
    }

    /**
     * Chấm điểm một câu trả lời
     */
    private function scoreAnswer(array $question, string $userAnswer): bool
    {
        $correctAnswer = $question['correct_answer'];

        // Normalize nếu options_json là object format {"A":"..","B":".."}
        if (!empty($question['options_json'])) {
            $parsed = json_decode($question['options_json'], true);
            if ($parsed && isset($parsed['A']) && isset($parsed[$correctAnswer])) {
                $correctAnswer = $parsed[$correctAnswer];
            }
        }

        if ($question['question_type'] === 'fill_blank') {
            return strtolower(trim($userAnswer)) === strtolower(trim($correctAnswer));
        }

        return trim($userAnswer) === trim($correctAnswer);
    }

    // ============================================
    // TERMINATION CHECK
    // ============================================

    /**
     * Kiểm tra xem có nên kết thúc sớm không
     * Điều kiện: >= 10 câu && std deviation 5 theta gần nhất < 0.3
     */
    public function shouldTerminate(int $sessionId): bool
    {
        $session = $this->find($sessionId);
        if (!$session || $session['questions_answered'] < self::MIN_QUESTIONS) {
            return false;
        }

        // Lấy N theta gần nhất
        $stmt = $this->db->prepare(
            'SELECT theta_after FROM placement_responses WHERE session_id = :sid ORDER BY id DESC LIMIT :lim'
        );
        $stmt->bindValue('sid', $sessionId, \PDO::PARAM_INT);
        $stmt->bindValue('lim', self::STABILITY_WINDOW, \PDO::PARAM_INT);
        $stmt->execute();
        $thetas = array_column($stmt->fetchAll(), 'theta_after');

        if (count($thetas) < self::STABILITY_WINDOW) {
            return false;
        }

        $mean = array_sum($thetas) / count($thetas);
        $variance = array_sum(array_map(function ($t) use ($mean) {
            return pow($t - $mean, 2);
        }, $thetas)) / count($thetas);
        $stdDev = sqrt($variance);

        return $stdDev < self::STABILITY_THRESHOLD;
    }

    /**
     * Kiểm tra đã hết số câu tối đa chưa
     */
    public function isMaxedOut(int $sessionId): bool
    {
        $session = $this->find($sessionId);
        if (!$session) return true;

        return $session['questions_answered'] >= $session['total_questions'];
    }

    // ============================================
    // FINALIZATION
    // ============================================

    /**
     * Kết thúc phiên: tính final CEFR, cập nhật user level/XP
     * @return array Kết quả cuối cùng
     */
    public function finalizeSession(int $sessionId): array
    {
        $session = $this->find($sessionId);
        if (!$session) {
            throw new \RuntimeException("Session not found: $sessionId");
        }

        $theta = (float) $session['current_theta'];
        $finalCefr = self::numericToCefr($theta);

        // Tính confidence score
        $confidence = $this->calculateConfidence($sessionId);

        // Cập nhật session
        $this->update($sessionId, [
            'status'           => 'completed',
            'final_cefr'       => $finalCefr,
            'final_theta'      => $theta,
            'confidence_score'  => round($confidence, 2),
            'completed_at'     => date('Y-m-d H:i:s'),
        ]);

        // Cập nhật user
        $this->updateUserPlacement($session['user_id'], $finalCefr, $sessionId);

        return [
            'cefr_level'        => $finalCefr,
            'app_level'         => self::$cefrMapping[$finalCefr]['level'],
            'xp_awarded'        => self::$cefrMapping[$finalCefr]['xp'],
            'theta'             => round($theta, 2),
            'confidence'        => round($confidence, 2),
            'total_answered'    => $session['questions_answered'],
            'correct_count'     => $session['correct_count'],
        ];
    }

    /**
     * Đánh dấu user là "Mới bắt đầu" — gán A1, không cần làm test
     */
    public function markAsBeginner(int $userId): void
    {
        $this->db->prepare(
            'UPDATE users SET placement_level = :pl, placement_completed_at = NOW() WHERE id = :id'
        )->execute(['pl' => 'A1', 'id' => $userId]);

        // Log XP
        $this->db->prepare(
            "INSERT INTO xp_history (user_id, xp_amount, activity_type, description)
             VALUES (:uid, 0, 'placement_test', :desc)"
        )->execute(['uid' => $userId, 'desc' => 'Xac nhan trinh do: Moi bat dau (A1)']);

        // Khởi tạo course progress cho A1
        require_once APP_PATH . '/models/CourseProgress.php';
        CourseProgress::initializeForUser($userId, 'A1');
    }

    // ============================================
    // HELPERS
    // ============================================

    /**
     * Tính confidence score dựa trên mức độ ổn định của theta
     */
    private function calculateConfidence(int $sessionId): float
    {
        $stmt = $this->db->prepare(
            'SELECT theta_after FROM placement_responses WHERE session_id = :sid ORDER BY id ASC'
        );
        $stmt->execute(['sid' => $sessionId]);
        $thetas = array_column($stmt->fetchAll(), 'theta_after');

        if (count($thetas) < 2) return 0.5;

        $diffs = [];
        for ($i = 1; $i < count($thetas); $i++) {
            $diffs[] = abs($thetas[$i] - $thetas[$i - 1]);
        }
        $avgFluctuation = array_sum($diffs) / count($diffs);

        return max(0, min(1, 1 - $avgFluctuation));
    }

    /**
     * Cập nhật thông tin placement cho user
     */
    private function updateUserPlacement(int $userId, string $cefr, int $sessionId): void
    {
        $mapping = self::$cefrMapping[$cefr];
        $targetXP = $mapping['xp'];

        // Lấy XP hiện tại
        $stmt = $this->db->prepare('SELECT total_xp FROM users WHERE id = :id');
        $stmt->execute(['id' => $userId]);
        $user = $stmt->fetch();

        if (!$user) return;

        $currentXP = (int) $user['total_xp'];

        if ($targetXP > $currentXP) {
            $xpToAdd = $targetXP - $currentXP;
            $this->db->prepare(
                'UPDATE users SET total_xp = total_xp + :xp, level = GREATEST(1, FLOOR((total_xp + :xp2) / 100) + 1),
                 placement_level = :pl, placement_completed_at = NOW(), placement_session_id = :sid WHERE id = :id'
            )->execute([
                'xp' => $xpToAdd, 'xp2' => $xpToAdd,
                'pl' => $cefr, 'sid' => $sessionId, 'id' => $userId,
            ]);

            // Log XP history
            $this->db->prepare(
                "INSERT INTO xp_history (user_id, xp_amount, activity_type, description)
                 VALUES (:uid, :xp, 'placement_test', :desc)"
            )->execute([
                'uid' => $userId, 'xp' => $xpToAdd,
                'desc' => "Hoan thanh bai kiem tra dau vao - Trinh do $cefr",
            ]);
        } else {
            // Chỉ ghi nhận placement, không đổi XP/level
            $this->db->prepare(
                'UPDATE users SET placement_level = :pl, placement_completed_at = NOW(), placement_session_id = :sid WHERE id = :id'
            )->execute(['pl' => $cefr, 'sid' => $sessionId, 'id' => $userId]);
        }

        // Khởi tạo course progress theo CEFR level
        require_once APP_PATH . '/models/CourseProgress.php';
        CourseProgress::initializeForUser($userId, $cefr);
    }

    /**
     * Lấy danh sách question_id đã hỏi trong session
     */
    public function getAskedQuestionIds(int $sessionId): array
    {
        $stmt = $this->db->prepare(
            'SELECT question_id FROM placement_responses WHERE session_id = :sid ORDER BY question_order ASC'
        );
        $stmt->execute(['sid' => $sessionId]);
        return array_column($stmt->fetchAll(), 'question_id');
    }

    /**
     * Lấy topic gợi ý theo CEFR level
     */
    public function getRecommendedTopics(string $cefr): array
    {
        $cefrToTopicLevel = [
            'A1' => 'beginner',
            'A2' => 'beginner',
            'B1' => 'intermediate',
            'B2' => 'intermediate',
            'C1' => 'advanced',
        ];

        $topicLevel = $cefrToTopicLevel[$cefr] ?? 'beginner';
        $stmt = $this->db->prepare(
            'SELECT * FROM topics WHERE level = :level AND is_active = 1 ORDER BY sort_order ASC LIMIT 3'
        );
        $stmt->execute(['level' => $topicLevel]);
        return $stmt->fetchAll();
    }

    /**
     * Đếm số câu hỏi theo CEFR level (cho admin)
     */
    public function getQuestionCountByLevel(): array
    {
        $stmt = $this->db->query(
            'SELECT cefr_level, COUNT(*) as cnt FROM placement_questions WHERE is_active = 1 GROUP BY cefr_level ORDER BY FIELD(cefr_level, "A1","A2","B1","B2","C1")'
        );
        return $stmt->fetchAll();
    }
}
