<?php // -*-mode:php; coding:euc-jp-unix-*-
//------------------------------------------------------------------------------
/// @file
/// @brief  ユーティリティ関数
/// @author Yuichi Nakamura
/// @date   Time-stamp: "2011-08-25 12:55:02"
//------------------------------------------------------------------------------
// require_once(EFP_CORE_DIR . DS . 'lib' . DS . 'lib_string.php');

//------------------------------------------------
/// @brief  array_key_exsistsのneedleを配列にしたもの
/// @param  $needles    存在するかどうか検査するキー配列
/// @param  $search     検査される配列
/// @retval true        $needlesで定義されるキーがすべて含まれる
/// @retal  false       $needlesで定義されるキーの中で含まれまいものが存在する
/// @author Yuichi Nakamura
//------------------------------------------------
function array_key_exsists_multi($needles, $search)
{
    foreach ($needles as $k) {
        if (!array_key_exists($k, $search)) {
            return false;
        }
    }
    return true;
}


//------------------------------------------------
/// @brief  配列から要素の値を取得する (デフォルト値付)
/// @param  $ary        値を取得する配列
/// @param  $key        取得する値のキー
/// @param  $default    値が見つからなかったときのデフォルト値 (省略したときはnull)
/// @author Yuichi Nakamura
//------------------------------------------------
function arrayGet($ary, $key, $default=null)
{
    if (true == isset($ary[$key])) {
        return $ary[$key];
    }
    else {
        return $default;
    }
}


//------------------------------------------------
/// @brief  配列から要素への参照を取得する (デフォルト値付)
/// @param  $ary        値を取得する配列
/// @param  $key        取得する値のキー
/// @param  $default    値が見つからなかったときのデフォルト値 (省略したときはnull)
/// @note   データへの参照を返します。<br>
///         キーが見つからないときは、デフォルト値を格納した要素を生成します。
/// @code   使用例
///         $ret =& getRef('key', 'default');
/// @endcode
/// @author Yuichi Nakamura
//------------------------------------------------
function & arrayGetRef(&$ary, $key, $default=null)
{
    if (false == isset($ary[$key])) {
        $ary[$key] = $default;
    }
    return $ary[$key];
}


//------------------------------------------------
/// @brief  配列要素を削除する
/// @param  $ary        要素を削除する配列への参照
/// @param  $keys       削除するキー もしくは キー配列
/// @param  $as_array   (数値キーの)配列として扱うかどうか(要素を詰める)
///         true        配列として扱う
///         false       ハッシュとして扱う(デフォルト)
/// @return なし
/// @author Yuichi Nakamura
//------------------------------------------------
function arrayRemoveKeys(&$ary, $keys, $as_array=false)
{
    if (!is_array($keys)) {
        $keys = array($keys);
    }
    foreach ($keys as $k) {
        if (array_key_exists($k, $ary)) {
            unset($ary[$k]);
        }
    }

    // 必要に応じてインデックスを振り直す
    if ($as_array) {
        $ary = array_merge($ary, array());      // 文字列キーは残る
//         $ary = array_values($ary);           // 文字列キーは削除されて数値キーに振り直される
    }
}


//------------------------------------------------
/// @brief  配列で指定したキーがすべて配列に存在するか検査する
/// @param  $keys   存在を検査するキーもしくはキー配列
/// @param  $target 検査する配列
/// @retval true    キーがすべて存在する
/// @retval false   存在しないキーがある
/// @author Yuichi Nakamura
///------------------------------------------------
function arrayKeysExists($keys, $target)
{
    if (!is_array($keys)) {
        $keys = array($keys);
    }

    foreach ($keys as $k) {
        if (!array_key_exists($k, $target)) {
            return false;
        }
    }
    return true;
}


//------------------------------------------------
/// @brief  ドットで連結された文字列からブラケットで囲われた配列表記に直す
/// @param  $src        ドットで連結されたデータ階層表現
/// @param  $root       最上位階層のデータ名 (デフォルトは'data')
/// @param  $quote      ブラケット内の識別子を囲む記号 (デフォルトは無し)
/// @param  $delimiter  ソース文字列を連結するデリミタの指定 (デフォルトは'.')
/// @return ブラケットで囲われた配列表記
/// @note   デフォルトの変換: 'top.second.third' -> 'data[top][second][third]'
/// @author Yuichi Nakamura
//------------------------------------------------
function dotToBracket($src, $root='data', $quote='', $delimiter='.')
{
    $replacement = $quote . '][' . $quote;
    return $root . '[' . $quote . str_replace($delimiter, $replacement, $src) . $quote . ']';
}


