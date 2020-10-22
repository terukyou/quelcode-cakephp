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
}
