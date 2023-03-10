<?php // -*-mode:php; coding:euc-jp-unix-*-
//------------------------------------------------------------------------------
/// @file
/// @brief  文字列処理関連ユーティリティ
/// @author Yuichi Nakamura
/// @date   Time-stamp: "2011-08-29 17:09:50"
//------------------------------------------------------------------------------
//------------------------------------------------
/// @brief  文字エンコーディング変換(to EUCJP-WIN)
/// @param  $str        ソース文字列(あるいは文字列配列)
/// @param  $enc_from   変換元のエンコーディング形式
/// @return 変換後の文字列(あるいは文字列配列)
/// @author Yuichi Nakamura
//------------------------------------------------
function toEUCJP_WIN($str, $enc_from = null)
{
    if (is_null($enc_from)) {
        $enc_from = EFP_ENCODING_DETECT_ORDER;
    }
    if (is_array($str)) {
        mb_convert_variables('EUCJP-WIN', $enc_from, $str);
        return $str;
    }
    else {
        return mb_convert_encoding($str, 'EUCJP-WIN', $enc_from);
    }
}


//------------------------------------------------
/// @brief  文字エンコーディング変換(to EUC)
/// @param  $str        ソース文字列(あるいは文字列配列)
/// @param  $enc_from   変換元のエンコーディング形式
/// @return 変換後の文字列(あるいは文字列配列)
/// @author Yuichi Nakamura
//------------------------------------------------
function toEUC($str, $enc_from = null)
{
    if (is_null($enc_from)) {
        $enc_from = EFP_ENCODING_DETECT_ORDER;
    }
    if (is_array($str)) {
        mb_convert_variables('UTF-8', $enc_from, $str);
        return $str;
    }
    else {
        return mb_convert_encoding($str, 'UTF-8', $enc_from);
    }
}


//------------------------------------------------
/// @brief  文字エンコーディング変換(to SJIS)
/// @param  $str        ソース文字列(あるいは文字列配列)
/// @param  $enc_from   変換元のエンコーディング形式
/// @return 変換後の文字列(あるいは文字列配列)
/// @author Yuichi Nakamura
//------------------------------------------------
function toSJIS($str, $enc_from = null)
{
    if (is_null($enc_from)) {
        $enc_from = EFP_ENCODING_DETECT_ORDER;
    }
    if (is_array($str)) {
        mb_convert_variables('SJIS-win', $enc_from, $str);
        return $str;
    }
    else {
        return mb_convert_encoding($str, 'SJIS-win', $enc_from);
    }
}


//------------------------------------------------
/// @brief  文字エンコーディング変換(to JIS)
/// @param  $str        ソース文字列(あるいは文字列配列)
/// @param  $enc_from   変換元のエンコーディング形式
/// @return 変換後の文字列(あるいは文字列配列)
/// @author Yuichi Nakamura
//------------------------------------------------
function toJIS($str, $enc_from = null)
{
    if (is_null($enc_from)) {
        $enc_from = EFP_ENCODING_DETECT_ORDER;
    }
    if (is_array($str)) {
        mb_convert_variables('JIS', $enc_from, $str);
        return $str;
    }
    else {
        return mb_convert_encoding($str, 'JIS', $enc_from);
    }
}


//------------------------------------------------
/// @brief  文字エンコーディング変換(to UTF8)
/// @param  $str        ソース文字列(あるいは文字列配列)
/// @param  $enc_from   変換元のエンコーディング形式
/// @return 変換後の文字列(あるいは文字列配列)
/// @author Yuichi Nakamura
//------------------------------------------------
function toUTF8($str, $enc_from = null)
{
    if (is_null($enc_from)) {
        $enc_from = EFP_ENCODING_DETECT_ORDER;
    }
    if (is_array($str)) {
        mb_convert_variables('UTF-8', $enc_from, $str);
        return $str;
    }
    else {
        return mb_convert_encoding($str, 'UTF-8', $enc_from);
    }
}


