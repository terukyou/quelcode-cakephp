<?php

namespace App\Model\Validation;

use Cake\Validation\Validation;

class CustomValidation extends Validation
{
    // 数字とハイフンのみ許可
    public static function isPhoneNumber($value)
    {
        return (bool)preg_match("/^[0-9\-]+$/", $value);
    }

    // 空白文字のみの場合falseを返す
    public function NotBlankOnly($value)
    {
        return !(bool)preg_match("/^[ 　\t\r\n]+$/", $value);
    }
    // 拡張子がpng・jpeg・jpg・gifのいずれかに一致する場合はtrue
    public function isImageExtension($value)
    {
        $check_ok_array = ['png', 'jpeg', 'jpg', 'gif'];
        $extension = mb_strtolower($value);
        return (bool)array_search($extension, $check_ok_array);
    }
}
