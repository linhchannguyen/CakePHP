<?php
    //------------------------------------------------
    // プレビューと公開ページで異なる値 (プレビューのエントリスクリプト内で上書きする値)
    //------------------------------------------------
    /// @enum   IS_PREVIEW    プレビュー状態識別フラグ
    if (!defined('IS_PREVIEW')) {
        define('IS_PREVIEW', false);
    }

    /// @enum   EVENT_INFO_TERM   公開ページ(校舎)でのイベント情報表示期間
    if (!defined('EVENT_INFO_TERM')) {
        define('EVENT_INFO_TERM', 7);
    }

    /// @enum   NEWS_USE_ENABLED_FROM     公開ページ(校舎)で有効期間カラム(開始)をWhat's Newデータ抽出時に考慮するかどうか
    if (!defined('NEWS_USE_ENABLED_FROM')) {
        define('NEWS_USE_ENABLED_FROM', true);
    }

    /// @enum   NEWS_USE_ENABLED_TO       公開ページ(校舎)で有効期間カラム(終了)をWhat's Newデータ抽出時に考慮するかどうか
    if (!defined('NEWS_USE_ENABLED_TO')) {
        define('NEWS_USE_ENABLED_TO', true);
    }

    /// @enum   RECOMMEND_USE_ENABLED_FROM    公開ページ(校舎)で有効期間カラム(開始)をおすすめ講座データ抽出時に考慮するかどうか
    if (!defined('RECOMMEND_USE_ENABLED_FROM')) {
        define('RECOMMEND_USE_ENABLED_FROM', true);
    }

    /// @enum   RECOMMEND_USE_ENABLED_TO      公開ページ(校舎)で有効期間カラム(終了)をおすすめ講座データ抽出時に考慮するかどうか
    if (!defined('RECOMMEND_USE_ENABLED_TO')) {
        define('RECOMMEND_USE_ENABLED_TO', true);
    }

    /// @enum   EVENT_INFO_MONTH_OFFSET 公開ページ(イベント情報)でのイベント取得期間オフセット (今月に加算される月数)
    /// @note   0 だと今月から取得開始
    if (!defined('EVENT_INFO_MONTH_OFFSET')) {
        define('EVENT_INFO_MONTH_OFFSET', 0);
    }

    /// @enum   EVENT_INFO_MONTH_TERM   公開ページ(イベント情報)でのイベント取得期間 (月数)
    /// @note   1 だと来月まで取得
    if (!defined('EVENT_INFO_MONTH_TERM')) {
        define('EVENT_INFO_MONTH_TERM', 1);
    }

    /// @enum   EVENT_INFO_MONTH_DAY_OF_PERIOD  公開ページ(イベント情報)でのイベント取得終了日
    /// @note   15 だと 今月+EVENT_INFO_MONTH_OFFSET 〜 今月+EVENT_INFO_MONTH_OFFSET+EVENT_INFO_MONTH_TERM月の15日まで取得
    if (!defined('EVENT_INFO_MONTH_DAY_OF_PERIOD')) {
        define('EVENT_INFO_MONTH_DAY_OF_PERIOD', 15);
    }
    //------------------------------------------------

    define('MAX_URL_LEN', 512);                     /// @enum MAX_URL_LEN                   フォームから入力されるURL文字列最大長
    define('MAX_ORDER_LEN', 20);                    /// @enum MAX_ORDER_LEN                 フォームから入力される並び補正値文字列最大長

    define('NEWS_MAX_TITLE_LEN', 512);              /// @enum NEWS_MAX_TITLE_LEN            What's Newタイトル文字列最大長
    define('NEWS_MAX_TITLE_SUB_LEN', 512);          /// @enum NEWS_MAX_TITLE_SUB_LEN        What's Newサブタイトル文字列最大長
    define('NEWS_MAX_URL_LEN', MAX_URL_LEN);        /// @enum NEWS_MAX_URL_LEN              What's NewURLリンク文字列最大長
    define('NEWS_MAX_PDF_LEN', MAX_URL_LEN);        /// @enum NEWS_MAX_PDF_LEN              What's NewPDFリンク文字列最大長

    define('RECOMMENDS_MAX_TITLE_LEN', 512);        /// @enum RECOMMENDS_MAX_TITLE_LEN      おすすめ講座タイトル文字列最大長
    define('RECOMMENDS_MAX_TITLE_SUB_LEN', 1024);    /// @enum RECOMMENDS_MAX_TITLE_SUB_LEN  おすすめ講座サブタイトル文字列最大長
    define('RECOMMENDS_MAX_URL_LEN', MAX_URL_LEN);  /// @enum RECOMMENDS_MAX_URL_LEN        おすすめ講座URLリンク文字列最大長
    define('RECOMMENDS_MAX_PDF_LEN', MAX_URL_LEN);  /// @enum RECOMMENDS_MAX_PDF_LEN        おすすめ講座PDFリンク文字列最大長

    define('EVENTS_MAX_TITLE_LEN', 512);            /// @enum EVENTS_MAX_TITLE_LEN          イベントタイトル文字列最大長
    define('EVENTS_MAX_BODY_LEN', 512);             /// @enum EVENTS_MAX_BODY_LEN           イベント本文文字列最大長
    define('HOLIDAYS_MAX_NAME_LEN', 128);           /// @enum HOLIDAYS_MAX_NAME_LEN         祝日名文字列最大長

    define('MAX_FILE_SIZE', 10485760);              /// @enum MAX_FILE_SIZE                 アップロード可能な最大ファイルサイズ (10MB)

    define('MIN_ORDER_NO', -100000000);             /// @enum MIN_ORDER_NO                  並び順補正値最小値
    define('MAX_ORDER_NO', 100000000);              /// @enum MAX_ORDER_NO                  並び順補正値最大値

    //----- admin用だが、ここで定義しておく。 -----

    // リスト表示画面のソート順デフォルト値
    // 下記ファイルと同期が必要
    //     news_listAction.class.php (exec)
    //     news_listView.class.php (_genFormControls)
    //     recommends_listAction.class.php (exec)
    //     recommends_listView.class.php (_genFormControls)
    //     events_listAction.class.php (exec)
    //     events_listView.class.php (_genFormControls)


    /// @enum ADMIN_NEWS_LIST_DEFAULT_ORDER
    ///       What's Newリスト画面ソート順デフォルト値
    ///        - 0 => '日付順 (昇順)'
    ///        - 1 => '日付順 (降順)'
    ///        - 2 => 'ID順 (昇順)'
    ///        - 3 => 'ID順 (降順)'
    ///        - 4 => '校舎順 (昇順)'
    ///        - 5 => '校舎順 (降順)'
    ///        - 6 => '更新順 (昇順)'
    ///        - 7 => '更新順 (降順)'
    define('ADMIN_NEWS_LIST_DEFAULT_ORDER', 1);

    /// @enum ADMIN_RECOMMENDS_LIST_DEFAULT_ORDER
    ///       おすすめ講座リスト画面ソート順デフォルト値
    ///       - 0 => '講座順 (昇順)'
    ///       - 1 => '講座順 (降順)'
    ///       - 2 => 'ID順 (昇順)'
    ///       - 3 => 'ID順 (降順)'
    ///       - 4 => '校舎順 (昇順)'
    ///       - 5 => '校舎順 (降順)'
    ///       - 6 => '更新順 (昇順)'
    ///       - 7 => '更新順 (降順)'
    define('ADMIN_RECOMMENDS_LIST_DEFAULT_ORDER', 0);

    /// @enum ADMIN_EVENTS_LIST_DEFAULT_ORDER
    ///       イベントリスト画面ソート順デフォルト値
    ///       - 0 => '日付順 (昇順)'
    ///       - 1 => '日付順 (降順)'
    ///       - 2 => 'ID順 (昇順)'
    ///       - 3 => 'ID順 (降順)'
    ///       - 4 => '校舎順 (昇順)'
    ///       - 5 => '校舎順 (降順)'
    ///       - 6 => '更新順 (昇順)'
    ///       - 7 => '更新順 (降順)'
    define('ADMIN_EVENTS_LIST_DEFAULT_ORDER', 1);

    // リスト表示のときに切り詰める文字数 (mb_strimwidthを使うのでバイト数指定)
    define('ADMIN_NEWS_TITLE_DISPLAY_LENGTH', 32);          /// @enum ADMIN_NEWS_TITLE_DISPLAY_LENGTH       What's New:タイトル
    define('ADMIN_NEWS_LINK_DISPLAY_LENGTH', 32);           /// @enum ADMIN_NEWS_LINK_DISPLAY_LENGTH        What's New:URL
    define('ADMIN_RECOMMENDS_TITLE_DISPLAY_LENGTH', 32);    /// @enum ADMIN_RECOMMENDS_TITLE_DISPLAY_LENGTH おすすめ講座:タイトル
    define('ADMIN_RECOMMENDS_LINK_DISPLAY_LENGTH', 32);     /// @enum ADMIN_RECOMMENDS_LINK_DISPLAY_LENGTH  おすすめ講座:URL
    define('ADMIN_EVENTS_TITLE_DISPLAY_LENGTH', 32);        /// @enum ADMIN_EVENTS_TITLE_DISPLAY_LENGTH     イベント:タイトル
    define('ADMIN_EVENTS_BODY_DISPLAY_LENGTH', 48);         /// @enum ADMIN_EVENTS_BODY_DISPLAY_LENGTH      イベント:本文
    define('ADMIN_EVENTS_LINK_DISPLAY_LENGTH', 32);         /// @enum ADMIN_EVENTS_LINK_DISPLAY_LENGTH      イベント:URL
    define('ADMIN_FILES_ORG_NAME_DISPLAY_LENGTH', 64);      /// @enum ADMIN_FILES_ORG_NAME_DISPLAY_LENGTH   ファイル管理:オリジナルファイル名

    // adminフォーム要素用
    define('ADMIN_NEWS_TITLE_TEXT_BOX_SIZE', 64);           /// @enum ADMIN_NEWS_TITLE_TEXT_BOX_SIZE        What's New:タイトルテキストボックスサイズ
    define('ADMIN_NEWS_TITLE_SUB_TEXT_BOX_SIZE', 64);       /// @enum ADMIN_NEWS_TITLE_SUB_TEXT_BOX_SIZE    What's New:サブタイトルテキストボックスサイズ
    define('ADMIN_NEWS_URL_TEXT_BOX_SIZE', 64);             /// @enum ADMIN_NEWS_URL_TEXT_BOX_SIZE          What's New:URLリンクテキストボックスサイズ
    define('ADMIN_NEWS_PDF_FILE_CTL_SIZE', 64);             /// @enum ADMIN_NEWS_PDF_FILE_CTL_SIZE          What's New:PDFファイル選択コントロールサイズ
    define('ADMIN_NEWS_ORDER_TEXT_BOX_SIZE', 20);           /// @enum ADMIN_NEWS_ORDER_TEXT_BOX_SIZE        What's New:URL並び補正値テキストボックスサイズ

    define('ADMIN_RECOMMENDS_TITLE_TEXT_BOX_SIZE', 64);     /// @enum ADMIN_RECOMMENDS_TITLE_TEXT_BOX_SIZE  おすすめ講座:タイトルテキストボックスサイズ
    define('ADMIN_RECOMMENDS_TITLE_SUB_TEXT_BOX_SIZE', 64); /// @enum ADMIN_RECOMMENDS_TITLE_SUB_TEXT_BOX_SIZE  おすすめ講座:タイトルテキストボックスサイズ
    define('ADMIN_RECOMMENDS_URL_TEXT_BOX_SIZE', 64);       /// @enum ADMIN_RECOMMENDS_URL_TEXT_BOX_SIZE    おすすめ講座:URLリンクテキストボックスサイズ
    define('ADMIN_RECOMMENDS_PDF_FILE_CTL_SIZE', 64);       /// @enum ADMIN_RECOMMENDS_PDF_FILE_CTL_SIZE    おすすめ講座:PDFファイル選択コントロールサイズ
    define('ADMIN_RECOMMENDS_ORDER_TEXT_BOX_SIZE', 20);     /// @enum ADMIN_RECOMMENDS_ORDER_TEXT_BOX_SIZE  おすすめ講座:URL並び補正値テキストボックスサイズ

    define('ADMIN_EVENTS_TITLE_TEXT_BOX_SIZE', 64);         /// @enum ADMIN_EVENTS_TITLE_TEXT_BOX_SIZE      イベント:タイトルテキストボックスサイズ
    define('ADMIN_EVENTS_BODY_TEXT_BOX_SIZE', 64);          /// @enum ADMIN_EVENTS_BODY_TEXT_BOX_SIZE       イベント:本文テキストボックスサイズ
    define('ADMIN_EVENTS_ORDER_TEXT_BOX_SIZE', 20);         /// @enum ADMIN_EVENTS_ORDER_TEXT_BOX_SIZE      イベント:URL並び補正値テキストボックスサイズ

    // CSV取り込み用
    define('CSV_MAX_ERROR_COUNT', 30);                      /// @enum CSV_MAX_ERROR_COUNT   処理を中止する最大エラー数
    define('CSV_FILE_BUFFER_SIZE', 2048);                   /// @enum CSV_FILE_BUFFER_SIZE  ファイルバッファサイズ
    define('CSV_DETECT_ORDER', 'SJIS-win,EUCJP-WIN,UTF-8,JIS,ASCII');   /// @enum CSV_DETECT_ORDER  CSVファイルの文字エンコーディング検出順

    /// @enum   PUBLIC_DIR  公開ページのディレクトリ (URLではなくディレクトリパス/相対パスの場合は管理ツールを基準)
    /// @note   絶対パスでも、相対パスでも可。
    define('PUBLIC_DIR', '..');

    /// @enum   PDF_DIR     PDFファイル格納ディレクトリ (URLではなくディレクトリパス/相対パスの場合は管理ツールを基準)
    define('PDF_DIR', PUBLIC_DIR . DS . 'pdf');

    define('TACNUMBER', 'TACNUMBER');
    define('NAME', 'NAME');
    define('FURIGANA', 'FURIGANA');
    define('BIRTHDAY', 'BIRTHDAY');
    define('MAIL', 'MAIL');
    define('RELEASE', 'RELEASE');
    define('PHOTO', 'PHOTO');
    define('ZEIRISHI', 'ZEIRISHI');
    define('ZEIRISHI_KAMOKU', 'ZEIRISHI_KAMOKU');
    define('ALPHABET', 'ALPHABET');
    define('RADIO', 'RADIO');
    define('LIST', 'LIST');
    define('CHECKBOX', 'CHECKBOX');
    define('FREECOMMENT', 'FREECOMMENT');
    define('PERSONALINFORMATION', 'PERSONALINFORMATION');
    define('JYUKENTIKU1', 'JYUKENTIKU1');
    define('JYUKENTIKU2', 'JYUKENTIKU2');
    define('JYUKENTIKU3', 'JYUKENTIKU3');

    define('JYUKENTIKU_TEXT1', 'JYUKENTIKU_TEXT1');
    define('JYUKENTIKU_TEXT2', 'JYUKENTIKU_TEXT2');
    define('JYUKENTIKU_TEXT3', 'JYUKENTIKU_TEXT3');

    define('HIGHEST', 1);
    define('ADMIN', 2);
    define('NOMAL', 3);
    define('MAX_COUNT', 6);

    define('PER_PAGE', 20);                 /// @enum PER_PAGE      リスト画面で1ページに表示するデータ件数

    //------------------------------------------------
    // ソースコード記述エンコーディング
    //------------------------------------------------
    if (!defined('EFP_SRC_ENCODING')) {
        // define('EFP_SRC_ENCODING', 'eucJP-win');
        define('EFP_SRC_ENCODING', 'UTF-8');
    }
    //------------------------------------------------
    // 文字エンコーディング検出順
    //------------------------------------------------
    if (!defined('EFP_ENCODING_DETECT_ORDER')) {
        define('EFP_ENCODING_DETECT_ORDER', 'eucJP-win,SJIS-win,UTF-8,JIS,ASCII');
    }
    mb_detect_order(EFP_ENCODING_DETECT_ORDER);
    //------------------------------------------------
    // DB内のエンコーディング形式
    //     次のどれかを必ず指定すること(大文字小文字は関係ありません)。
    //     'eucjp-win', 'euc-jp', 'sjis', 'sjis-win', 'utf-8'
    //------------------------------------------------
    if (!defined('EFP_DB_ENCODING')) {
        // define('EFP_DB_ENCODING', 'eucJP-win');
        define('EFP_DB_ENCODING', 'UTF-8');
    }

    define('INFINITY_DATE', '9999-12-31 23:59:59');
    define('DEV_INFINITY_DATE', '1000-01-01 00:00:00');
    // CSVダウンロード用
    define('CSV_DIVIDE', 100);                             /// @enum CSV_DIVIDE データ取得単位 (n件ずつ取得して処理)

    define('ReceiveFile_ERR_OK', 0);                    /// @enum  エラー無し
    define('ReceiveFile_ERR_GENERIC', 1);               /// @enum  一般エラー
    define('ReceiveFile_ERR_PARTIAL', 2);               /// @enum  ファイルの一部しかアップロードされていない
    define('ReceiveFile_ERR_NO_FILE', 3);               /// @enum  アップロードされなかった
    define('ReceiveFile_ERR_NO_TMP_DIR', 4);            /// @enum  テンポラリディレクトリが存在しない
    define('ReceiveFile_ERR_CANT_WRITE_TMP_FILE', 5);   /// @enum  テンポラリファイルの書き込み失敗
    define('ReceiveFile_ERR_BY_EXTENSION', 6);          /// @enum  ファイルのアップロードが拡張モジュールによって停止された
    define('ReceiveFile_ERR_FILENAME', 7);              /// @enum  ファイル名が不正
    define('ReceiveFile_ERR_FILESIZE', 8);              /// @enum  ファイルサイズが大きすぎる
    define('ReceiveFile_ERR_MIME_TYPE', 9);             /// @enum  MIME TYPEが不正
    define('ReceiveFile_ERR_SAVE', 10);                 /// @enum  ファイルの書き込み失敗
    define('ReceiveFile_ERR_UNKNOWN', 11);              /// @enum  不明なエラー

    // system.php
    //------------------------------------------------------------------------------
    /// @file
    /// @brief  フレームワーク設定ファイル
    /// @author Yuichi Nakamura
    /// @date   Time-stamp: "2011-10-03 15:34:45"
    //------------------------------------------------------------------------------
    if (!defined('DS')) {
        define('DS', DIRECTORY_SEPARATOR);      // *nix -> '/', win* -> '\'
    }
    if (!defined('PS')) {
        define('PS', PATH_SEPARATOR);           // *nix -> ':', win* -> ';'
    }

    //------------------------------------------------
    // 基本的な値
    // - selectbox, radiobutton等で使います。
    //------------------------------------------------
    define('EFP_INVALID', -1);              /// @enum EFP_INVALID   無効値
    define('EFP_NIL', 0);                   /// @enum EFP_NIL       NIL値
    define('EFP_YES', 1);                   /// @enum EFP_YES       YES値
    define('EFP_NO', 2);                    /// @enum EFP_NO        NO値
    define('EFP_OK', 1);                    /// @enum EFP_OK        YES値
    define('EFP_NG', 2);                    /// @enum EFP_NG        NO値


    //------------------------------------------------
    // バージョン
    // - (対応システム.メジャーバージョン.マイナーバージョン.メンテナンス番号)
    //------------------------------------------------
    define('EFP_VERSION', 'PHP4.1.0.0');
    define('EFP_RELEASE_DATE', '2010-04-21 14:28:11');


    //------------------------------------------------
    // 環境定義シンボル
    //------------------------------------------------
    if (!defined('OS_WIN')) {
        define('OS_WIN', strtoupper(substr(PHP_OS, 0, 3) === 'WIN') ? true : false);
    }
    if (!defined('PHP_4')) {
        define('PHP_4', strtoupper(substr(phpversion(), 0, 1) === '4') ? true : false);
    }
    if (!defined('PHP_5')) {
        define('PHP_5', strtoupper(substr(phpversion(), 0, 1) === '5') ? true : false);
    }

    // HTTPS接続かどうか
    define('EFP_IS_HTTPS', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']) ? true : false);


    //------------------------------------------------
    // debug_level定義
    // 0 : リリース
    // 1 : 低
    // 2 : ↓
    // 3 : 高
    //------------------------------------------------
    if (!defined('EFP_DEBUG')) {
        define('EFP_DEBUG', 0);
    }

    //------------------------------------------------
    // ログ出力許可
    // true : 出力許可
    // false: 出力禁止
    // EFP_DEBUGが0のときは、EFP_DEBUG_LOGの値にかかわらずログ出力を行いません。
    //------------------------------------------------
    if (!defined('EFP_DEBUG_LOG')) {
        define('EFP_DEBUG_LOG', false);
    }

    //------------------------------------------------
    // エラー表示レベルの設定
    //------------------------------------------------
    {
        if (!defined('EFP_DEBUG')) {
            define('EFP_DEBUG', 0);
        }
        switch (EFP_DEBUG) {
        case 0:
            // リリース時は全てのエラー出力をオフにする
            ini_set('display_errors', 0);
            error_reporting(0);
            break;

        case 1:
            // 単純な実行時エラーを表示する
            ini_set('display_errors', 1);
            error_reporting(E_ERROR | E_WARNING | E_PARSE);
            break;

        case 2:
            // E_NOTICE 以外の全てのエラーを表示する
            // php.ini で設定されているデフォルト値
            ini_set('display_errors', 1);
            error_reporting(E_ALL ^ E_NOTICE);
            break;

        case 3:
        default:
            // 全ての PHP エラーを表示する
            ini_set('display_errors', 1);
    //      error_reporting(E_ALL);
            if (error_reporting() > 6143) {
                error_reporting(E_ALL & ~E_DEPRECATED);
            }
            break;
        }
    }

    //------------------------------------------------
    // フレームワークディレクトリ
    //------------------------------------------------
    if (!defined('EFP_CORE_DIR')) {
        define('EFP_CORE_DIR', dirname(__FILE__));
    }

    //------------------------------------------------
    // Web Application 本体の配置ディレクトリ
    //------------------------------------------------
    if (!defined('EFP_WEBAPP_DIR')) {
        define('EFP_WEBAPP_DIR', realpath(dirname(__FILE__) . DS. '..' . DS . 'webapp'));
    }

    //------------------------------------------------
    // 基本フィルタ格納ディレクトリ
    //------------------------------------------------
    if (!defined('EFP_FILTER_DIR')) {
        define('EFP_FILTER_DIR', EFP_CORE_DIR . DS . 'filter');
    }

    //------------------------------------------------
    // 作業用ディレクトリ
    //------------------------------------------------
    if (!defined('EFP_VAR_DIR')) {
        define('EFP_VAR_DIR', EFP_WEBAPP_DIR . DS . 'var');
    }

    //------------------------------------------------
    // PEAR等の外部ライブラリ配置ディレクトリ
    //------------------------------------------------
    if (!defined('EFP_OPT_DIR')) {
        define('EFP_OPT_DIR', realpath(dirname(__FILE__) . DS . '..' . DS . 'opt'));
    }

    //------------------------------------------------
    // Adodb用キャッシュディレクトリ
    //------------------------------------------------
    $ADODB_CACHE_DIR = EFP_OPT_DIR . DS . 'db_cache' . DS;

    //------------------------------------------------
    // アクションを格納しているリクエストオブジェクトのキー名
    //------------------------------------------------
    if (!defined('EFP_ACTION_KEY')) {
        define('EFP_ACTION_KEY', 'action');
    }

    //------------------------------------------------
    // デフォルトアクション名
    //------------------------------------------------
    if (!defined('EFP_DEFAULT_ACTION')) {
        define('EFP_DEFAULT_ACTION', 'default');
    }


    //------------------------------------------------
    // EFPライブラリ内のソースコード記述エンコーディング
    // ※ライブラリの文字エンコーディングを変更したときは、ここの値を書き換えてください。
    //------------------------------------------------
    if (!defined('EFP_LIB_ENCODING')) {
        define('EFP_LIB_ENCODING', 'eucJP-win');
    }

    //------------------------------------------------
    // 出力エンコーディング形式
    //------------------------------------------------
    if (!defined('EFP_TARGET_ENCODING')) {
        define('EFP_TARGET_ENCODING', 'UTF-8');
    }


    //------------------------------------------------
    /// 出力バッファの利用可否
    // - 使わなければメモリの節約になります。
    // - フィルタチェイン内で何らかの操作が必要な場合以外はfalseに設定してください。
    //------------------------------------------------
    if (!defined('EFP_USE_OUTPUT_BUFFER')) {
        define('EFP_USE_OUTPUT_BUFFER', false);
    }


    //------------------------------------------------
    /// 出力バッファ名
    //------------------------------------------------
    if (!defined('EFP_OUTPUT_BUFFER')) {
        define('EFP_OUTPUT_BUFFER', 'tyrant_output_buffer');
    }


    //------------------------------------------------
    // セッション名
    //------------------------------------------------
    if (!defined('EFP_SESSION_NAME')) {
        define('EFP_SESSION_NAME', session_name());
    }


    //------------------------------------------------
    // フォームデータ受信用トップレベル配列名
    //------------------------------------------------
    if (!defined('EFP_TOP_DATA_NAME')) {
        define('EFP_TOP_DATA_NAME', 'data');
    }


    //------------------------------------------------
    // 認証用パスワード記録タイプ
    //------------------------------------------------
    if (!defined('EFP_PASS_TYPE')) {
        define('EFP_PASS_TYPE', 'MD5');
    }


    //------------------------------------------------
    /// 出力ヘッダ文字列
    /// @note   header関数で文字エンコーディングを指定しないと、RPMでインストールした
    ///         PHP5.1x系で実行したときにブラウザがUTF-8と勘違いしてしまう。@n
    ///         他にも複合する要因があるかもしれないが、headerを出力しても害はないというか、
    ///         出力するべきなのでこれでよい。@n
    ///         実際の出力処理はモジュールフィルタに登録したheaderFilterが行う。
    //------------------------------------------------
    if (!defined('EFP_HEADER_STRING')) {
        //    define('EFP_HEADER_STRING', 'Content-Type: text/html; charset=Shift_JIS');
        define('EFP_HEADER_STRING', '');
    }

    define('WORK_DIR', TMP . 'work');
    define('PAGER_URL_VAR', 'start');
    define('PHP_4', false);