//------------------------------------------------
/// @brief  動的に配列階層を生成し、その要素への参照を返す
/// @param  $ref_ary    親配列への参照
/// @param  $hierarchy  コンテナの階層名を'.'で連結した文字列
/// @return コンテナ要素への参照
/// @note   staticメソッド。
/// @note   階層名に空文字列を渡された場合は、親配列そのものを返します。
/// @code
/// 使用例
///     $g_ary = array('a', 'b', 'c');
///     $elm =& getRefOfArrayElement($g_ary, 'level1.level2');
///     $elm = 10;
///
///     [実行後の配列の様子]
///     $g_ary = Array
///     (
///         [0] => a
///         [1] => b
///         [2] => c
///         [level1] => Array
///             (
///                 [level2] => 10
///             )
///
///     )
/// @endcode
/// @author Yuichi Nakamura
//------------------------------------------------
function & getRefOfArrayElement(&$ref_ary, $hierarchy = '')
{
    $hs = explode('.', $hierarchy);
    return getRefOfArrayElementByDynamicHierarchy($ref_ary, $hs);
}


//------------------------------------------------
/// @brief  動的に配列階層を生成し、その要素への参照を返す
/// @param  $ref_ary    親配列への参照
/// @param  $hierarchy  コンテナの階層名を列挙した配列、
///                     もしくはトップレベルコンテナ名(1階層だけのとき)
/// @return コンテナ要素への参照
/// @note   staticメソッド。
/// @note   階層名を列挙した配列に空の配列を渡された場合は、親配列そのものへの参照を返します。
/// @code
/// 使用例
///     $g_ary = array('a', 'b', 'c');
///     $hcy = array('level1', 'level2');
///
///     $elm =& getRefOfArrayElementByDynamicHierarchy($g_ary, $hcy);
///     $elm = 10;
///
///     [実行後の配列の様子]
///     $g_ary = Array
///     (
///         [0] => a
///         [1] => b
///         [2] => c
///         [level1] => Array
///             (
///                 [level2] => 10
///             )
///
///     )
/// @endcode
/// @author Yuichi Nakamura
//------------------------------------------------
function & getRefOfArrayElementByDynamicHierarchy(&$ref_ary, $hierarchy = array())
{
    if (!is_array($hierarchy) && !is_null($hierarchy)) {
        $hierarchy = array($hierarchy);
    }
    else if (is_null($hierarchy)) {
        $hierarchy = array();
    }

    $ref_elm =& $ref_ary;
    for ($i = 0; $i < count($hierarchy); $i++) {    // 配列内の並び順番を保証したいので for 文
        if ('' != $hierarchy[$i]) {
            $ref_elm =& $ref_elm[$hierarchy[$i]];
        }
    }

    return $ref_elm;
}


//------------------------------------------------
/// @brief  配列を他の配列で再帰的に上書きする
/// @param  $ary1       元になる配列
/// @param  $ary2       上書きする配列
/// @return $ary1に$ary2を上書きした配列
/// @note   array_merge_recursiveだと同じ数値添え字の要素は追記されてしまい、
///         関数等のデフォルト値をオプションで上書きするときに不都合なため、新しく作った。
/// @author Yuichi Nakamura
//------------------------------------------------
function arrayOverwriteRecursive($ary1, $ary2)
{
    foreach ($ary1 as $k => $v) {

        if (array_key_exists($k, $ary2)) {
            if (is_array($v)) {
                $ary1[$k] = arrayOverwriteRecursive($ary1[$k], $ary2[$k]);
            }
            else {
                $ary1[$k] = $ary2[$k];
            }
        }
    }
    return $ary1;
}


//------------------------------------------------
/// @brief  ユニークIDを生成する
/// @param  なし
/// @return ユニークID
/// @note   http://jp.php.net/microtime
/// @note   18文字
/// @author Yuichi Nakamura
//------------------------------------------------
function seqid()
{
    list($usec, $sec) = explode(" ", microtime());
    list($int, $dec)  = explode(".", $usec);
    return $sec . $dec;
}


//------------------------------------------------
/// @brief  ディレクトリの容量を調べる
/// @param  $dir        ディレクトリ名
/// @return ディレクトリの容量
/// @note   http://weiy1005.exblog.jp/1809865 より。
/// @author Yuichi Nakamura
//------------------------------------------------
function getDirSize($dir)
{
    $handle = opendir($dir);

    $mas = '';
    while ($file = readdir($handle)) {
        if ($file != '..' && $file != '.' && !is_dir($dir.'/'.$file)) {
            $mas += filesize($dir.'/'.$file);
        }
        else if (is_dir($dir.'/'.$file) && $file != '..' && $file != '.') {
            $mas += GetDirSize($dir.'/'.$file);
        }
    }
    return $mas;
}