//------------------------------------------------
/// @brief  全角文字対応trim
/// @param  $str    ソース文字列
/// @param  $chars  削除したい文字
/// @return 変換結果文字列
/// @note   cf. (url:http://note.area93.net/it/?p=71)
/// @author Yuichi Nakamura
//------------------------------------------------
function mbTrimSimple($str, $chars="\s　")
{
    $str = mb_ereg_replace("^[$chars]+", "", $str);
    $str = mb_ereg_replace("[$chars]+$", "", $str);
    return $str;
}


//------------------------------------------------
/// @brief  行頭行末の空白を削除(マルチバイト対応)
/// @param  $str            文字列
/// @param  $str_encoding   受け取った文字列のエンコーディング
/// @retval 空白を削除された文字列
/// @author Yuichi Nakamura
//------------------------------------------------
function mbTrim($str, $str_encoding='auto')
{
    mb_language("ja") ;
    $src_encoding = mb_internal_encoding();

    $chars = '\s　';
    $str = mb_convert_encoding($str, $src_encoding, $str_encoding);

    $str = mb_ereg_replace("^[$chars]+", "", $str);
    $str = mb_ereg_replace("[$chars]+$", "", $str);

    $str = mb_convert_encoding($str, $str_encoding, $src_encoding);

    return $str;
}


//------------------------------------------------
/// @brief  指定した範囲の文字列長かどうか検査する(半角ASCII専用)
/// @param  $min    最低長('' もしくは null で省略)
/// @param  $max    最大長('' もしくは null で省略)
/// @param  $str    検査する文字列
/// @retval true    指定された範囲の文字列長($min <= $str <= $max)
/// @retval false   指定外の文字列長
/// @author Yuichi Nakamura
//------------------------------------------------
function testStrLen($min, $max, $str)
{
    $min = is_null($min) ? 0 : $min;
    $max = is_null($max) ? 0 : $max;
    $pattern = sprintf('/^.{%d,%d}$/', $min, $max);

    if (preg_match($pattern, $str)) {
        return true;
    }
    return false;
}


//------------------------------------------------
/// @brief  指定した範囲の文字列長かどうか検査する(全角対応)
/// @param  $min    最低長('' もしくは null で省略)
/// @param  $max    最大長('' もしくは null で省略)
/// @param  $str    検査する文字列
/// @retval true    指定された範囲の文字列長($min <= $str <= $max)
/// @retval false   指定外の文字列長
/// @author Yuichi Nakamura
//------------------------------------------------
function testMbStrLen($min, $max, $str)
{
    $min = is_null($min) ? 0 : $min;
    $max = is_null($max) ? 0 : $max;
    $pattern = sprintf('^.{%d,%d}$', $min, $max);

    if (mb_ereg($pattern, $str)) {
        return true;
    }
    return false;
}


//------------------------------------------------
/// @brief  文字列が半角英数字だけで構成されているか検査する
/// @param  $str    検査する文字列
/// @retval true    半角英数字だけで構成されている
/// @retval false   他の文字を含んでいる
/// @author Yuichi Nakamura
//------------------------------------------------
function isAlNum($str)
{
    if (preg_match("/^[a-zA-Z0-9]+$/", $str)) {
        return true;
    }
    return false;
}


//------------------------------------------------
/// @brief  文字列が半角数字だけで構成されているか検査する
/// @param  $str    検査する文字列
/// @retval true    半角数字だけで構成されている
/// @retval false   他の文字を含んでいる
/// @author Yuichi Nakamura
//------------------------------------------------
function isNum($str)
{
    if (preg_match("/^[0-9]+$/", $str)) {
        return true;
    }
    return false;
}


//------------------------------------------------
/// @brief  文字列が半角英字だけで構成されているか検査する
/// @param  $str    検査する文字列
/// @retval true    半角英字だけで構成されている
/// @retval false   他の文字を含んでいる
/// @author Yuichi Nakamura
//------------------------------------------------
function isAlpha($str)
{
    if (preg_match("/^[a-zA-Z]+$/", $str)) {
        return true;
    }
    return false;
}


