<?php

use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase
{
    public function testRequiredRule_passes_whenFieldPresent()
    {
        $errors = Validator::make(['name' => 'John'], ['name' => 'required']);

        $this->assertEmpty($errors, 'Không được có lỗi khi field có giá trị');
    }

    public function testRequiredRule_fails_whenFieldEmpty()
    {
        $errors = Validator::make(['name' => ''], ['name' => 'required']);

        $this->assertNotEmpty($errors);
        $this->assertArrayHasKey('name', $errors);
    }

    public function testEmailRule_fails_whenInvalid()
    {
        $errors = Validator::make(['email' => 'notanemail'], ['email' => 'required|email']);

        $this->assertArrayHasKey('email', $errors);
    }

    public function testEmailRule_passes_whenValid()
    {
        $errors = Validator::make(['email' => 'test@example.com'], ['email' => 'required|email']);

        $this->assertEmpty($errors);
    }

    public function testMinRule_fails_whenTooShort()
    {
        $errors = Validator::make(['password' => 'ab'], ['password' => 'required|min:6']);

        $this->assertArrayHasKey('password', $errors);
    }

    public function testMinRule_passes_whenLongEnough()
    {
        $errors = Validator::make(['password' => 'password123'], ['password' => 'required|min:6']);

        $this->assertEmpty($errors);
    }

    public function testCombinedRules_returnsAllErrors()
    {
        $errors = Validator::make(
            ['username' => '', 'email' => 'bad'],
            ['username' => 'required|min:3', 'email' => 'required|email']
        );

        $this->assertCount(2, $errors);
        $this->assertArrayHasKey('username', $errors);
        $this->assertArrayHasKey('email', $errors);
    }

    public function testMissingOptionalField_noError()
    {
        $errors = Validator::make([], ['bio' => 'max:200']);

        $this->assertEmpty($errors, 'Field tùy chọn không có trong data không được báo lỗi');
    }
}