//------------------------------------------------
/// @brief  パスワード文字列を生成する
/// @param  なし
/// @return パスワード文字列(32文字)
/// @author Yuichi Nakamura
//------------------------------------------------
function genPassword()
{
    list($usec, $sec) = explode(' ', microtime());
    mt_srand((float) $sec + ((float) $usec * 100000));
    $rand = mt_rand();

    $remort_addr = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 0;
    $remort_port = isset($_SERVER['REMOTE_PORT']) ? $_SERVER['REMOTE_PORT'] : 0;

    return md5($remort_addr . microtime() . $remort_port . $rand);
}


//------------------------------------------------
/// @brief  ランダムな文字列を生成する
/// @param  $length     文字列長
/// @param  $chars      使用する文字種のリスト(列挙した文字列)
///         - 'a'   アルファベット小文字
///         - 'A'   アルファベット大文字
///         - '0'   数字
/// @return ランダムな文字列
/// @note   パスワードメール送信処理などに使います。
///         「英文字始まりで英数字が続く・・・」などの条件を付ける場合は、
///          この関数を組み合わせるなどして対応してください。
/// @note   http://jp2.php.net/function.mt_rand
/// @author Yuichi Nakamura
//------------------------------------------------
function genRndString($length=8, $chars='aA0')
{
    $pool = array();
    if (false !== strpos($chars, 'a')) {
        $pool = array_merge(range('a', 'z'), $pool);
    }
    if (false !== strpos($chars, 'A')) {
        $pool = array_merge(range('A', 'Z'), $pool);
    }
    if (false !== strpos($chars, '0')) {
        $pool = array_merge(range('0', '9'), $pool);
    }

    $str = '';
    for ($i = 0; $i < $length; $i++) {
       $str .= $pool[mt_rand(0, count($pool) - 1)];
    }
    return $str;
}


//------------------------------------------------
/// @brief  改行区切りデータを配列に分解する
/// @param  $str    改行区切りのデータ文字列
/// @return 要素ごとに分解された配列
/// @note   "CRLF", "CR", "LF" すべてに対応。
/// @author Yuichi Nakamura
//------------------------------------------------
function splitNLSV($str)
{
    $str = mb_ereg_replace("\r\n", "\n", $str);
    $str = mb_ereg_replace("\r", "\n", $str);

    return explode("\n", $str);
}


//------------------------------------------------
/// @brief  文字列を指定文字数で丸める
/// @param  $str        丸めたい文字列
/// @param  $start      開始位置のオフセット
/// @param  $width      丸める文字数
/// @param  $trimmarker 丸めた後に追加される文字列
/// @param  $encoding   文字エンコーディング(省略可)
/// @return 丸められた文字列
/// @note   $widthは$trimmarker込みで計算されます。
/// @note   mb_strimwidthだと文字数ではなく、文字幅で丸められてしまう。
/// @author Yuichi Nakamura
//------------------------------------------------
function strmlen($str, $start, $width, $trimmarker, $encoding=null)
{
    if (is_null($encoding)) {
        $encoding = mb_internal_encoding();
    }

    $len_trimmarker = mb_strlen($trimmarker);
    $len = $width - ($start + $len_trimmarker);

    $str2 = mb_substr($str, $start, $len, $encoding);
    if (mb_strlen($str2) < mb_strlen($str)) {
        $str2 .= $trimmarker;
    }
    return $str2;
}


//------------------------------------------------
/// @brief  一定の確率でtrueとなる可能性を検査する
/// @param  $gc_probability     確率計算用分子
/// @param  $gc_divisor         確率計算用分母
/// @retval true                真
/// @retval false               偽
/// @note   引数で与えられた確率でtrueを返します。
/// @author Yuichi Nakamura
//------------------------------------------------
function testRandomChance($gc_probability, $gc_divisor)
{
    // 分子の方が大きければ確率は 1
    if ($gc_divisor < $gc_probability) {
        $gc_probability = $gc_divisor;
    }

    $max  = 10000;           // 1 / 10000 の精度を持つ
    $prob = round($max * $gc_probability / $gc_divisor);        // $prob = 0 ~ $max までの整数
    $rnd  = mt_rand(0, $max);                                   // $rnd = 0 ~ $max までの整数

    return ($rnd < $prob) ? true : false;
}


