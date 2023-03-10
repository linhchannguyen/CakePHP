<?php // -*-mode:php; coding:euc-jp-unix-*-

namespace App\Traits;

trait dateMiscTrait
{
    //------------------------------------------------------------------------------
    /// @file   dateMisc.php
    /// @brief  簡単日付計算ユーティリティ
    /// @author ChanNL
    /// @date   Time-stamp: "2010-03-17 17:11:38"
    //------------------------------------------------------------------------------
    //------------------------------------------------
    /// @brief  月初日を取得する
    /// @param  $ym         年月 (Y-m (xxxx-xx)形式)
    /// @param  $zero_fill  先頭をゼロで埋めるかどうか (デフォルトはゼロフィルする)
    /// @return ISO表記の年月
    /// @author ChanNL
    //------------------------------------------------
    function getFirstDayOfMonth($ym, $zero_fill = true)
    {
        $fmt = $zero_fill ? 'Y-m-d' : 'Y-n-j';
        list($y, $m) = explode('-', $ym);
        return date($fmt, mktime(0, 0, 0, $m, 1, $y));
    }


    //------------------------------------------------
    /// @brief  月末日を取得する
    /// @param  $ym         年月 (Y-m (xxxx-xx)形式)
    /// @param  $zero_fill  先頭をゼロで埋めるかどうか (デフォルトはゼロフィルする)
    /// @return ISO表記の年月
    /// @author ChanNL
    //------------------------------------------------
    function getLastDayOfMonth($ym, $zero_fill = true)
    {
        $fmt = $zero_fill ? 'Y-m-d' : 'Y-n-j';
        list($y, $m) = explode('-', $ym);
        return date($fmt, mktime(0, 0, 0, $m + 1, 0, $y));
    }


    //------------------------------------------------
    /// @brief  年月のリストを生成する
    /// @param  $from   開始年月 (Y-m (xxxx-xx)形式)
    /// @param  $count  月数
    /// @return ISO表記の年月配列
    /// @code
    /// [使い方]
    /// $from = date('Y-m', strtotime(date('Y-m-1') . ' -1 month'));    // 前月
    /// $ym = genYearMonthList($from, 3);
    /// @endcode
    /// @author ChanNL
    //------------------------------------------------
    function genYearMonthList($from, $count)
    {
        $from = $from . '-1';       // 1日

        $listym = array();
        for ($i = 0; $i < $count; $i++) {
            $ym = date('Y-m', strtotime($from . "+{$i} month"));
            $listym[$ym] = $ym;
        }
        return $listym;
    }

    //------------------------------------------------
    /// @brief  日付のリストを生成する
    /// @param  $from       開始日 (int)
    /// @param  $count      日数
    /// @param  $zero_fill  先頭をゼロで埋めるかどうか (デフォルトはゼロフィルする)
    /// @return ISO表記の年月配列
    /// @code
    /// [使い方]
    /// $ym = genDayList(1, 31);
    /// @endcode
    /// @author ChanNL
    //------------------------------------------------
    function genDayList($from, $count, $zero_fill = true)
    {
        $fmt = $zero_fill ? '%02d' : '%d';

        $listd = array();
        for ($i = 0; $i < $count; $i++) {
            $d = sprintf($fmt, $from + $i);
            $listd[$d] = $d;
        }
        return $listd;
    }


    //------------------------------------------------
    /// @brief  「時」、「分」のリストを生成する
    /// @param  $from       開始時間
    /// @param  $to         終了時間
    /// @param  $step       ステップ
    /// @param  $zero_fill  先頭をゼロで埋めるかどうか (デフォルトはゼロフィルする/2桁まで)
    /// @param  $digits     ゼロフィルする場合の全体の桁数
    /// @note   終了時刻はリストに含めません。
    /// @code
    /// [使い方]
    /// $ym = genHourMinutList(0, 180);
    /// @endcode
    /// @author Yuichi Nakamura
    //------------------------------------------------
    function genHourMinutList($from, $to, $step, $zero_fill=true, $digits=2)
    {
        $fmt = ($zero_fill) ? "%0{$digits}d" : '%d';
    
        $li = array();
        for ($i = $from; $i < $to; $i += $step) {
            $h = sprintf($fmt, $i);
            $li[$h] = $h;
        }
    
        return $li;
    }


