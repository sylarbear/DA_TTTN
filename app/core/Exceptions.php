<?php

/**
 * ValidationException — dùng khi dữ liệu đầu vào không hợp lệ
 * Controller sẽ catch exception này và trả về HTTP 422 với message thân thiện
 */
class ValidationException extends Exception
{
    /** @var array */
    protected $errors;

    public function __construct($message = 'Dữ liệu không hợp lệ.', array $errors = [], $code = 422)
    {
        parent::__construct($message, $code);
        $this->errors = $errors;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
}

/**
 * NotFoundException — dùng khi resource không tồn tại
 */
class NotFoundException extends Exception
{
    public function __construct($message = 'Không tìm thấy tài nguyên.', $code = 404)
    {
        parent::__construct($message, $code);
    }
}

/**
 * UnauthorizedException — dùng khi chưa đăng nhập
 */
class UnauthorizedException extends Exception
{
    public function __construct($message = 'Vui lòng đăng nhập để tiếp tục.', $code = 401)
    {
        parent::__construct($message, $code);
    }
}

/**
 * ForbiddenException — dùng khi không có quyền
 */
class ForbiddenException extends Exception
{
    public function __construct($message = 'Bạn không có quyền thực hiện hành động này.', $code = 403)
    {
        parent::__construct($message, $code);
    }
}