//------------------------------------------------
/// @brief  作業ファイルを削除する
/// @param  $gc_probability     実行確率計算用(分子)
/// @param  $gc_divisor         実行確率計算用(分母)
/// @param  $gc_max_lifetime    作業ファイルのライフタイム(秒)
///                             - この期間を経過したファイルが削除対象となります。
/// @param  $work_dir           作業ファイル格納ディレクトリ
/// @param  $file_pattern       globに与えるファイルパターン(デフォルト'*')
/// @return なし
/// @note   起動確率は ($gc_probability / $gc_divisor * 100) % となります。
/// @author Yuichi Nakamura
//------------------------------------------------
function cleanWorkFile($gc_probability, $gc_divisor, $gc_max_lifetime, $work_dir, $file_pattern='*')
{
    if (false == testRandomChance($gc_probability, $gc_divisor)) {
        return;
    }

    if (false == ($ary = glob($work_dir . DS . $file_pattern, GLOB_NOSORT))) {
        return false;
    }
    foreach ($ary as $filename) {
        $idle = time() - filemtime($filename);
        if ($gc_max_lifetime < $idle) {
            @unlink($filename);
        }
    }
}


//------------------------------------------------
/// @brief  アクションへのリンクを生成する
/// @param  $rewrite_action mod_rewriteでのアクション名書き換えに対応するか
///                         true : 対応する
///                         false: 対応しない
/// @param  $params         パラメータ配列
/// @return なし
/// @code   [使用例]
///     genLink(array('f'=>'home'));
/// @endcode
/// @author Yuichi Nakamura
//------------------------------------------------
function genLink($rewrite_action, $params=array())
{
    $ary_work = array();
    $action = '';           // mod_rewrite用アクション名
    foreach ($params as $key => $val) {
        if ($rewrite_action) {
            if (EFP_ACTION_KEY == $key) {
                $action = "/$val";
                continue;
            }
        }
        $ary_work[] = "{$key}={$val}";
    }

    $str_param = '';
    if (0 < count($ary_work)) {
        $str_param = '?' . implode('&', $ary_work);
    }

    $host = $_SERVER['HTTP_HOST'];
    $uri  = $_SERVER['SCRIPT_NAME'];
    if ($rewrite_action) {
        $uri = dirname($uri);
    }
    $uri = rtrim($uri, '/');        // $_SERVER['SCRIPT_NAME']が'/'のとき、'/'が2重になってしまうのを防止

    return "http://{$host}{$uri}{$action}{$str_param}";
}


//------------------------------------------------
/// @brief  Locationヘッダを使ってリダイレクトする
/// @param  $rewrite_action mod_rewriteでのアクション名書き換えに対応するか
///                         true : 対応する
///                         false: 対応しない
/// @param  $params         パラメータ配列
/// @param  $is_https       HTTPS接続でリダイレクトするかどうか
///                         true : HTTPSでリダイレクトする
///                         false: HTTPでリダイレクトする
///                         null:  自動判別
/// @return なし
/// @code   [使用例]
///     redirect(array('f'=>'home'));
/// @endcode
/// @author Yuichi Nakamura
//------------------------------------------------
function redirect($rewrite_action, $params=array(), $is_https=null)
{
    $ary_work = array();
    $action = '';           // mod_rewrite用アクション名
    foreach ($params as $key => $val) {
        if ($rewrite_action) {
            if (EFP_ACTION_KEY == $key) {
                $action = "/$val";
                continue;
            }
        }
        $ary_work[] = "{$key}={$val}";
    }

    $str_param = '';
    if (0 < count($ary_work)) {
        $str_param = '?' . implode('&', $ary_work);
    }

    $host = $_SERVER['HTTP_HOST'];
    $uri  = $_SERVER['SCRIPT_NAME'];
    if ($rewrite_action) {
        $uri = dirname($uri);
    }
    $uri = rtrim($uri, '/');        // $_SERVER['SCRIPT_NAME']が'/'のとき、'/'が2重になってしまうのを防止

    if (is_null($is_https)) {
        $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    }
    else {
        $protocol = $is_https ? 'https' : 'http';
    }
    header("Location: {$protocol}://{$host}{$uri}{$action}{$str_param}");
    exit(1);
}


