<?php

namespace Viviniko\Promotion\Enums;

class CouponFormat
{
    const ALPHANUMERIC  = 'alphanum';
    const ALPHABETICAL  = 'alpha';
    const NUMERIC       = 'num';

    private static $formats = [
        self::ALPHANUMERIC  => '字母数字',
        self::ALPHABETICAL  => '字母',
        self::NUMERIC       => '数字'
    ];

    private static $fmtChars = [
        self::ALPHANUMERIC  => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789',
        self::ALPHABETICAL  => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
        self::NUMERIC       => '0123456789'
    ];

    public static function getChars($fmt) {
        return self::$fmtChars[$fmt];
    }

    public static function getFormats() {
        return self::$formats;
    }

    public static function randStr($chars, $length, $dash, $prefix, $suffix) {
        $str = substr(str_shuffle(str_repeat($chars, $length)), 0, $length);
        $str = $prefix.$str.$suffix;
        $strLen = strlen($str);
        if ($dash > 0) {
            $newChars = [];
            for ($i = 0; $i < $strLen; $i ++) {
                if ($i != 0 && $i % $dash == 0) {
                    $newChars[] = '-';
                }
                $newChars[] = $str[$i];
            }
            $str = implode($newChars);
        }
        return strtoupper($str);
    }
}