    //------------------------------------------------
    /// @brief  「時」のリストを生成する
    /// @param  $from       開始時間 (時)
    /// @param  $to         終了時間 (時)
    /// @param  $step       ステップ (hour)
    /// @param  $zero_fill  先頭をゼロで埋めるかどうか (デフォルトはゼロフィルする/2桁まで)
    /// @param  $digits     ゼロフィルする場合の全体の桁数
    /// @note   終了時刻はリストに含めません。
    /// @code
    /// [使い方]
    /// $ym = genHourList(0, 24);
    /// @endcode
    /// @author Yuichi Nakamura
    //------------------------------------------------
    function genHourList($from, $to, $step, $zero_fill=true, $digits=2)
    {
        return $this->genHourMinutList($from, $to, $step, $zero_fill, $digits);
    }
    
    
    //------------------------------------------------
    /// @brief  「分」のリストを生成する
    /// @param  $from       開始時間 (分)
    /// @param  $to         終了時間 (分)
    /// @param  $step       ステップ (minut)
    /// @param  $zero_fill  先頭をゼロで埋めるかどうか (デフォルトはゼロフィルする/2桁まで)
    /// @param  $digits     ゼロフィルする場合の全体の桁数
    /// @note   終了時刻はリストに含めません。
    /// @code
    /// [使い方]
    /// $ym = genMinutList(0, 180);
    /// @endcode
    /// @author Yuichi Nakamura
    //------------------------------------------------
    function genMinutList($from, $to, $step, $zero_fill=true, $digits=2)
    {
        return $this->genHourMinutList($from, $to, $step, $zero_fill, $digits);
    }

    //------------------------------------------------
    /// @brief  ISO表記の時刻を00:00:00からの秒数に変換する
    /// @param  $time_str   秒数に変換する時刻文字列
    /// @return 秒数
    /// @author ChanNL
    //------------------------------------------------
    function timeStringToSecond($time_str)
    {
        $time_parts = explode(':', $time_str);      // 分、秒が省略されるときを考慮
        $par_seconds = 3600;
        $seconds = 0;
        for ($i = 0; $i < count($time_parts); $i++) {
            $seconds += intval((string)$time_parts[$i]) * $par_seconds;
            $par_seconds /= 60;
        }
        return $seconds;
    }

    //------------------------------------------------
    /// @brief  00:00:00からの秒数をISO表記の時刻文字列に変換する
    /// @param  $seconds            時刻文字列に変換する秒数
    /// @param  $with_second_field  秒も出力するかどうか (デフォルトは出力しない)
    /// @param  $zero_fill          先頭をゼロで埋めるかどうか (デフォルトはゼロフィルする)
    /// @return ISO表記の時刻文字列
    /// @author ChanNL
    //------------------------------------------------
    function secondsToTimeString($seconds, $with_second_field = false, $zero_fill = true)
    {
        $hour = floor($seconds / 3600);
        $seconds %= 3600;

        $minuts = floor($seconds / 60);
        $seconds %= 60;

        if ($with_second_field) {
            $fmt = $zero_fill ? '%02d:%02d:%02d' : '%d:%d:%d';
        } else {
            $fmt = $zero_fill ? '%02d:%02d' : '%d:%d';
        }
        return sprintf($fmt, $hour, $minuts, $seconds);
    }