//------------------------------------------------
/// @brief パスから絶対URLを作成
/// @param  $path                   パス
/// @param  $default_port           デフォルトのポート（そのポートである場合にはURLに含めない）
/// @param  $repce_hostname_to_ip   ホスト名をIPアドレスに置き換えるかどうか指定する
///         - true  : 置き換える
///         - false : 置き換えない(デフォルト)
/// @return URL
/// @note   http://programming-magic.com/?id=118
/// @note   ホスト名が'localhost'になってしまい、外部のクライアントからアクセスするときに支障が出る場合は
///         $repce_hostname_to_ip に true を指定し、IPアドレスに変換を行わせると問題が解決する場合があります。
/// @author Yuichi Nakamura
//------------------------------------------------
function path_to_url($path, $default_port=80, $repce_hostname_to_ip=false)
{
    //ドキュメントルートのパスとURLの作成
    $document_root_url = $_SERVER['SCRIPT_NAME'];
    $document_root_path = $_SERVER['SCRIPT_FILENAME'];
    while (basename($document_root_url) === basename($document_root_path)) {
        $document_root_url = dirname($document_root_url);
        $document_root_path = dirname($document_root_path);
    }

    $document_root_path = strtr($document_root_path, '\\', '/');        // win対策 2010-02-23 18:55:20
    $document_root_url = strtr($document_root_url, '\\', '/');
    if ($document_root_path === '/')  {
        $document_root_path = '';
    }
    if ($document_root_url === '/') {
        $document_root_url = '';
    }

    // ワーニングが出ないように isset($_SERVER['HTTPS'])とした。
    // yuichi nakamura 2009-04-15 21:28:32
    $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $port = ($_SERVER['SERVER_PORT'] && $_SERVER['SERVER_PORT'] != $default_port) ? ':' . $_SERVER['SERVER_PORT'] : '';
    $document_root_url = $protocol . '://' . $_SERVER['SERVER_NAME'] . $port . $document_root_url;

    //絶対パスの取得 (realpath関数ではファイルが存在しない場合や、シンボリックリンクである場合にうまくいかない)
    $absolute_path = realpath($path);
    $absolute_path = strtr($absolute_path, '\\', '/');                  // win対策 2010-02-23 18:55:59

    if (!$absolute_path) {
        return false;
    }
    if (substr($absolute_path, -1) !== '/' && substr($path, -1) === '/') {
        $absolute_path .= '/';
    }

    //パスを置換して返す
    $url = str_replace($document_root_path, $document_root_url, $absolute_path);
    if ($absolute_path === $url) {
        return false;
    }

    // ホスト名をIPアドレスに置換する
    if ($repce_hostname_to_ip) {
        $host = preg_quote($_SERVER['SERVER_NAME']);    // '/'以外の正規表現記号をエスケープする
        $ip   = $_SERVER['SERVER_ADDR'];
        $url = preg_replace("/{$host}/", $ip, $url, 1);
    }

    return $url;
}


//------------------------------------------------
/// @brief      相対リンク文字列を作成する
/// @param      $file   リンクするファイルパス(ブラウザから呼び出されたスクリプトのディレクトリを基点とした相対パス指定)
/// @return     リンク先のURL
/// @note       ファイルパスは相対パス指定のみです。
/// @note       呼び出しプロトコル(http, https)は自動で判別されます。
/// @code
///         // [呼び出し例]
///         $lnk = makeLink('..' . DS . '.taiken' . DS . 'login.php');
///         echo '<a href="' . $lnk . '">login.php</a>';
/// @endcode
/// @author     Yuichi Nakamura
//------------------------------------------------
function makeLink($file)
{
    $filename = $_SERVER['SCRIPT_FILENAME'];
    if (!file_exists($filename)) {
        $filename = $_SERVER['PATH_TRANSLATED'];
    }
    $dirname = dirname(realpath($filename));
    return path_to_url($dirname . DS . $file);
}


// //------------------------------------------------
// /// @brief      相対リンク文字列を作成する
// /// @param      $protocol    接続プロトコル(http or https)
// /// @param       $file_name  リンクするファイル名
// /// @return     なし
// /// @note        ファイル名にパスを含めることはできません。
// /// @author     Yuichi Nakamura
// //------------------------------------------------
// function makeLink($protocol, $file_name)
// {
//     $server = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : $_SERVER['SERVER_ADDR'];
//     return "{$protocol}://{$server}" . dirname($_SERVER['SCRIPT_NAME']) . "/$file_name";
// }


//------------------------------------------------
/// @brief  パスワード形式に応じて文字列を変換する
/// @param  $pass_string    変換するパスワード文字列
///         - MD5
///         - PLAINTEXT
/// @param  $pass_type      パスワードタイプ
/// @author Yuichi Nakamura
//------------------------------------------------
function convPassString($pass_string, $pass_type=null)
{
    if (is_null($pass_type)) {
        $pass_type = EFP_PASS_TYPE;
    }
    switch ($pass_type) {
    case 'MD5':
        return md5($pass_string);

    case 'PLAINTEXT':
    default:
        return $pass_string;
    }
}