//------------------------------------------------
/// @brief  文字列が全角ひらがなだけで構成されているか検査する
/// @param  $str    検査する文字列
/// @param  $encode 検査するエンコーディング形式の指定
/// @retval true    全角ひらがなだけで構成されている
/// @retval false   他の文字を含んでいる
/// @author Yuichi Nakamura
//------------------------------------------------
function isZenHira($str, $encode='')
{
    $pattern = '^[ぁ-ん]+$';

    if (!empty($encode) || (strtolower(EFP_SRC_ENCODING) !== strtolower(EFP_LIB_ENCODING))) {
        $pattern = mb_convert_encoding($pattern, $encode, EFP_SRC_ENCODING);
    }
    if (mb_ereg($pattern, $str)) {
        return true;
    }
    return false;
}


//------------------------------------------------
/// @brief  文字列が全角カタカナだけで構成されているか検査する
/// @param  $str    検査する文字列
/// @param  $encode 検査するエンコーディング形式の指定
/// @retval true    全角カタカナだけで構成されている
/// @retval false   他の文字を含んでいる
/// @author Yuichi Nakamura
//------------------------------------------------
function isZenKatakana($str, $encode='')
{
    $pattern = '^[ァ-ヶー]+$';
    if (!empty($encode)) {
        $pattern = mb_convert_encoding($pattern, $encode, EFP_LIB_ENCODING);
    }
    else if (strtolower(EFP_SRC_ENCODING) !== strtolower(EFP_LIB_ENCODING)) {
        $pattern = mb_convert_encoding($pattern, EFP_SRC_ENCODING, EFP_LIB_ENCODING);
    }
    if (mb_ereg($pattern, $str)) {
        return true;
    }
    return false;
}


//------------------------------------------------
/// @brief  文字列が半角カタカナだけで構成されているか検査する
/// @param  $str    検査する文字列
/// @param  $encode 検査するエンコーディング形式の指定
/// @retval true    半角カタカナだけで構成されている
/// @retval false   他の文字を含んでいる
/// @author Yuichi Nakamura
//------------------------------------------------
function isHanKatakana($str, $encode='')
{
    $pattern = '^[ア-ン゛゜]+$';
    if (!empty($encode)) {
        $pattern = mb_convert_encoding($pattern, $encode, EFP_LIB_ENCODING);
    }
    else if (strtolower(EFP_SRC_ENCODING) !== strtolower(EFP_LIB_ENCODING)) {
        $pattern = mb_convert_encoding($pattern, EFP_SRC_ENCODING, EFP_LIB_ENCODING);
    }
    if (mb_ereg($pattern, $str)) {
        return true;
    }
    return false;
}


//------------------------------------------------
/// @brief  文字列が半角カタカナを含むか検査する
/// @param  $str    検査する文字列
/// @param  $encode 検査するエンコーディング形式の指定
/// @retval true    半角カタカナを含んでいる
/// @retval false   半角カタカナを含まない
/// @author Yuichi Nakamura
//------------------------------------------------
function isThereHanKatakana($str, $encode='')
{
    $pattern = '[ア-ン゛゜]';
    if (!empty($encode)) {
        $pattern = mb_convert_encoding($pattern, $encode, EFP_LIB_ENCODING);
    }
    else if (strtolower(EFP_SRC_ENCODING) !== strtolower(EFP_LIB_ENCODING)) {
        $pattern = mb_convert_encoding($pattern, EFP_SRC_ENCODING, EFP_LIB_ENCODING);
    }
    if (mb_ereg($pattern, $str)) {
        return true;
    }
    return false;
}


//------------------------------------------------
/// @brief  文字列が「全角カナ、長音、全角半角スペース」だけで構成されているか検査する
/// @param  $str    検査する文字列
/// @param  $encode 検査するエンコーディング形式の指定
/// @retval true    「全角カナ、長音、全角半角スペース」だけで構成されている
/// @retval false   他の文字を含んでいる
/// @author Yuichi Nakamura
//------------------------------------------------
function isZenKanaStr($str, $encode)
{
    $pattern = '^[ァ-ヶー 　]*$';
    if (!empty($encode)) {
        $pattern = mb_convert_encoding($pattern, $encode, EFP_LIB_ENCODING);
    }
    else if (strtolower(EFP_SRC_ENCODING) !== strtolower(EFP_LIB_ENCODING)) {
        $pattern = mb_convert_encoding($pattern, EFP_SRC_ENCODING, EFP_LIB_ENCODING);
    }
    if (mb_ereg($pattern, $str)) {
        return true;
    }
    return false;
}


