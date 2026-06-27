<?php

namespace App\Helpers;

/**
 * Validator — tập trung validate dữ liệu đầu vào
 * Trả về mảng lỗi (rỗng nếu hợp lệ)
 *
 * Usage:
 *   $errors = Validator::make($data, ['email' => 'required|email', 'name' => 'required|min:3']);
 *   if (!empty($errors)) { throw new ValidationException('Invalid', $errors); }
 */
class Validator
{
    /** @var array */
    private $data;

    /** @var array */
    private $errors = [];

    private function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Validate dữ liệu theo rules
     *
     * @param  array $data  Dữ liệu cần validate
     * @param  array $rules Rules [field => 'rule1|rule2']
     * @return array Mảng lỗi [field => [messages]], rỗng nếu OK
     */
    public static function make(array $data, array $rules): array
    {
        $instance = new self($data);

        foreach ($rules as $field => $ruleString) {
            $ruleList = explode('|', $ruleString);
            foreach ($ruleList as $rule) {
                $params = [];
                if (strpos($rule, ':') !== false) {
                    [$rule, $paramStr] = explode(':', $rule, 2);
                    $params = explode(',', $paramStr);
                }
                $method = 'rule' . ucfirst($rule);
                if (method_exists($instance, $method)) {
                    $instance->{$method}($field, $params);
                }
            }
        }

        return $instance->errors;
    }

    /**
     * @param  string $field
     * @param  string $message
     */
    private function addError(string $field, string $message): void
    {
        $this->errors[$field][] = $message;
    }

    /**
     * @param  string $field
     * @return mixed
     */
    private function value(string $field)
    {
        return $this->data[$field] ?? null;
    }

    // ─── Validation Rules ──────────────────────────────

    /** @param array $params */
    private function ruleRequired(string $field, array $params): void
    {
        $value = trim((string) $this->value($field));
        if ($value === '') {
            $this->addError($field, 'Trường này là bắt buộc.');
        }
    }

    /** @param array $params */
    private function ruleEmail(string $field, array $params): void
    {
        $value = $this->value($field);
        if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->addError($field, 'Email không hợp lệ.');
        }
    }

    /** @param array $params */
    private function ruleMin(string $field, array $params): void
    {
        $min = (int) ($params[0] ?? 0);
        $value = $this->value($field);
        if (!empty($value) && strlen((string) $value) < $min) {
            $this->addError($field, "Phải có ít nhất {$min} ký tự.");
        }
    }

    /** @param array $params */
    private function ruleMax(string $field, array $params): void
    {
        $max = (int) ($params[0] ?? 0);
        $value = $this->value($field);
        if (!empty($value) && strlen((string) $value) > $max) {
            $this->addError($field, "Tối đa {$max} ký tự.");
        }
    }

    /** @param array $params */
    private function ruleNumeric(string $field, array $params): void
    {
        $value = $this->value($field);
        if (!empty($value) && !is_numeric($value)) {
            $this->addError($field, 'Phải là số.');
        }
    }

    /** @param array $params */
    private function ruleInt(string $field, array $params): void
    {
        $value = $this->value($field);
        if ($value !== null && $value !== '' && !filter_var($value, FILTER_VALIDATE_INT)) {
            $this->addError($field, 'Phải là số nguyên.');
        }
    }

    /** @param array $params */
    private function ruleAlphanumeric(string $field, array $params): void
    {
        $value = $this->value($field);
        if (!empty($value) && !preg_match('/^[a-zA-Z0-9_]+$/', (string) $value)) {
            $this->addError($field, 'Chỉ chứa chữ cái, số và dấu gạch dưới.');
        }
    }

    /** @param array $params */
    private function ruleIn(string $field, array $params): void
    {
        $value = $this->value($field);
        if (!empty($value) && !in_array($value, $params, true)) {
            $this->addError($field, 'Giá trị không hợp lệ.');
        }
    }
}