//------------------------------------------------
/// @brief  スクリプトソースコードと同じ文字コードに変換
/// @param  $str        ソース文字列(あるいは文字列配列)
/// @param  $enc_from   変換元のエンコーディング形式
/// @return 変換後の文字列(あるいは文字列配列)
/// @author Yuichi Nakamura
//------------------------------------------------
function toScriptEncoding($str, $enc_from=null)
{
    if (strtolower(EFP_SRC_ENCODING) == strtolower($enc_from)) {
        return $str;
    }

    switch (strtolower(EFP_SRC_ENCODING)) {
    case 'eucjp-win':
        return toEUCJP_WIN($str, $enc_from);

    case 'euc-jp':
        return toEUC($str, $enc_from);

    case 'sjis':
    case 'sjis-win':
        return toSJIS($str, $enc_from);

    case 'jis':
        return toJIS($str, $enc_from);

    case 'utf-8':
        return toUTF8($str, $enc_from);

    default:
        return $str;
    }
}


//------------------------------------------------
/// @brief  DB格納時用の文字コード変換
/// @param  $str        ソース文字列(あるいは文字列配列)
/// @param  $enc_from   変換元のエンコーディング形式
/// @return 変換後の文字列(あるいは文字列配列)
/// @author Yuichi Nakamura
//------------------------------------------------
function toDBEncoding($str, $enc_from=null)
{
    if (strtolower(EFP_DB_ENCODING) == strtolower($enc_from)) {
        return $str;
    }

    switch (strtolower(EFP_DB_ENCODING)) {
    case 'eucjp-win':
        return toEUCJP_WIN($str, $enc_from);

    case 'euc-jp':
        return toEUC($str, $enc_from);

    case 'sjis':
    case 'sjis-win':
        return toSJIS($str, $enc_from);

    case 'jis':
        return toJIS($str, $enc_from);

    case 'utf-8':
        return toUTF8($str, $enc_from);

    default:
        return $str;
    }
}


//------------------------------------------------
/// @brief  HTML出力用の文字コード変換
/// @param  $str        ソース文字列(あるいは文字列配列)
/// @param  $enc_from   変換元のエンコーディング形式
/// @return 変換後の文字列(あるいは文字列配列)
/// @author Yuichi Nakamura
//------------------------------------------------
function toOutputEncoding($str, $enc_from=EFP_SRC_ENCODING)
{
    if (strtolower(EFP_TARGET_ENCODING) == strtolower($enc_from)) {
        return $str;
    }

    switch (strtolower(EFP_TARGET_ENCODING)) {
    case 'eucjp-win':
        return toEUCJP_WIN($str, $enc_from);

    case 'euc-jp':
        return toEUC($str, $enc_from);

    case 'sjis':
    case 'sjis-win':
        return toSJIS($str, $enc_from);

    case 'jis':
        return toJIS($str, $enc_from);

    case 'utf-8':
        return toUTF8($str, $enc_from);

    default:
        return $str;
    }
}


//------------------------------------------------
/// @brief  数値をKB/MB/GB表示に変換する
/// @param  $n      数値
/// @param  $p      小数点以下の有効桁数
/// @return 変換後の文字列(浮動小数点表記)と単位の配列
///         - array(数値, 単位('', 'KB', 'MB', 'GB'))
/// @author Yuichi Nakamura
//------------------------------------------------
function num2HFormat($n, $p=3)
{
    $KB = 1024;
    $MB = 1048576;
    $GB = 1073741824;

    $unit = '';
    if ($GB < $n) {
        $unit = 'G';
        $n = round($n / $GB, $p);
    }
    else if ($MB < $n) {
        $unit = 'M';
        $n = round($n / $MB, $p);
    }
    else if ($KB < $n) {
        $unit = 'K';
        $n = round($n / $KB, $p);
    }
    else {
        return array((int)$n, '');
    }
    return array(sprintf("%.{$p}f", $n), $unit);
}