//------------------------------------------------
/// @brief  文字列がURLか検査する
/// @param  $str    検査する文字列
/// @retval true    URLである
/// @retval false   URLではない
/// @author Yuichi Nakamura
//------------------------------------------------
function isUrl($str) {
    if (preg_match('/^(https?|ftp)(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)$/', $str)) {
        return true;
    }
    return false;
}


//------------------------------------------------
/// @brief  文字列がメールアドレスか検査する
/// @param  $str    検査する文字列
/// @retval true    メールアドレスである
/// @retval false   メールアドレスではない
/// @author Yuichi Nakamura
//------------------------------------------------
function isEmailAddress($str) {
    if (preg_match('/^[a-zA-Z0-9_\.\-]+?@[A-Za-z0-9_\.\-]+$/', $str)) {
        return true;
    }
    return false;
}


//------------------------------------------------
/// @brief  文字列が郵便番号か検査する(ハイフン付きの形式を検査します)
/// @param  $str    検査する文字列
/// @retval true    郵便番号である
/// @retval false   郵便番号ではない
/// @code
///         [使用例]
///         if (isZipCode(formatZipCode($zip))) {
///             郵便番号である;
///         }
///         else {
///             郵便番号でない;
///         }
/// @endcode
/// @author Yuichi Nakamura
//------------------------------------------------
function isZipCode($str) {
    // xxx-xxxx 形式
    if (preg_match("/^\d{3}\-\d{4}$/", $str)) {
        return true;
    }
    // xxx-xx 形式
    if (preg_match("/^\d{3}\-\d{2}$/", $str)) {
        return true;
    }
    // xxx 形式
    else if (preg_match("/^\d{3}$/", $str)) {
        return true;
    }
    return false;
}


//------------------------------------------------
/// @brief  文字列が郵便番号か検査する(ハイフン無しの形式を検査します)
/// @param  $str    検査する文字列
/// @retval true    郵便番号である
/// @retval false   郵便番号ではない
/// @code
///         [使用例]
///         if (isZipCodeWithoutHyphen(formatZipCode($zip))) {
///             郵便番号である;
///         }
///         else {
///             郵便番号でない;
///         }
/// @endcode
/// @author Yuichi Nakamura
//------------------------------------------------
function isZipCodeWithoutHyphen($str) {
    // xxx-xxxx 形式
    if (preg_match("/^\d{7}$/", $str)) {
        return true;
    }
    // xxx-xx 形式
    if (preg_match("/^\d{5}$/", $str)) {
        return true;
    }
    // xxx 形式
    else if (preg_match("/^\d{3}$/", $str)) {
        return true;
    }
    return false;
}


// //------------------------------------------------
// /// @brief  文字列が電話番号か検査する
// /// @param  $str    検査する文字列
// /// @retval true    有効
// /// @retval false   無効
// /// @code
// ///         [使用例]
// ///         if (isTelNumber(formatTelNumber($tel1, $tel2, $tel3))) {
// ///             電話番号である;
// ///         }
// ///         else {
// ///             電話番号でない;
// ///         }
// /// @endcode
// /// @author Yuichi Nakamura
// //------------------------------------------------
// function isTelNumber($str)
// {
//     // 固定電話 (0x-xxxx-xxxx / 10桁)
//     if (preg_match("/^0\d{1}\-\d{4}\-\d{4}$/", $str)) {
//         return true;
//     }
//     // 固定電話 (0xx-xxx-xxxx / 10桁)
//     else if (preg_match("/^0\d{2}\-\d{3}\-\d{4}$/", $str)) {
//         return true;
//     }
//     // 固定電話 (0xxx-xx-xxxx / 10桁)
//     else if (preg_match("/^0\d{3}\-\d{2}\-\d{4}$/", $str)) {
//         return true;
//     }
//     // 固定電話 (0xxxx-x-xxxx / 10桁)
//     else if (preg_match("/^0\d{4}\-\d{1}\-\d{4}$/", $str)) {
//         return true;
//     }
//     // 固定電話 (0xxxxx-xxxx / 9桁)
//     else if (preg_match("/^0\d{5}\-\d{4}$/", $str)) {
//         return true;
//     }
//     // 携帯電話 (0xx-xxxx-xxxx / 11桁)
//     else if (preg_match("/^0\d{2}\-\d{4}\-\d{4}$/", $str)) {
//         return true;
//     }
//     return false;
// }


