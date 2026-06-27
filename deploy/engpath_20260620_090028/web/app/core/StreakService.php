<?php
/**
 * StreakService
 * Quản lý streak, XP, daily goal
 */
class StreakService {

    /**
     * Cập nhật streak khi user hoạt động
     */
    public static function updateStreak($userId) {
        $db = getDB();
        $today = date('Y-m-d');

        $stmt = $db->prepare("SELECT current_streak, longest_streak, last_activity_date, daily_goal_date, daily_xp_today FROM users WHERE id=:id");
        $stmt->execute(['id' => $userId]);
        $user = $stmt->fetch();
        if (!$user) return;

        $lastDate = $user['last_activity_date'];

        if ($lastDate === $today) return; // Already updated today

        $newStreak = 1;
        if ($lastDate === date('Y-m-d', strtotime('-1 day'))) {
            $newStreak = $user['current_streak'] + 1;
        }

        $longestStreak = max($user['longest_streak'], $newStreak);

        // Reset daily XP if new day
        $dailyXp = ($user['daily_goal_date'] === $today) ? $user['daily_xp_today'] : 0;

        $stmt = $db->prepare("UPDATE users SET current_streak=:s, longest_streak=:ls, last_activity_date=:d, daily_xp_today=:dx, daily_goal_date=:dd WHERE id=:id");
        $stmt->execute([
            's' => $newStreak, 'ls' => $longestStreak, 'd' => $today,
            'dx' => $dailyXp, 'dd' => $today, 'id' => $userId
        ]);

        // Streak bonus XP (every 7 days)
        if ($newStreak > 0 && $newStreak % 7 === 0) {
            self::addXP($userId, 50, 'streak_bonus', "🔥 Streak $newStreak ngày!");
        }
    }

    /**
     * Thêm XP cho user
     */
    public static function addXP($userId, $amount, $activityType, $description = '') {
        $db = getDB();
        $today = date('Y-m-d');

        // Log XP
        $stmt = $db->prepare("INSERT INTO xp_history (user_id, xp_amount, activity_type, description) VALUES (:uid, :xp, :type, :desc)");
        $stmt->execute(['uid' => $userId, 'xp' => $amount, 'type' => $activityType, 'desc' => $description]);

        // Update total XP + daily XP + level
        $stmt = $db->prepare("UPDATE users SET total_xp = total_xp + :xp, daily_xp_today = daily_xp_today + :xp2, daily_goal_date = :today, level = GREATEST(1, FLOOR((total_xp + :xp3) / 100) + 1) WHERE id = :id");
        $stmt->execute(['xp' => $amount, 'xp2' => $amount, 'xp3' => $amount, 'today' => $today, 'id' => $userId]);
    }

    /**
     * Lấy thông tin streak + XP
     */
    public static function getUserStats($userId) {
        $db = getDB();
        $today = date('Y-m-d');

        $stmt = $db->prepare("SELECT current_streak, longest_streak, last_activity_date, total_xp, level, daily_goal, daily_xp_today, daily_goal_date FROM users WHERE id=:id");
        $stmt->execute(['id' => $userId]);
        $user = $stmt->fetch();

        if (!$user) return null;

        // If last_activity was not today/yesterday, streak is broken
        if ($user['last_activity_date'] && $user['last_activity_date'] !== $today && $user['last_activity_date'] !== date('Y-m-d', strtotime('-1 day'))) {
            $db->prepare("UPDATE users SET current_streak = 0 WHERE id=:id")->execute(['id' => $userId]);
            $user['current_streak'] = 0;
        }

        // Reset daily XP if different day
        if ($user['daily_goal_date'] !== $today) {
            $user['daily_xp_today'] = 0;
        }

        $user['xp_to_next_level'] = ($user['level'] * 100) - $user['total_xp'];
        $user['level_progress'] = $user['total_xp'] % 100;
        $user['daily_progress'] = $user['daily_goal'] > 0 ? min(100, round($user['daily_xp_today'] / $user['daily_goal'] * 100)) : 0;

        return $user;
    }

    /**
     * XP amounts per activity
     */
    public static function getXPAmounts() {
        return [
            'vocab_learn' => 10,
            'test_complete' => 50,
            'speaking_practice' => 30,
            'flashcard' => 15,
            'lesson_complete' => 20,
            'login_bonus' => 5,
            'streak_bonus' => 50,
        ];
    }

    /**
     * Cập nhật daily goal
     */
    public static function setDailyGoal($userId, $goal) {
        $db = getDB();
        $goal = max(10, min(200, intval($goal)));
        $db->prepare("UPDATE users SET daily_goal=:g WHERE id=:id")->execute(['g' => $goal, 'id' => $userId]);
    }

    /**
     * Lấy XP history gần đây
     */
    public static function getRecentXP($userId, $limit = 10) {
        $db = getDB();
        $limit = max(1, min(50, intval($limit)));
        $stmt = $db->prepare("SELECT * FROM xp_history WHERE user_id=:id ORDER BY created_at DESC LIMIT :lim");
        $stmt->bindValue(':id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
