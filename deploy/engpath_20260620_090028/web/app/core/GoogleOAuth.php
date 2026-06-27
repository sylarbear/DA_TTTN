<?php
/**
 * GoogleOAuth - Google OAuth 2.0 Service
 * Xử lý đăng nhập bằng tài khoản Google (vanilla PHP, không cần Composer)
 */
class GoogleOAuth {

    /**
     * Lấy URL đăng nhập Google
     * @return string Authorization URL
     */
    public static function getAuthUrl() {
        $params = [
            'client_id'     => GOOGLE_CLIENT_ID,
            'redirect_uri'  => GOOGLE_REDIRECT_URI,
            'response_type' => 'code',
            'scope'         => 'openid email profile',
            'access_type'   => 'offline',
            'prompt'        => 'select_account',
            'state'         => self::generateState()
        ];
        return 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
    }

    /**
     * Đổi authorization code lấy access token
     * @param string $code
     * @return array|false Token data
     */
    public static function getAccessToken($code) {
        $postData = [
            'code'          => $code,
            'client_id'     => GOOGLE_CLIENT_ID,
            'client_secret' => GOOGLE_CLIENT_SECRET,
            'redirect_uri'  => GOOGLE_REDIRECT_URI,
            'grant_type'    => 'authorization_code'
        ];

        $response = self::httpPost('https://oauth2.googleapis.com/token', $postData);
        if ($response && isset($response['access_token'])) {
            return $response;
        }
        return false;
    }

    /**
     * Lấy thông tin user từ Google
     * @param string $accessToken
     * @return array|false User info
     */
    public static function getUserInfo($accessToken) {
        $url = 'https://www.googleapis.com/oauth2/v2/userinfo?access_token=' . urlencode($accessToken);
        $response = self::httpGet($url);
        if ($response && isset($response['email'])) {
            return $response;
        }
        return false;
    }

    /**
     * Tạo CSRF state token
     * @return string
     */
    private static function generateState() {
        $state = bin2hex(random_bytes(16));
        $_SESSION['google_oauth_state'] = $state;
        return $state;
    }

    /**
     * Xác thực state token
     * @param string $state
     * @return bool
     */
    public static function verifyState($state) {
        $valid = isset($_SESSION['google_oauth_state']) && hash_equals($_SESSION['google_oauth_state'], $state);
        unset($_SESSION['google_oauth_state']);
        return $valid;
    }

    /**
     * HTTP POST request
     */
    private static function httpPost($url, $data) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query($data),
            CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded'],
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_TIMEOUT        => 15
        ]);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response ? json_decode($response, true) : false;
    }

    /**
     * HTTP GET request
     */
    private static function httpGet($url) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_TIMEOUT        => 15
        ]);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response ? json_decode($response, true) : false;
    }
}