//------------------------------------------------
/// @brief  文字化け対処版CSVデータ読み込み関数
/// @param  $handle ファイルハンドル
/// @param  $length 行の最大長
/// @param  $d      フィールドのデリミタ
/// @param  $e      フィールドの囲いこみ文字
/// @retval !null   読み込んだフィールドの内容を含む数値添字配列
/// @retval null    エラー、またはファイルの終端
/// @sa     http://yossy.iimp.jp/wp/?p=56
//------------------------------------------------
function fgetcsv_reg(&$handle, $length=null, $d=',', $e='"')
{
    $d = preg_quote($d);
    $e = preg_quote($e);
    $_line = '';
    $eof = false;
    while ($eof != true) {
        $_line .= (empty($length) ? fgets($handle) : fgets($handle, $length));
        $itemcnt = preg_match_all('/' . $e . '/', $_line, $dummy);
        if ($itemcnt % 2 == 0) {
            $eof = true;
        }
    }
    $_csv_line = preg_replace('/(?:\r\n|[\r\n])?$/', $d, trim($_line));
    $_csv_pattern = '/(' . $e . '[^' . $e . ']*(?:' . $e . $e . '[^' . $e . ']*)*' . $e . '|[^' . $d . ']*)' . $d . '/';
    preg_match_all($_csv_pattern, $_csv_line, $_csv_matches);
    $_csv_data = $_csv_matches[1];
    for ($_csv_i = 0; $_csv_i < count($_csv_data); $_csv_i++) {
        $_csv_data[$_csv_i] = preg_replace('/^' . $e . '(.*)' . $e . '$/s', '$1', $_csv_data[$_csv_i]);
        $_csv_data[$_csv_i] = str_replace($e . $e, $e, $_csv_data[$_csv_i]);
    }
    return empty($_line) ? false : $_csv_data;
}


//------------------------------------------------
/// @brief  CSVデータ読み込み関数
/// @param  $handle ファイルハンドル
/// @param  $length 行の最大長
/// @param  $d      フィールドのデリミタ
/// @param  $e      フィールドの囲いこみ文字
/// @retval !null   読み込んだフィールドの内容を含む数値添字配列
/// @retval null    エラー、またはファイルの終端
/// @note   PHP5以降の環境だと、fgetcsvで文字化けが発生するケースがあるため、
///         PHPのバージョンを見て呼び出す関数を選択する。
/// @author Yuichi Nakamura
//------------------------------------------------
function fgetcsvWrapper(&$handle, $length=null, $d=',', $e='"')
{
    if (PHP_4) {
        if (is_null($length)) {
            return fgetcsv($handle);
        }
        else {
            return fgetcsv($handle, $length, $d, $e);
        }
    }
    else {
        return fgetcsv_reg($handle, $length, $d, $e);
    }
}


//------------------------------------------------
/// @brief  PHP4用CSVデータ(文字列)読み込み関数
/// @param  $str    文字列
/// @param  $d      フィールドのデリミタ
/// @param  $e      フィールドの囲いこみ文字
/// @return 読み込んだフィールドの内容を含む数値添字配列
/// @sa     http://yossy.iimp.jp/wp/?p=56
//------------------------------------------------
function str_getcsv_reg($str, $d=',', $e='"')
{
    $d = preg_quote($d);
    $e = preg_quote($e);

    $_csv_line = preg_replace('/(?:\r\n|[\r\n])?$/', $d, trim($str));
    $_csv_pattern = '/(' . $e . '[^' . $e . ']*(?:' . $e . $e . '[^' . $e . ']*)*' . $e . '|[^' . $d . ']*)' . $d . '/';
    preg_match_all($_csv_pattern, $_csv_line, $_csv_matches);
    $_csv_data = $_csv_matches[1];
    for ($_csv_i = 0; $_csv_i < count($_csv_data); $_csv_i++) {
        $_csv_data[$_csv_i] = trim($_csv_data[$_csv_i]);    // ','の後に付けがちな空白を取る
        $_csv_data[$_csv_i] = preg_replace('/^' . $e . '(.*)' . $e . '$/s', '$1', $_csv_data[$_csv_i]);
        $_csv_data[$_csv_i] = str_replace($e . $e, $e, $_csv_data[$_csv_i]);
    }
    return empty($str) ? array() : $_csv_data;
}


//------------------------------------------------
/// @brief  CSVデータ(文字列)読み込み関数
/// @param  $str    文字列
/// @param  $d      フィールドのデリミタ
/// @param  $e      フィールドの囲いこみ文字
/// @return 読み込んだフィールドの内容を含む数値添字配列
/// @sa     http://yossy.iimp.jp/wp/?p=56
/// @note   CSV1行分のデータしか処理しません。
/// @code
///         // 複数行を処理する例
///         $data = mb_ereg_replace("\\r\\n", "\\n", $data);
///         $data = mb_ereg_replace("\\r", "\\n", $data);
///         $lines = explode("\n", $data);
///         $nlines = count($lines);
///         for ($i = 0; $i < $nlines; $i++) {
///             $elm = str_getcsvWrapper($lines[$i]);
///             $this->m_exams_data[$i] = array('id'             => $i,
///                                             'kouza_md_code'  => $elm[0],
///                                             'course_md_code' => $elm[1],
///                                             'label'          => $elm[2]);
///         }
/// @endcode
//------------------------------------------------
function str_getcsvWrapper($str, $d=',', $e='"')
{
    if (function_exists('str_getcsv')) {
        return str_getcsv($str, $d, $e);
    }
    else {
        return str_getcsv_reg($str, $d, $e);
    }
}


