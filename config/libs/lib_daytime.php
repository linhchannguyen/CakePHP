<?php // -*-mode:php; coding:euc-jp-unix-*-
//------------------------------------------------------------------------------
/// @file   lib_daytime.php
/// @brief  時刻関連ユーティリティ
/// @author Yuichi Nakamura
/// @date   Time-stamp: "2010-03-17 13:39:21"
//------------------------------------------------------------------------------
if (!function_exists('date_parse')) {
    //------------------------------------------------
    /// @brief  PHP4でdate_parseをシミュレートする
    /// @param  $str    strtotimeが理解できる日時文字列
    /// @retval false   失敗
    /// @retval !false  結果配列
    ///                 - Array
    ///                 - (
    ///                 -     [year]        年
    ///                 -     [month]       月
    ///                 -     [day]         日
    ///                 -     [hour]        時
    ///                 -     [minute]      分
    ///                 -     [second]      秒
    ///                 -     [fraction]    ？
    ///                 -     [warning_count]
    ///                 -     [warnings]
    ///                 -     [error_count]
    ///                 -     [errors]
    ///                 -     [is_localtime]
    ///                 - )
    /// @author Yuichi Nakamura
    //------------------------------------------------
    function date_parse($str)
    {
        $tm = strtotime($str);
        if (false === $tm) {
            return false;
        }
        $info = getdate($tm);
        $ret = array('year'          => $info['year'],
                     'month'         => $info['mon'],
                     'day'           => $info['mday'],
                     'hour'          => $info['hours'],
                     'minute'        => $info['minutes'],
                     'second'        => $info['seconds'],
                     'fraction'      => 0,
                     'warning_count' => 0,
                     'warnings'      => array(),
                     'error_count'   => 0,
                     'errors'        => array(),
                     'is_localtime' => true,

            );
        return $ret;
    }
}


//------------------------------------------------
/// @brief  正しい時刻か検査する (checkdateの時刻版)
/// @param  $hour   時
/// @param  $min    分
/// @param  $sec    秒
/// @note   http://cha.sblo.jp/article/19104724.html
/// @author Yuichi Nakamura
//------------------------------------------------
function checktime($hour, $min, $sec)
{
    if ($hour < 0 || 23 < $hour) {
        return(false);
    }
    if ($min < 0 || 59 < $min) {
        return(false);
    }
    if ($sec < 0 || 59 < $sec) {
        return(false);
    }
    return(true);
}


//------------------------------------------------
/// @brief  日付の検査
/// @param  $str    日付文字列
/// @retval true    日付文字列である
/// @retval false   日付文字列でない
/// @note   http://cha.sblo.jp/article/19104724.html
/// @author Yuichi Nakamura
//------------------------------------------------
function isDate($str)
{
    if (preg_match("/^([0-9]{4})[\/\-\.]([01]?[0-9])[\/\-\.]([0123]?[0-9])$/", $str, $parts)) {
        return checkdate($parts[2], $parts[3], $parts[1]);
    }
    return false;
}


//------------------------------------------------
/// @brief  時刻を検査する
/// @param  $str    時刻文字列
/// @retval true    時刻文字列である
/// @retval false   時刻文字列でない
/// @note   http://cha.sblo.jp/article/19104724.html
/// @author Yuichi Nakamura
//------------------------------------------------
function isTime($str) {
    if (preg_match("/^([012]?[0-9])[:\.]([0-6]?[0-9])[:\.]([0-6]?[0-9])$/", $str, $parts)) {
        return checktime($parts[1], $parts[2], $parts[3]);
    }
    else {
        if (preg_match("/^([012]?[0-9])[:\.]([0-6]?[0-9])$/", $str, $parts)) {
            return checktime($parts[1], $parts[2], '0');
        }
    }
    return false;
}


//------------------------------------------------
/// @brief  日時を検査する
/// @param  $str    日時文字列
/// @retval true    日時文字列である
/// @retval false   日時文字列でない
/// @note   http://cha.sblo.jp/article/19104724.html
/// @author Yuichi Nakamura
//------------------------------------------------
function isDateTime($str) {
    // 秒も指定されている場合 -> 2010-03-05 15:24:39
    if (preg_match("/^([0-9]{4})[\/\-\.]([01]?[0-9])[\/\-\.]([0123]?[0-9])[ ]([012]?[0-9])[:\.]([0-6]?[0-9])[:\.]([0-6]?[0-9])$/", $str, $parts)) {
        return checkdate(intval($parts[2]), intval($parts[3]), intval($parts[1])) && checktime(intval($parts[4]), intval($parts[5]), intval($parts[6]));
    }
    // 秒が省略されている場合 -> 2010-03-05 15:24
    else if (preg_match("/^([0-9]{4})[\/\-\.]([01]?[0-9])[\/\-\.]([0123]?[0-9])[ ]([012]?[0-9])[:\.]([0-6]?[0-9])$/", $str, $parts)) {
        return checkdate($parts[2], $parts[3], $parts[1]) && checktime($parts[4], $parts[5], '00');
    }
    return false;
}



//------------------------------------------------------------------------------
// end of file
//------------------------------------------------------------------------------
// ?>
