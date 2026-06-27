<?php
/**
 * User Model
 * Quản lý người dùng
 */
class User extends Model {
    protected $table = 'users';

    /**
     * Đăng ký user mới
     * @param array $data
     * @return int User ID
     */
    public function register($data) {
        return $this->create([
            'username'      => $data['username'],
            'email'         => $data['email'],
            'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
            'full_name'     => $data['full_name'] ?? null
        ]);
    }

    /**
     * Xác thực đăng nhập
     * @param string $username
     * @param string $password
     * @return array|false User data hoặc false
     */
    public function authenticate($username, $password) {
        $user = $this->findBy('username', $username);
        
        if ($user && password_verify($password, $user['password_hash'])) {
            return $user;
        }
        
        // Thử tìm bằng email
        $user = $this->findBy('email', $username);
        if ($user && password_verify($password, $user['password_hash'])) {
            return $user;
        }
        
        return false;
    }

    /**
     * Kiểm tra username đã tồn tại
     * @param string $username
     * @return bool
     */
    public function usernameExists($username) {
        return $this->findBy('username', $username) !== false;
    }

    /**
     * Kiểm tra email đã tồn tại
     * @param string $email
     * @return bool
     */
    public function emailExists($email) {
        return $this->findBy('email', $email) !== false;
    }

    /**
     * Cập nhật profile
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateProfile($id, $data) {
        $updateData = [];
        if (isset($data['full_name'])) $updateData['full_name'] = $data['full_name'];
        if (isset($data['email'])) $updateData['email'] = $data['email'];
        if (isset($data['avatar'])) $updateData['avatar'] = $data['avatar'];
        
        return $this->update($id, $updateData);
    }

    /**
     * Đổi mật khẩu
     * @param int $id
     * @param string $newPassword
     * @return bool
     */
    public function changePassword($id, $newPassword) {
        return $this->update($id, [
            'password_hash' => password_hash($newPassword, PASSWORD_DEFAULT)
        ]);
    }

    /**
     * Tìm hoặc tạo user từ Google OAuth
     * @param array $googleUser {email, name, picture, id}
     * @return array|false User data
     */
    public function findOrCreateByGoogle($googleUser) {
        $email = $googleUser['email'];
        
        // Tìm user đã tồn tại bằng email
        $user = $this->findBy('email', $email);
        if ($user) {
            // Cập nhật avatar từ Google nếu đang dùng default
            if ($user['avatar'] === 'default.png' && !empty($googleUser['picture'])) {
                $this->update($user['id'], ['avatar' => $googleUser['picture']]);
                $user['avatar'] = $googleUser['picture'];
            }
            return $user;
        }

        // Tạo user mới
        $fullName = $googleUser['name'] ?? explode('@', $email)[0];
        $username = $this->generateUniqueUsername($email);
        $randomPassword = bin2hex(random_bytes(16)); // Random password (user dùng Google login)

        try {
            $userId = $this->create([
                'username'      => $username,
                'email'         => $email,
                'password_hash' => password_hash($randomPassword, PASSWORD_DEFAULT),
                'full_name'     => $fullName,
                'avatar'        => $googleUser['picture'] ?? 'default.png'
            ]);
            return $this->find($userId);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Tạo username duy nhất từ email
     * @param string $email
     * @return string
     */
    private function generateUniqueUsername($email) {
        $base = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', explode('@', $email)[0]));
        if (strlen($base) < 3) $base = 'user' . $base;
        
        $username = $base;
        $counter = 1;
        while ($this->usernameExists($username)) {
            $username = $base . $counter;
            $counter++;
        }
        return $username;
    }
}