//------------------------------------------------
/// @brief  配列からCSVレコードを1つ作成する
/// @param  $fields     フィールドを格納した配列
/// @param  $with_rs    レコードセパレータを追加するかどうか
///         - true      追加する
///         - false     追加しない(デフォルト)
/// @return CSVレコード
/// @note   RFCで定義されているレコードセパレータはCRLF。
/// @sa
///         - RFC4180 (http://www.rfc-editor.org/rfc/rfc4180.txt)
///         - 邦訳 (http://www.kasai.fm/wiki/rfc4180jp)
/// @author Yuichi Nakamura
//------------------------------------------------
function makeCsvRecord($fields, $with_rs=false)
{
    $nf = count($fields);
    for ($i = 0; $i < $nf; $i++) {
        $fields[$i] = '"' . str_replace('"', '""', $fields[$i]) . '"';
    }
    return implode(',', $fields) . ($with_rs ? "\r\n" : '');
}


//------------------------------------------------
/// @brief  IDがインデックスとなるように配列を変換する
/// @param  $ary        変換する配列 ('id'項目が必要)
/// @param  $id_name    id名 (省略時は'id')
/// @return 変換された配列
/// @code
///     // この関数を通すことで、$before配列が$after配列のようになります。
///     $before = array(0 => array ('id' => '1',
///                                 'ex_job_code' => '19',
///                                 'ex_job_name' => '会社員'),
///                     1 => array ('id' => '2',
///                                 'ex_job_code' => '50',
///                                 'ex_job_name' => '学生'),
///                     2 => array ('id' => '3',
///                                 'ex_job_code' => '90',
///                                 'ex_job_name' => 'その他'));
///
///     $after = toIdIndexedArray($before, 'id');
///
///     $after =  array(1 => array ('id' => '1',
///                                 'ex_job_code' => '19',
///                                 'ex_job_name' => '会社員'),
///                     2 => array ('id' => '2',
///                                 'ex_job_code' => '50',
///                                 'ex_job_name' => '学生'),
///                     3 => array ('id' => '3',
///                                 'ex_job_code' => '90',
///                                 'ex_job_name' => 'その他'));
/// @endcode
/// @author Yuichi Nakamura
//------------------------------------------------
function toIdIndexedArray($ary, $id_name='id')
{
    $ary_tmp = array();
    foreach($ary as $elm)
    {
        if (isset($elm[$id_name])) {
            $ary_tmp[$elm[$id_name]] = $elm;
        }
    }
    return $ary_tmp;
}


//------------------------------------------------
/// @brief  キャッシュ用データ文字列を生成する
/// @param  $var_name   割り当てる変数名文字列
/// @param  $data       ソースデータ
/// @return データ文字列
/// @author Yuichi Nakamura
//------------------------------------------------
function genCacheDataString($var_name, $data)
{
    return $var_name . ' = ' . var_export($data, true) . ";\n";
}


//------------------------------------------------
/// @brief  構造体(配列)配列をタグ名ごとに別の配列に分割する
/// @param  $ary        分割する配列
/// @param  $tag_name   分割のグループ分けに使われるタグ名
/// @return $tag_nameごとにまとめられた配列の配列
/// @note   配列キーは保存されます。
//------------------------------------------------
function divideArrayByTagName($ary, $tag_name)
{
    $ary_tmp = array();
    $current_tag = null;
    foreach ($ary as $key => $elm) {
        // ホワイトスペースの連続のような値は検査しない
        if (!isset($elm[$tag_name]) || !is_scalar($elm[$tag_name])) {
            continue;
        }
        if ($elm[$tag_name] !== $current_tag) {
            $current_tag = $elm[$tag_name];
        }
        $ary_tmp[$current_tag][$key] = $elm;
    }
    return $ary_tmp;
}


//------------------------------------------------
/// @brief  datetime型(データベース)の文字列を生成する
/// @param  $unix_time  UNIXタイムスタンプ
/// @return datetime型の文字列表現
/// @note   引数が省略されたときは現在時刻を使います。
/// @author Yuichi Nakamura
//------------------------------------------------
function makeDateTime($unix_time=null)
{
    if (is_null($unix_time)) {
        $unix_time = time();
    }
    return date("Y-m-d H:i:s", $unix_time);
}



//------------------------------------------------------------------------------
// end of file
//------------------------------------------------------------------------------
// ?>