//------------------------------------------------
/// @brief  文字列が電話番号か検査する
/// @param  $str    検査する文字列
/// @retval true    有効
/// @retval false   無効
/// @note   一応、国際電話用番号表記(06-6531-7888 -> +81-6-6531-7888)を考慮('+'だけ)。\n
///         検査は非常に甘い。\n
///         ITU-T(国際電気通信連合 電気通信標準化部門) E.164(公衆交換電話網などの電話網 の電話番号計画の勧告)
///         によれば、電話番号は国別コード混みで15桁以下の数字になる。\n
///         日本国内の最短電話番号は10桁(9桁地域は消滅したらしい)。
///         が、直通/ダイヤルインサービスを考慮すると訳分かんないので、適当に処理ｗ
/// @code
///         [使用例]
///         if (isTelNumber(formatTelNumber($tel1, $tel2, $tel3))) {
///             電話番号である;
///         }
///         else {
///             電話番号でない;
///         }
/// @endcode
/// @author Yuichi Nakamura
//------------------------------------------------
function isTelNumber($str)
{
    $str = preg_replace('( |-|〜)', '', $str);
//     if (preg_match('/^\+{0,1}[0-9]{9,20}$/', $str)) {       // 甘い判定 '/^\+{0,1}[0-9]{10,15}$/'がたぶん正しい形式
    if (preg_match('/^\+{0,1}[0-9]{10,15}$/', $str)) {       // '/^\+{0,1}[0-9]{10,15}$/'がたぶん正しい形式
        return true;
    }
    else {
        return false;
    }
}


//------------------------------------------------
/// @brief  文字列が電話番号か検査する(ハイフン対応)
/// @param  $str            検査する文字列
/// @param  $with_hyphen    ハイフンを考慮するか否か
///         - true          ハイフン区切りの電話番号として検査
///         - false         ハイフンを省略した数字の羅列として検査
/// @retval true    有効
/// @retval false   無効
/// @note   2007(平成19)年2月25日以降、国内の固定電話番号はすべて10桁に統一された。\n
///         携帯電話は11桁。
/// @code
///         [使用例]
///         if (isTel('03-1234-5678', true)) {
///             電話番号である;
///         }
///         else {
///             電話番号でない;
///         }
/// @endcode
/// @sa     http://www.soumu.go.jp/main_sosiki/joho_tsusin/top/tel_number/q_and_a-2001aug.html
/// @sa     http://www.soumu.go.jp/main_sosiki/joho_tsusin/top/tel_number/number_shitei.html
/// @sa     http://www.wdic.org/w/WDIC/6%E6%A1%81%E3%81%AE%E5%B8%82%E5%A4%96%E5%B1%80%E7%95%AA
/// @sa     http://www.wdic.org/w/WDIC/9%E6%A1%81%E3%81%AE%E9%9B%BB%E8%A9%B1%E7%95%AA%E5%8F%B7
/// @author Yuichi Nakamura
//------------------------------------------------
function isTel($str, $with_hyphen=false)
{
    if (false === $with_hyphen) {
        return preg_match('/^0\d{9,10}$/', $str) ? true : false;
    }
    else {
        return preg_match('/^0\d{1,4}-\d{1,4}-\d{4}$/ ', $str) ? true : false;
    }
}


//------------------------------------------------
/// @brief  半角カナを全角カナに変換する
/// @param  $str        変換する文字列
/// @param  $encoding   出力する文字エンコーディング(省略可)
/// @return 変換後の文字列
/// @author Yuichi Nakamura
//------------------------------------------------
function toZenKatakana($str, $encoding = EFP_SRC_ENCODING)
{
    return mb_convert_kana($str, 'KV', $encoding);
}


//------------------------------------------------
/// @brief  全角カナを半角カナに変換する
/// @param  $str        変換する文字列
/// @param  $encoding   出力する文字エンコーディング(省略可)
/// @return 変換後の文字列
/// @author Yuichi Nakamura
//------------------------------------------------
function toHanKatakana($str, $encoding = EFP_SRC_ENCODING)
{
    return mb_convert_kana($str, 'k', $encoding);
}