    //------------------------------------------------
    /// @brief  時刻のリストを生成する
    /// @param  $from       開始時刻 ('00:00:00'形式)
    /// @param  $to         終了時刻 ('00:00:00'形式)
    /// @param  $step_sec   ステップ (秒数)
    /// @param  $zero_fill  先頭をゼロで埋めるかどうか (デフォルトはゼロフィルする)
    /// @return ISO表記の時刻配列
    /// @note   終了時刻はリストに含めません。
    /// @code
    /// [使い方]
    /// $ym = genTimeList(0, 24);
    /// @endcode
    /// @author ChanNL
    //------------------------------------------------
    function genTimeList($from, $to, $step_sec, $zero_fill = true)
    {
        $from_as_second = $this->timeStringToSecond($from);
        $to_as_second = $this->timeStringToSecond($to);

        $listtm = array();
        for ($sec = $from_as_second; $sec < $to_as_second; $sec += $step_sec) {
            $time = $this->secondsToTimeString($sec, false, $zero_fill);
            $listtm[$time] = $time;
        }
        return $listtm;
    }

    //------------------------------------------------
    /// @brief  配列から要素の値を取得する (デフォルト値付)
    /// @param  $ary        値を取得する配列
    /// @param  $key        取得する値のキー
    /// @param  $default    値が見つからなかったときのデフォルト値 (省略したときはnull)
    /// @author ChanNL
    //------------------------------------------------
    function arrayGet($ary, $key, $default = null)
    {
        if (true == isset($ary[$key])) {
            return $ary[$key];
        } else {
            return $default;
        }
    }

    //------------------------------------------------
    /// @brief  正しい時刻か検査する (checkdateの時刻版)
    /// @param  $hour   時
    /// @param  $min    分
    /// @param  $sec    秒
    /// @note   http://cha.sblo.jp/article/19104724.html
    /// @author ChanNL
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
    /// @author ChanNL
    //------------------------------------------------
    function isDate($str)
    {
        if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $str, $parts)) {
            return checkdate($parts[1], $parts[2], substr($parts[0], 0, 4));
        }
        return false;
    }

    //------------------------------------------------
    /// @brief  日時を検査する
    /// @param  $str    日時文字列
    /// @retval true    日時文字列である
    /// @retval false   日時文字列でない
    /// @note   http://cha.sblo.jp/article/19104724.html
    /// @author ChanNL
    //------------------------------------------------
    function isDateTime($str) {
        // 秒も指定されている場合 -> 2010-03-05 15:24:39
        if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1]) (0[0-9]|1[0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/", $str, $parts)){
            return checkdate($parts[1], $parts[2], substr($parts[0], 0, 4)) && $this->checktime($parts[3], $parts[4], $parts[5]);
        }
        // 秒が省略されている場合 -> 2010-03-05 15:24
        else if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1]) (0[0-9]|1[0-9]|2[0-3]):([0-5][0-9])$/", $str, $parts)) {
            return checkdate($parts[1], $parts[2], substr($parts[0], 0, 4)) && $this->checktime($parts[3], $parts[4], '00');
        }
        return false;
    }

    //------------------------------------------------
    /// @brief  文字エンコーディング変換(to SJIS)
    /// @param  $str        ソース文字列(あるいは文字列配列)
    /// @param  $enc_from   変換元のエンコーディング形式
    /// @return 変換後の文字列(あるいは文字列配列)
    /// @author ChanNL
    //------------------------------------------------
    function toSJIS($str, $enc_from = null)
    {
        if (is_null($enc_from)) {
            $enc_from = EFP_ENCODING_DETECT_ORDER;
        }
        if (is_array($str)) {
            mb_convert_variables('SJIS-win', $enc_from, $str);
            return $str;
        } else {
            return mb_convert_encoding($str, 'SJIS-win', $enc_from);
        }
    }

    //------------------------------------------------
    /// @brief  改行コードをbrタグに変換する
    /// @param  $str    変換する文字列
    /// @return 変換後の文字列
    /// @note   文字エンコーディングは EFP_SRC_ENCODING を想定しています。
    /// @author ChanNL
    //------------------------------------------------
    function lfToBr($str)
    {
        return mb_ereg_replace("\n", '<br />', $str);
    }

    function checkAndFormatDate($date, $format = 'Y-m-d H:i:s')
    {
        if ($date) {
            return $date->format($format);
        }
        return $date;
    }

    //------------------------------------------------------------------------------
    // end of file
    //------------------------------------------------------------------------------
    //
}
