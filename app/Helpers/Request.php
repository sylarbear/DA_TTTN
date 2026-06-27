<?php

namespace App\Helpers;

/**
 * Request helper — các method tiện ích xử lý HTTP request
 */
class Request
{
    /**
     * Lấy JSON body từ request (đã parse)
     * @return array
     */
    public static function json()
    {
        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true);

        return is_array($data) ? $data : [];
    }

    /**
     * Kiểm tra request có phải AJAX không
     * @return bool
     */
    public static function isAjax()
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Lấy giá trị từ $_POST (đã trim)
     * @param  string     $key
     * @param  mixed|null $default
     * @return mixed
     */
    public static function post($key, $default = null)
    {
        return isset($_POST[$key]) ? trim($_POST[$key]) : $default;
    }

    /**
     * Lấy giá trị từ $_GET (đã trim)
     * @param  string     $key
     * @param  mixed|null $default
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        return isset($_GET[$key]) ? trim($_GET[$key]) : $default;
    }

    /**
     * Lấy HTTP method
     * @return string
     */
    public static function method()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Kiểm tra HTTP method
     * @param  string $method
     * @return bool
     */
    public static function isMethod($method)
    {
        return strtoupper($_SERVER['REQUEST_METHOD']) === strtoupper($method);
    }
}