//------------------------------------------------
/// @brief  HTMLタグを無効化する
/// @param  $data       ソース文字列 あるいは 配列(ハッシュ化)
/// @param  $encoding   加工する文字列のエンコーディング形式(nullなら無指定)
/// @param  $rlevel     再帰レベルの制限
/// @return 変換したデータ
/// @note   出力用に特殊文字をHTMLエンティティに変換します。
/// @note   日本語では意味がないのでエンコーディング指定は廃止。
///         http://d.hatena.ne.jp/teracc/20070415
/// @author Yuichi Nakamura
//------------------------------------------------
function cleanTags($data, $encoding=null, $rlevel=5)
{
    if ($rlevel < 0) {
        return $data;
    }

    $encoding;
    if (!is_array($data)) {
        if (!is_string($data)) {
            return $data;
        }
        return htmlspecialchars($data, ENT_QUOTES);
        // else {
        //     if (is_null($encoding)) {
        //         return htmlspecialchars($data, ENT_QUOTES);
        //     }
        //     else {
        //         return htmlspecialchars($data, ENT_QUOTES, $encoding);
        //     }
        // }
    }
    else {
        $ary_tmp = array();
        foreach ($data as $key => $val) {
            $ary_tmp[$key] = cleanTags($val, $encoding, $rlevel-1);
        }
        return $ary_tmp;
    }
}


//------------------------------------------------
/// @brief  渡された文字列を指定した文字列で挟み込む
/// @param  $src    加工する文字列
/// @param  $head   前に付加する文字列
/// @param  $tail   後に付加する文字列
/// @params $force  文字列が空でも強制的に加工するかどうか
/// @return 加工された文字列
/// @note   結果は直接$src配列に反映されます。
/// @author Yuichi Nakamura
//------------------------------------------------
function sandwich($src, $head='', $tail='', $force=false)
{
    if ((false != $force) || (0 < strlen($src))) {
        $src = $head . $src . $tail;
    }
    return $src;
}


//------------------------------------------------
/// @brief  文字列を指定した文字列で挟み込む
/// @param  $src    ソース文字列 あるいは 配列
/// @param  $head   前に付加する文字列
/// @param  $tail   後に付加する文字列
/// @params $force  文字列が空でも強制的に加工するかどうか
/// @param  $rlevel 再帰レベルの制限
/// @return 変換したデータ
/// @note   元の配列を変更しません。
/// @author Yuichi Nakamura
//------------------------------------------------
function sandwichEach($src, $head='', $tail='', $force=false, $rlevel=5)
{
    if ($rlevel < 0) {
        return $src;
    }

    if (!is_array($src)) {
        if ((false != $force) || (0 < strlen($src))) {
            return $head . $src . $tail;
        }
        else {
            return $src;
        }
    }
    else {
        $ary_tmp = array();
        foreach ($src as $key => $val) {
            $ary_tmp[$key] = sandwichEach($val, $head, $tail, $force, $rlevel-1);
        }
        return $ary_tmp;
    }
}


//------------------------------------------------
/// @brief  JavaScript文字列用にエスケープする
/// @param  $src    ソース文字列
/// @return エスケープ処理後の文字列
/// @note   http://d.hatena.ne.jp/hoshikuzu/20071011#p1
/// @author Yuichi Nakamura
//------------------------------------------------
function jsEscape($src)
{
    $pairs = array('\\' => '\\\\',        // 「\」を「\\」に置換する
                   '"'  => '\\"',         // 「"」を「\"」に置換する
                   "'"  => "\\'",         // 「'」を「\'」に置換する
                   '/'  => '\\/',         // 「/」を「\/」に置換する
                   '<'  => '\\x3c',       // 「<」を「\x3c」に置換する
                   '>'  => '\\x3e',       // 「>」を「\x3e」に置換する
                   "\r" => '\\r',         // 「0x0D（CR)」を「\\r」に置換する
                   "\n" => '\\n',         // 「0x0A（LF）」を「\\n」に置換する
        );

    return strtr($src, $pairs);
}



//------------------------------------------------------------------------------
// end of file
//------------------------------------------------------------------------------
// ?>
