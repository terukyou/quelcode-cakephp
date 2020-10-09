<?php

namespace App\Model\Validation;

use Cake\Validation\Validation;

// カスタムバリデーションの作成
class CustomValidation extends Validation
{
    // 空白文字のみの場合falseを返す
    public function NotBlankOnly($value)
    {
        return !(bool)preg_match("/^[ 　\t\r\n]+$/", $value);
    }
    // 拡張子がpng・jpeg・jpg・gifのいずれかに一致する場合はtrue
    public function isImageExtension($value)
    {
        $check_array = array(1 => 'png', 2 => 'jpeg', 3 => 'jpg', 4 => 'gif');
        $Extension = mb_strtolower($value);
        if (array_search($Extension, $check_array)) {
            return true;
        }
    }
}
