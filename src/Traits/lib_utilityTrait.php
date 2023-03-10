<?php

namespace App\Traits;

trait lib_utilityTrait
{
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
    function path_to_url($path, $default_port = 80, $repce_hostname_to_ip = false)
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
        if ($document_root_path === '/') {
            $document_root_path = '';
        }
        if ($document_root_url === '/') {
            $document_root_url = '';
        }

        // ワーニングが出ないように isset($_SERVER['HTTPS'])とした。
        // yuichi nakamura 2009-04-15 21:28:32
        $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $port = ($_SERVER['SERVER_PORT'] && $_SERVER['SERVER_PORT'] != $default_port) ? ':' . $_SERVER['SERVER_PORT'] : '';
        $document_root_url = $protocol . '://'. $_SERVER['HTTP_HOST'];//$protocol . '://' . $_SERVER['SERVER_NAME'] . $port . $document_root_url;

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

    function bool2str($value) {
        return $value ? 'true' : 'false';
    }
}
