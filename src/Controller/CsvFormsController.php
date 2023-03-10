<?php
declare(strict_types=1);
namespace App\Controller;

use App\Repositories\Schools\SchoolRepository;
use App\Repositories\Kouzas\KouzaRepository;
use App\Repositories\EventTypes\EventTypeRepository;
use App\Repositories\News\NewsRepository;
use App\Repositories\Recommends\RecommendRepository;
use App\Repositories\Events\EventRepository;
use App\Repositories\Holidays\HolidayRepository;
use App\Helpers\Pager;


class CsvFormsController extends AppController {
    private $schoolRepository;
    private $kouzaRepository;
    private $eventTypeRepository;
    private $newsRepository;
    private $recommendRepository;
    private $eventRepository;
    private $holidayRepository;

    private $m_errors = [];
    private $m_cnt_errors = 0;
    private $m_cnt_records = 0;
    private $m_cnt_valid_records = 0;

    public function initialize(): void {
        parent::initialize();
        $this->viewBuilder()->setLayout('custom');
        $this->Schools = $this->fetchTable('Schools');
        $this->Kouzas = $this->fetchTable('Kouzas');
        $this->EventTypes = $this->fetchTable('EventTypes');
        $this->News = $this->fetchTable('News');
        $this->Recommends = $this->fetchTable('Recommends');
        $this->Events = $this->fetchTable('Events');
        $this->Holidays = $this->fetchTable('Holidays');
        $this->schoolRepository = new SchoolRepository($this->Schools);
        $this->kouzaRepository = new KouzaRepository($this->Kouzas);
        $this->eventTypeRepository = new EventTypeRepository($this->EventTypes);
        $this->newsRepository = new NewsRepository($this->News);
        $this->recommendRepository = new RecommendRepository($this->Recommends);
        $this->eventRepository = new EventRepository($this->Events);
        $this->holidayRepository = new HolidayRepository($this->Holidays);
    }

    public function index() {
        $controls = [];
        $hidden_ticket_tag = []; 
        $init_school_code_table = []; 
        $init_kouza_code_table = []; 
        $init_event_kind_code_table = [1 => 'セミナー', 2 => '講座説明会', 3 => '体験入学'];
        $school_code_table = $this->schoolRepository->getKeyValuePairsWithCondition(['id', 'school_name'], ['is_active' => true]) ?? $init_school_code_table;
        $kouza_code_table = $this->kouzaRepository->getKeyValuePairsWithCondition(['id', 'kouza_name'], ['is_active' => true]) ?? $init_kouza_code_table;
        $event_kind_code_table = $this->eventTypeRepository->getKeyValuePairsWithCondition(['id', 'event_type_name'], ['is_active' => true]) ?? $init_event_kind_code_table;

        $this->set('title_head', __d('csv_form', 'CSV_FORM_TITLE'));
        $this->set('controls', $controls);
        $this->set('hidden_ticket_tag', $hidden_ticket_tag);
        $this->set('school_code_table', $school_code_table);
        $this->set('kouza_code_table', $kouza_code_table);
        $this->set('event_kind_code_table', $event_kind_code_table);
    }

    public function csvNewsReceive() {
        $this->set('title_head', __d('csv_form', 'CSV_NEWS_RECEIVE_TITLE'));
        $return_url = '/csv_form/index';
        
        // CSVファイル受信処理
        $file = $this->request->getData('csv_news');
        $csv_file = $this->_receiveFile($file);
        if (!$csv_file) {
            $error_messages = [__d('csv_form', 'CSV_UPLOAD_FAIL')];
            $this->set('title_head', __d('csv_form', 'CSV_UPLOAD_ERROR_TITLE'));
            $this->set('error_messages', $error_messages);
            $this->set('return_url', $return_url);
            return;
        }

        // CsvNewsModelを作ってCSVファイル内のデータレコードに対するバリデーションを行う。
        // CSVファイルにエラーがなければ、csv_news_confirmにリダイレクト(複数ページに渡ってデータの確認を行う可能性があるため)
        // エラーがあるときは、csv_news_receiveViewを呼び出す。
        if (!($fp = @fopen($csv_file, 'r'))) {
            $error_messages = [
                __d('csv_form', 'CSV_OPEN_FAIL'),
                __d('csv_form', 'CSV_PARSE_FAIL')
            ];
            $error_messages[] = $this->_makeErrorReport();
            $this->set('error_messages', $error_messages);
            $this->set('return_url', $return_url);
            return;
        }

        $result = $this->_validateCsvNews($fp);
        if($result !== 0) {
            $error_messages= [__d('csv_form', 'CSV_PARSE_FAIL')];
            $error_messages[] = $this->_makeErrorReport();
            $this->set('error_messages', $error_messages);
            $this->set('return_url', $return_url);
            return;
        }
        // 作業ファイル名と、全体のレコード数をセッションに記録
        $this->request->getSession()->write('csv_news_file_path', $csv_file);
        $this->request->getSession()->write('csv_num_records', $this->m_cnt_records);
        $this->request->getSession()->write('csv_num_valid_records', $this->m_cnt_valid_records);
        $this->redirect('/csv_form/csv_news_receive_confirm');
    }

    public function csvNewsReceiveConfirm() {
        $this->set('title_head', __d('csv_form', 'CSV_NEWS_CONFIRM_TITLE'));
        $csv_file_path = $this->request->getSession()->read('csv_news_file_path', '');
        if($csv_file_path === '' || !($fp = @fopen($csv_file_path, 'r'))) {
            $error_messages = [
                __d('csv_form', 'CSV_OPEN_FAIL'),
                __d('csv_form', 'CSV_GET_INFO_FAIL')
            ];
            $this->set('title_head', __d('csv_form', 'CSV_UPLOAD_ERROR_TITLE'));
            $this->set('error_messages', $error_messages);
            return;
        }

        // ページネーション
        $offset = 0;                 // 何件目から表示するか指定
        $limit = PER_PAGE;           // 1ページに表示するデータ件数
        $url_var = PAGER_URL_VAR;    // ページ数設定用パラメータ名
        $url_current = 'csv_news_receive_confirm';
        $num_records = $this->request->getSession()->read('csv_num_valid_records', 0);
        $ary_options = $this->_configPagination($limit, $url_var, $num_records, $url_current);
        $pager = Pager::factory($ary_options);
        list($offset) = $pager->getOffsetByPageId();        // 配列で返される値を個々の変数で受けるときはlistで。
        $offset = (0 == $offset) ? 0 : $offset - 1;         // Pagerから返されるoffsetは1始まりなので。
        $pager_link = $pager->links;
        $table_body = [];

        // 表示レコード取得
        $records = $this->_getRecordsNews($fp, $offset, $limit);
        $table_body = $this->_makeTableTrCsvNewsList($records);

        $this->set('pager_link', $pager_link ?? ''); // Pagerが生成したページ遷移リンク文字列をuser経由でviewに渡す。
        $this->set('num_records', $num_records);
        $this->set('table_body', $table_body ?? []);
    }

    public function csvNewsReceiveFinish() {
        $this->set('title_head', __d('csv_form', 'CSV_NEWS_FINISH_TITLE'));
        $fname = $this->request->getSession()->read('csv_news_file_path', '');
        if($fname === '' || !($fp = @fopen($fname, 'r'))) {
            $error_messages = [
                __d('csv_form', 'CSV_OPEN_FAIL'),
                __d('csv_form', 'CSV_REGISTER_FAIL')
            ];
            $this->set('title_head', __d('csv_form', 'CSV_UPLOAD_ERROR_TITLE'));
            $this->set('error_messages', $error_messages);
            return;
        }

        while (false !== ($data = (fgetCsvWrapper($fp, CSV_FILE_BUFFER_SIZE, ',')))) {
            $r = mb_convert_variables(EFP_TARGET_ENCODING, CSV_DETECT_ORDER, $data);
            $params = [
                'school_id'      => arrayGet($data, 0, ''),     // 校舎ID
                'urgency'        => arrayGet($data, 1, ''),     // 緊急度
                'news_date'      => arrayGet($data, 2, ''),     // 日付
                'news_title'     => arrayGet($data, 3, ''),     // タイトル
                'news_title_sub' => arrayGet($data, 4, ''),     // サブタイトル
                'news_url'       => arrayGet($data, 5, ''),     // リンクURL
                'enabled_from'   => arrayGet($data, 6, ''),     // 掲載有効期間開始日時
                'enabled_to'     => arrayGet($data, 7, ''),     // 掲載有効期間終了日時
                'order_no'       => arrayGet($data, 8, ''),     // 並び順調整用パラメータ
                'is_active'      => arrayGet($data, 9, ''),     // 表示有効/無効フラグ
            ];

            // 空行はエラーにしないで読み飛ばす
            if (('' == $params['school_id']) && ('' == $params['urgency']) && ('' == $params['news_date']) && ('' == $params['news_title']) && ('' == $params['news_title_sub']) &&
                ('' == $params['news_url']) && ('' == $params['enabled_from']) && ('' == $params['enabled_to']) && ('' == $params['order_no']) &&
                ('' == $params['is_active'])) {
                continue;
            }

            $params['news_date'] = date('Y-m-d', strtotime($params['news_date']) ?: 0);
            if (strlen($params['enabled_from'])) {
                $params['enabled_from'] = date('Y-m-d H:i:s', strtotime($params['enabled_from']) ?: 0);
            } else {
                unset($params['enabled_from']);
            }
            if (strlen($params['enabled_to'])) {
                $params['enabled_to'] = date('Y-m-d H:i:s', strtotime($params['enabled_to']) ?: 0);
            } else {
                unset($params['enabled_to']);
            }
            $params['urgency'] = $params['urgency'] == 1 ? '高' : '';
            $params['is_active'] = (0 != $params['is_active']) ? 1 : 0;
            $params_ary[] = $params;
        }
        if($params_ary){
            $result = $this->newsRepository->createMany($params_ary);
            if (!$result) {
                $error_messages = [__d('csv_form', 'CSV_REGISTER_FAIL')];
                $this->set('title_head', __d('csv_form', 'CSV_UPLOAD_ERROR_TITLE'));
                $this->set('error_messages', $error_messages);
                return;
            }
        }
        @unlink($fname);
        $this->request->getSession()->delete('csv_news_file_path');
        $this->request->getSession()->delete('csv_num_records');
        $message = __d('csv_form', 'CSV_REGISTER_SUCCESS');
        $this->set('message', $message);
    }

    public function csvRecommendsReceive() {
        $this->set('title_head', __d('csv_form', 'CSV_RECOMMEND_RECEIVE_TITLE'));
        $return_url = '/csv_form/index';
        
        // CSVファイル受信処理
        $file = $this->request->getData('csv_recommends');
        $csv_file = $this->_receiveFile($file);
        if (!$csv_file) {
            $error_messages = [__d('csv_form', 'CSV_UPLOAD_FAIL')];
            $this->set('title_head', __d('csv_form', 'CSV_UPLOAD_ERROR_TITLE'));
            $this->set('error_messages', $error_messages);
            $this->set('return_url', $return_url);
            return;
        }

        // CsvRecommendsModelを作ってCSVファイル内のデータレコードに対するバリデーションを行う。
        // CSVファイルにエラーがなければ、csv_recommends_confirmにリダイレクト(複数ページに渡ってデータの確認を行う可能性があるため)
        // エラーがあるときは、csv_recommends_receiveViewを呼び出す。
        if (!($fp = @fopen($csv_file, 'r'))) {
            $error_messages = [
                __d('csv_form', 'CSV_OPEN_FAIL'),
                __d('csv_form', 'CSV_PARSE_FAIL')
            ];
            $error_messages[] = $this->_makeErrorReport();
            $this->set('error_messages', $error_messages);
            $this->set('return_url', $return_url);
            return;
        }

        $result = $this->_validateCsvRecommend($fp);
        if($result !== 0) {
            $error_messages= [__d('csv_form', 'CSV_PARSE_FAIL')];
            $error_messages[] = $this->_makeErrorReport();
            $this->set('error_messages', $error_messages);
            $this->set('return_url', $return_url);
            return;
        }
        // 作業ファイル名と、全体のレコード数をセッションに記録
        $this->request->getSession()->write('csv_recommend_file_path', $csv_file);
        $this->request->getSession()->write('csv_num_records', $this->m_cnt_records);
        $this->request->getSession()->write('csv_num_valid_records', $this->m_cnt_valid_records);
        $this->redirect('/csv_form/csv_recommends_receive_confirm');
    }

    public function csvRecommendsReceiveConfirm() {
        $this->set('title_head', __d('csv_form', 'CSV_RECOMMEND_CONFIRM_TITLE'));
        $csv_file_path = $this->request->getSession()->read('csv_recommend_file_path', '');
        if($csv_file_path === '' || !($fp = @fopen($csv_file_path, 'r'))) {
            $error_messages = [
                __d('csv_form', 'CSV_OPEN_FAIL'),
                __d('csv_form', 'CSV_GET_INFO_FAIL')
            ];
            $this->set('title_head', __d('csv_form', 'CSV_UPLOAD_ERROR_TITLE'));
            $this->set('error_messages', $error_messages);
            return;
        }

        // ページネーション
        $offset = 0;                 // 何件目から表示するか指定
        $limit = PER_PAGE;           // 1ページに表示するデータ件数
        $url_var = PAGER_URL_VAR;    // ページ数設定用パラメータ名
        $url_current = 'csv_recommends_receive_confirm';
        $num_records = $this->request->getSession()->read('csv_num_valid_records', 0);
        $ary_options = $this->_configPagination($limit, $url_var, $num_records, $url_current);
        $pager = Pager::factory($ary_options);
        list($offset) = $pager->getOffsetByPageId();        // 配列で返される値を個々の変数で受けるときはlistで。
        $offset = (0 == $offset) ? 0 : $offset - 1;         // Pagerから返されるoffsetは1始まりなので。
        $pager_link = $pager->links;
        $table_body = [];

        // 表示レコード取得
        $records = $this->_getRecordsRecommends($fp, $offset, $limit);
        $table_body = $this->_makeTableTrCsvRecommendsList($records);

        $this->set('pager_link', $pager_link ?? ''); // Pagerが生成したページ遷移リンク文字列をuser経由でviewに渡す。
        $this->set('num_records', $num_records);
        $this->set('table_body', $table_body ?? []);
    }

    public function csvRecommendsReceiveFinish() {
        $this->set('title_head', __d('csv_form', 'CSV_RECOMMEND_FINISH_TITLE'));
        $fname = $this->request->getSession()->read('csv_recommend_file_path', '');
        if($fname === '' || !($fp = @fopen($fname, 'r'))) {
            $error_messages = [
                __d('csv_form', 'CSV_OPEN_FAIL'),
                __d('csv_form', 'CSV_REGISTER_FAIL')
            ];
            $this->set('title_head', __d('csv_form', 'CSV_UPLOAD_ERROR_TITLE'));
            $this->set('error_messages', $error_messages);
            return;
        }

        while (false !== ($data = (fgetcsv_reg($fp, CSV_FILE_BUFFER_SIZE, ',')))) {
            $r = mb_convert_variables(EFP_TARGET_ENCODING, CSV_DETECT_ORDER, $data);
            $params = [
                'school_id'           => arrayGet($data, 0, ''),    // 校舎ID
                'kouza_id'            => arrayGet($data, 1, ''),    // 講座ID
                'recommend_title'     => arrayGet($data, 2, ''),    // タイトル
                'recommend_title_sub' => arrayGet($data, 3, ''),    // サブタイトル
                'recommend_url'       => arrayGet($data, 4, ''),    // リンクURL
                'sub_title1'          => arrayGet($data, 5, ''),      // 名称1
                'sub_url1'            => arrayGet($data, 6, ''),      // リンク1
                'sub_title2'          => arrayGet($data, 7, ''),      // 名称2
                'sub_url2'            => arrayGet($data, 8, ''),      // リンク2
                'sub_title3'          => arrayGet($data, 9, ''),      // 名称3
                'sub_url3'            => arrayGet($data, 10, ''),     // リンク3
                'sub_title4'          => arrayGet($data, 11, ''),     // 名称4
                'sub_url4'            => arrayGet($data, 12, ''),     // リンク4
                'image_url1'          => arrayGet($data, 13, ''),     // 画像1
                'image_url2'          => arrayGet($data, 14, ''),     // 画像2
                'image_url3'          => arrayGet($data, 15, ''),     // 画像3
                'enabled_from'        => arrayGet($data, 16, ''),    // 掲載有効期間開始日時
                'enabled_to'          => arrayGet($data, 17, ''),    // 掲載有効期間終了日時
                'order_no'            => arrayGet($data, 18, ''),    // 並び順調整用パラメータ
                'is_active'           => arrayGet($data, 19, ''),    // 表示有効/無効フラグ
            ];

            // 空行はエラーにしないで読み飛ばす
            if (('' == $params['school_id']) && ('' == $params['kouza_id']) && ('' == $params['recommend_title']) && ('' == $params['recommend_title_sub']) &&
                ('' == $params['recommend_url']) && ('' == $params['enabled_from']) && ('' == $params['enabled_to']) && ('' == $params['order_no']) &&
                ('' == $params['is_active']) && ('' == $params['sub_title1']) && ('' == $params['sub_url1']) && ('' == $params['sub_title2']) && ('' == $params['sub_url2']) &&
                ('' == $params['sub_title3']) && ('' == $params['sub_url3']) && ('' == $params['sub_title4']) && ('' == $params['sub_url4']) &&
                ('' == $params['image_url1']) && ('' == $params['image_url2']) && ('' == $params['image_url3'])
            ) {
                continue;
            }

            if (strlen($params['enabled_from'])) {
                $params['enabled_from'] = date('Y-m-d H:i:s', strtotime($params['enabled_from']) ?: 0);
            } else {
                unset($params['enabled_from']);
            }
            if (strlen($params['enabled_to'])) {
                $params['enabled_to'] = date('Y-m-d H:i:s', strtotime($params['enabled_to']) ?: 0);
            } else {
                unset($params['enabled_to']);
            }
            $params['is_active'] = (0 != $params['is_active']) ? 1 : 0;
            $params_ary[] = $params;
        }
        if($params_ary){
            $result = $this->recommendRepository->createMany($params_ary);
            if (!$result) {
                $error_messages = [__d('csv_form', 'CSV_REGISTER_FAIL')];
                $this->set('title_head', __d('csv_form', 'CSV_UPLOAD_ERROR_TITLE'));
                $this->set('error_messages', $error_messages);
                return;
            }
        }
        @unlink($fname);
        $this->request->getSession()->delete('csv_recommend_file_path');
        $this->request->getSession()->delete('csv_num_records');
        $message = __d('csv_form', 'CSV_REGISTER_SUCCESS');
        $this->set('message', $message);
    }

    public function csvEventsReceive() {
        $this->set('title_head', __d('csv_form', 'CSV_EVENT_RECEIVE_TITLE'));
        $return_url = '/csv_form/index';

        // CSVファイル受信処理
        $file = $this->request->getData('csv_events');
        $csv_file = $this->_receiveFile($file);
        if (!$csv_file) {
            $error_messages = [__d('csv_form', 'CSV_UPLOAD_FAIL')];
            $this->set('title_head', __d('csv_form', 'CSV_UPLOAD_ERROR_TITLE'));
            $this->set('error_messages', $error_messages);
            $this->set('return_url', $return_url);
            return;
        }

        // CsvEventsModelを作ってCSVファイル内のデータレコードに対するバリデーションを行う。
        // CSVファイルにエラーがなければ、csv_events_confirmにリダイレクト(複数ページに渡ってデータの確認を行う可能性があるため)
        // エラーがあるときは、csv_events_receiveViewを呼び出す。
        if (!($fp = @fopen($csv_file, 'r'))) {
            $error_messages = [
                __d('csv_form', 'CSV_OPEN_FAIL'),
                __d('csv_form', 'CSV_PARSE_FAIL')
            ];
            $error_messages[] = $this->_makeErrorReport();
            $this->set('error_messages', $error_messages);
            $this->set('return_url', $return_url);
            return;
        }

        $result = $this->_validateCsvEvent($fp);
        if($result !== 0) {
            $error_messages= [__d('csv_form', 'CSV_PARSE_FAIL')];
            $error_messages[] = $this->_makeErrorReport();
            $this->set('error_messages', $error_messages);
            $this->set('return_url', $return_url);
            return;
        }
        // 作業ファイル名と、全体のレコード数をセッションに記録
        $this->request->getSession()->write('csv_event_file_path', $csv_file);
        $this->request->getSession()->write('csv_num_records', $this->m_cnt_records);
        $this->request->getSession()->write('csv_num_valid_records', $this->m_cnt_valid_records);
        $this->redirect('/csv_form/csv_events_receive_confirm');
    }

    public function csvEventsReceiveConfirm() {
        $this->set('title_head', __d('csv_form', 'CSV_EVENT_CONFIRM_TITLE'));
        $csv_file_path = $this->request->getSession()->read('csv_event_file_path', '');
        if($csv_file_path === '' || !($fp = @fopen($csv_file_path, 'r'))) {
            $error_messages = [
                __d('csv_form', 'CSV_OPEN_FAIL'),
                __d('csv_form', 'CSV_GET_INFO_FAIL')
            ];
            $this->set('title_head', __d('csv_form', 'CSV_UPLOAD_ERROR_TITLE'));
            $this->set('error_messages', $error_messages);
            return;
        }

        // ページネーション
        $offset = 0;                 // 何件目から表示するか指定
        $limit = PER_PAGE;           // 1ページに表示するデータ件数
        $url_var = PAGER_URL_VAR;    // ページ数設定用パラメータ名
        $url_current = 'csv_events_receive_confirm';
        $num_records = $this->request->getSession()->read('csv_num_valid_records', 0);
        $ary_options = $this->_configPagination($limit, $url_var, $num_records, $url_current);
        $pager = Pager::factory($ary_options);
        list($offset) = $pager->getOffsetByPageId();        // 配列で返される値を個々の変数で受けるときはlistで。
        $offset = (0 == $offset) ? 0 : $offset - 1;         // Pagerから返されるoffsetは1始まりなので。
        $pager_link = $pager->links;
        $table_body = [];

        // 表示レコード取得
        $records = $this->_getRecordsEvents($fp, $offset, $limit);
        $table_body = $this->_makeTableTrCsvEventsList($records);

        $this->set('pager_link', $pager_link ?? ''); // Pagerが生成したページ遷移リンク文字列をuser経由でviewに渡す。
        $this->set('num_records', $num_records);
        $this->set('table_body', $table_body ?? []);
    }

    public function csvEventsReceiveFinish() {
        $this->set('title_head', __d('csv_form', 'CSV_EVENT_FINISH_TITLE'));
        $fname = $this->request->getSession()->read('csv_event_file_path', '');
        if($fname === '' || !($fp = @fopen($fname, 'r'))) {
            $error_messages = [
                __d('csv_form', 'CSV_OPEN_FAIL'),
                __d('csv_form', 'CSV_REGISTER_FAIL')
            ];
            $this->set('title_head', __d('csv_form', 'CSV_UPLOAD_ERROR_TITLE'));
            $this->set('error_messages', $error_messages);
            return;
        }

        while (false !== ($data = (fgetcsvWrapper($fp, CSV_FILE_BUFFER_SIZE, ',')))) {
            $r = mb_convert_variables(EFP_TARGET_ENCODING, CSV_DETECT_ORDER, $data);
            $params = [
                'school_id'    => arrayGet($data, 0, ''),        // 校舎ID
                'kouza_id'     => arrayGet($data, 1, ''),        // 講座ID
                'event_day'    => arrayGet($data, 2, ''),        // イベント開催日
                'event_time'   => arrayGet($data, 3, ''),        // イベント開催時刻
                'event_type'   => arrayGet($data, 4, ''),        // イベント種別ID
                'event_title'  => arrayGet($data, 5, ''),        // タイトル
                'event_body'   => arrayGet($data, 6, ''),        // 本文
                'order_no'     => arrayGet($data, 7, ''),        // 並び順調整用パラメータ
                'is_active'    => arrayGet($data, 8, ''),        // 表示有効/無効フラグ
            ];

            // 空行はエラーにしないで読み飛ばす
            if (('' == $params['school_id']) && ('' == $params['kouza_id']) && ('' == $params['event_day']) && ('' == $params['event_time']) &&
                ('' == $params['event_type']) && ('' == $params['event_title']) && ('' == $params['event_body']) && ('' == $params['order_no']) &&
                ('' == $params['is_active'])) {
                continue;
            }
            $params['is_active'] = (0 != $params['is_active']) ? 1 : 0;
            $params['event_date'] = date('Y-m-d H:i:s', strtotime($params['event_day'] . ' ' . $params['event_time']) ?: 0);
            $params_ary[] = $params;
        }
        if($params_ary){
            $result = $this->eventRepository->createMany($params_ary);
            if (!$result) {
                $error_messages = [__d('csv_form', 'CSV_REGISTER_FAIL')];
                $this->set('title_head', __d('csv_form', 'CSV_UPLOAD_ERROR_TITLE'));
                $this->set('error_messages', $error_messages);
                return;
            }
        }
        // ファイルとセッション情報の削除
        @unlink($fname);
        $this->request->getSession()->delete('csv_event_file_path');
        $this->request->getSession()->delete('csv_num_records');
        $message = __d('csv_form', 'CSV_REGISTER_SUCCESS');
        $this->set('message', $message);
    }

    public function csvHolidaysReceive() {
        $this->set('title_head', __d('csv_form', 'CSV_HOLIDAY_RECEIVE_TITLE'));
        $return_url = '/csv_form/index';

        // CSVファイル受信処理
        $file = $this->request->getData('csv_holidays');
        $csv_file = $this->_receiveFile($file);
        if (!$csv_file) {
            $error_messages = [__d('csv_form', 'CSV_UPLOAD_FAIL')];
            $this->set('title_head', __d('csv_form', 'CSV_UPLOAD_ERROR_TITLE'));
            $this->set('error_messages', $error_messages);
            $this->set('return_url', $return_url);
            return;
        }

        // CsvHolidaysModelを作ってCSVファイル内のデータレコードに対するバリデーションを行う。
        // CSVファイルにエラーがなければ、csv_holidays_confirmにリダイレクト(複数ページに渡ってデータの確認を行う可能性があるため)
        // エラーがあるときは、csv_holidays_receiveViewを呼び出す。
        if (!($fp = @fopen($csv_file, 'r'))) {
            $error_messages = [
                __d('csv_form', 'CSV_OPEN_FAIL'),
                __d('csv_form', 'CSV_PARSE_FAIL')
            ];
            $error_messages[] = $this->_makeErrorReport();
            $this->set('error_messages', $error_messages);
            $this->set('return_url', $return_url);
            return;
        }

        $result = $this->_validateCsvHoliday($fp);
        if($result !== 0) {
            $error_messages= [__d('csv_form', 'CSV_PARSE_FAIL')];
            $error_messages[] = $this->_makeErrorReport();
            $this->set('error_messages', $error_messages);
            $this->set('return_url', $return_url);
            return;
        }
        // 作業ファイル名と、全体のレコード数をセッションに記録
        $this->request->getSession()->write('csv_holiday_file_path', $csv_file);
        $this->request->getSession()->write('csv_num_records', $this->m_cnt_records);
        $this->request->getSession()->write('csv_num_valid_records', $this->m_cnt_valid_records);
        $this->redirect('/csv_form/csv_holidays_receive_confirm');
    }

    public function csvHolidaysReceiveConfirm() {
        $this->set('title_head', __d('csv_form', 'CSV_HOLIDAY_CONFIRM_TITLE'));
        $csv_file_path = $this->request->getSession()->read('csv_holiday_file_path', '');
        if($csv_file_path === '' || !($fp = @fopen($csv_file_path, 'r'))) {
            $error_messages = [
                __d('csv_form', 'CSV_OPEN_FAIL'),
                __d('csv_form', 'CSV_GET_INFO_FAIL')
            ];
            $this->set('title_head', __d('csv_form', 'CSV_UPLOAD_ERROR_TITLE'));
            $this->set('error_messages', $error_messages);
            return;
        }
        // ページネーション
        $offset = 0;                 // 何件目から表示するか指定
        $limit = PER_PAGE;           // 1ページに表示するデータ件数
        $url_var = PAGER_URL_VAR;    // ページ数設定用パラメータ名
        $url_current = 'csv_holidays_receive_confirm';
        $num_records = $this->request->getSession()->read('csv_num_valid_records', 0);
        $ary_options = $this->_configPagination($limit, $url_var, $num_records, $url_current);
        $pager = Pager::factory($ary_options);
        list($offset) = $pager->getOffsetByPageId();        // 配列で返される値を個々の変数で受けるときはlistで。
        $offset = (0 == $offset) ? 0 : $offset - 1;         // Pagerから返されるoffsetは1始まりなので。
        $pager_link = $pager->links;
        $table_body = [];

        // 表示レコード取得
        $records = $this->_getRecordsHolidays($fp, $offset, $limit);
        $table_body = $this->_makeTableTrCsvHolidaysList($records);

        $this->set('pager_link', $pager_link ?? ''); // Pagerが生成したページ遷移リンク文字列をuser経由でviewに渡す。
        $this->set('num_records', $num_records);
        $this->set('table_body', $table_body ?? []);
    }

    public function csvHolidaysReceiveFinish() {
        $this->set('title_head', __d('csv_form', 'CSV_HOLIDAY_FINISH_TITLE'));
        $fname = $this->request->getSession()->read('csv_holiday_file_path', '');
        if($fname === '' || !($fp = @fopen($fname, 'r'))) {
            $error_messages = [
                __d('csv_form', 'CSV_OPEN_FAIL'),
                __d('csv_form', 'CSV_REGISTER_FAIL'),
                __d('csv_form', 'CSV_DUPLICATE_DATA_REGISTER_FAIL')
            ];
            $this->set('title_head', __d('csv_form', 'CSV_UPLOAD_ERROR_TITLE'));
            $this->set('error_messages', $error_messages);
            return;
        }

        while (false !== ($data = (fgetcsvWrapper($fp, CSV_FILE_BUFFER_SIZE, ',')))) {
            $r = mb_convert_variables(EFP_TARGET_ENCODING, CSV_DETECT_ORDER, $data);
            $params = [
                'holiday_date' => arrayGet($data, 0, ''),   // 祝日日付
                'holiday_name' => arrayGet($data, 1, ''),   // 祝日名
                'is_active'    => true,                     // 表示有効/無効フラグ
            ];

            // 空行はエラーにしないで読み飛ばす
            if (('' == $params['holiday_date']) && ('' == $params['holiday_name'])) {
                continue;
            }

            $params['holiday_date'] = date('Y-m-d', strtotime($params['holiday_date']) ?: 0);
            $params_ary[] = $params;
        }
        if($params_ary){
            $result = $this->holidayRepository->createMany($params_ary);
            if (!$result) {
                $error_messages = [
                    __d('csv_form', 'CSV_REGISTER_FAIL'),
                    __d('csv_form', 'CSV_DUPLICATE_DATA_REGISTER_FAIL')
                ];
                $this->set('title_head', __d('csv_form', 'CSV_UPLOAD_ERROR_TITLE'));
                $this->set('error_messages', $error_messages);
                return;
            }
        }
        // ファイルとセッション情報の削除
        // @unlink($fname);
        // $this->request->getSession()->delete('csv_holiday_file_path');
        // $this->request->getSession()->delete('csv_num_records');
        $message = __d('csv_form', 'CSV_REGISTER_SUCCESS');
        $this->set('message', $message);
    }

    private function _receiveFile($file) {
        if(!$file || $file->getError() > 0 || $file->getSize() >= MAX_FILE_SIZE ) {
            return false;
        }

        if (!file_exists(WORK_DIR)) {
            mkdir(WORK_DIR, 0755, true);
        }

        $fname = $this->_genRandomName();
        $imagePath = WORK_DIR . DS . $fname;

        $file->moveTo($imagePath);
        $new_fname = $fname . '.csv';
        rename(WORK_DIR . DS . $fname, WORK_DIR . DS . $new_fname);
        $path = WORK_DIR . DS . $new_fname; // URLとして使うので、パスセパレータは'/'でいい
        return $path;
    }

    private function _genRandomName($length=12) {
        return genRndString(1, 'aA') . genRndString($length-1, 'aA0');
    }

    private function _makeErrorReport() {
        $str = '<table class="catalog"><thead><tr><th>行番号</th><th>エラー原因</th></tr></thead><tbody>';
        foreach ($this->m_errors as $line => $msgs) {
            foreach ($msgs as $msg) {
                $str .= '<tr><td>' . $line . '</td><td>' . $msg . '</td></tr>';
            }
        }
        $str .= '</tbody></table>';
        if (CSV_MAX_ERROR_COUNT < $this->m_cnt_errors) {
            $str .= '<p style="margin-top:20px;">' . __d('csv_form', 'CSV_LIMIT_ROWS', [CSV_MAX_ERROR_COUNT]) .'</p>';
        }
        return $str;
    }

    private function _configPagination($limit, $url_var, $num_records, $url_current) {
        $ary_options = [
            'mode' => 'Sliding',           // 表示スタイル
            'perPage' => $limit,           // 1ページに何件表示するか
            'delta' => 5,                  // ページ移動用のリンクに何ページ分表示するか
            'clearIfVoid' => true,         // falseだと1ページしかないときもリンクを表示
            'urlVar' => $url_var,          // 次に表示するページ番号のURL変数名を指定
            'totalItems' => $num_records,  // 全記事数
            'prevImg' => toOutputEncoding('前へ', EFP_SRC_ENCODING),
            'nextImg' => toOutputEncoding('次へ', EFP_SRC_ENCODING),
            'firstPagePre' => '[',
            'firstPageText' => toOutputEncoding('先頭', EFP_SRC_ENCODING),
            'firstPagePost' => ']',
            'lastPagePre' => '[',
            'lastPageText' => toOutputEncoding('最後', EFP_SRC_ENCODING),
            'lastPagePost' => ']',
            'fixFileName' => false,
            'fileName' => '/csv_form' .DS . $url_current,
            'curTag' => 'u'
        ];

        return $ary_options;
    }

    private function _validateCsvNews($fp) {
        $this->m_cnt_records = 0;           // レコード(行)カウンタ
        $this->m_cnt_valid_records = 0;     // 有効なレコードカウンタ
        $data = [];
        while (false !== ($data = (fgetcsvWrapper($fp, CSV_FILE_BUFFER_SIZE, ',')))) {
            $this->m_cnt_records++;       // 行番号は1始まりとする
            // $this->clearFormDataAll();          // 使い回すので、まずクリア
            $r = mb_convert_variables(EFP_TARGET_ENCODING, CSV_DETECT_ORDER, $data);

            $vdata = [
                'school_id'      => arrayGet($data, 0, ''),      // 校舎ID
                'urgency'        => arrayGet($data, 1, ''),      // 緊急度
                'news_date'      => arrayGet($data, 2, ''),      // 日付
                'news_title'     => arrayGet($data, 3, ''),      // タイトル
                'news_title_sub' => arrayGet($data, 4, ''),      // サブタイトル
                'news_url'       => arrayGet($data, 5, ''),      // リンクURL
                'enabled_from'   => arrayGet($data, 6, ''),      // 掲載有効期間開始日時
                'enabled_to'     => arrayGet($data, 7, ''),      // 掲載有効期間終了日時
                'order_no'       => arrayGet($data, 8, ''),      // 並び順調整用パラメータ
                'is_active'      => arrayGet($data, 9, ''),      // 表示有効/無効フラグ
            ];

            // 空行はエラーにしないで読み飛ばす
            if (('' == $vdata['school_id']) && ('' == $vdata['urgency']) && ('' == $vdata['news_date']) && ('' == $vdata['news_title']) && ('' == $vdata['news_title_sub']) &&
                ('' == $vdata['news_url']) && ('' == $vdata['enabled_from']) && ('' == $vdata['enabled_to']) && ('' == $vdata['order_no']) &&
                ('' == $vdata['is_active'])) {
                continue;
            }
            $validData = $this->News->newEntity($vdata);
            $n = count($validData->getErrors());
            if($n == 0){
                $vdata['enabled_from'] = strlen($vdata['enabled_from']) ? date('Y-m-d H:i:s', strtotime($vdata['enabled_from'])) : null;
                $vdata['enabled_to'] = strlen($vdata['enabled_to']) ? date('Y-m-d H:i:s', strtotime($vdata['enabled_to'])) : null;
            }
            $this->m_cnt_errors += $n;
            if ($n > 0) {
                foreach($validData->getErrors() as $key => $value) {
                    $this->m_errors[$this->m_cnt_records][] = reset($value); // 実質的にメッセージは1つしかないので決め打ち
                }
            }

            if (CSV_MAX_ERROR_COUNT < $this->m_cnt_errors) {
                break;
            }
            $this->m_cnt_valid_records++;
        }
        return $this->m_cnt_errors;
    }

    private function _validateCsvRecommend($fp) {
        $this->m_cnt_records = 0;           // レコード(行)カウンタ
        $this->m_cnt_valid_records = 0;     // 有効なレコードカウンタ
        $data = [];
        while (false !== ($data = (fgetcsv_reg($fp, CSV_FILE_BUFFER_SIZE, ',')))) {
            $this->m_cnt_records++;             // 行番号は1始まりとする
            // $this->clearFormDataAll();          // 使い回すので、まずクリア
            $r = mb_convert_variables(EFP_TARGET_ENCODING, CSV_DETECT_ORDER, $data);
            $vdata = [
                'school_id'           => arrayGet($data, 0, ''),      // 校舎ID
                'kouza_id'            => arrayGet($data, 1, ''),      // 講座ID
                'recommend_title'     => arrayGet($data, 2, ''),      // タイトル
                'recommend_title_sub' => arrayGet($data, 3, ''),      // サブタイトル
                'recommend_url'       => arrayGet($data, 4, ''),      // リンクURL
                'sub_title1'          => arrayGet($data, 5, ''),      // 名称1
                'sub_url1'            => arrayGet($data, 6, ''),      // リンク1
                'sub_title2'          => arrayGet($data, 7, ''),      // 名称2
                'sub_url2'            => arrayGet($data, 8, ''),      // リンク2
                'sub_title3'          => arrayGet($data, 9, ''),      // 名称3
                'sub_url3'            => arrayGet($data, 10, ''),     // リンク3
                'sub_title4'          => arrayGet($data, 11, ''),     // 名称4
                'sub_url4'            => arrayGet($data, 12, ''),     // リンク4
                'image_url1'          => arrayGet($data, 13, ''),     // 画像1
                'image_url2'          => arrayGet($data, 14, ''),     // 画像2
                'image_url3'          => arrayGet($data, 15, ''),     // 画像3
                'enabled_from'        => arrayGet($data, 16, ''),     // 掲載有効期間開始日時
                'enabled_to'          => arrayGet($data, 17, ''),     // 掲載有効期間終了日時
                'order_no'            => arrayGet($data, 18, ''),     // 並び順調整用パラメータ
                'is_active'           => arrayGet($data, 19, ''),     // 表示有効/無効フラグ
            ];

            // 空行はエラーにしないで読み飛ばす
            if (('' == $vdata['school_id']) && ('' == $vdata['kouza_id']) && ('' == $vdata['recommend_title']) && ('' == $vdata['recommend_title_sub']) &&
                ('' == $vdata['recommend_url']) && ('' == $vdata['enabled_from']) && ('' == $vdata['enabled_to']) && ('' == $vdata['order_no']) &&
                ('' == $vdata['is_active']) && ('' == $vdata['sub_title1']) && ('' == $vdata['sub_url1']) && ('' == $vdata['sub_title2']) && ('' == $vdata['sub_url2']) &&
            	('' == $vdata['sub_title3']) && ('' == $vdata['sub_url3']) && ('' == $vdata['sub_title4']) && ('' == $vdata['sub_url4']) &&
            	('' == $vdata['image_url1'])  && ('' == $vdata['image_url2'])  && ('' == $vdata['image_url3']) ) {
                continue;
            }
            $validData = $this->Recommends->newEntity($vdata);

            $n = count($validData->getErrors());
            if($n == 0){
                $vdata['enabled_from'] = strtotime($vdata['enabled_from']) ? date('Y-m-d H:i:s', strtotime($vdata['enabled_from'])) : $vdata['enabled_from'];
                $vdata['enabled_to'] = strtotime($vdata['enabled_to']) ? date('Y-m-d H:i:s', strtotime($vdata['enabled_to'])) : $vdata['enabled_to'];
            }
            $this->m_cnt_errors += $n;
            if ($n > 0) {
                foreach($validData->getErrors() as $key => $value) {
                    $this->m_errors[$this->m_cnt_records][] = reset($value); // 実質的にメッセージは1つしかないので決め打ち
                }
            }

            if (CSV_MAX_ERROR_COUNT < $this->m_cnt_errors) {
                break;
            }
            $this->m_cnt_valid_records++;
        }
        return $this->m_cnt_errors;
    }

    private function _validateCsvEvent($fp) {
        $this->m_cnt_records = 0;           // レコード(行)カウンタ
        $this->m_cnt_valid_records = 0;     // 有効なレコードカウンタ
        $data = [];
        while (false !== ($data = (fgetcsvWrapper($fp, CSV_FILE_BUFFER_SIZE, ',')))) {
            $this->m_cnt_records++;             // 行番号は1始まりとする
            // $this->clearFormDataAll();          // 使い回すので、まずクリア
            $r = mb_convert_variables(EFP_TARGET_ENCODING, CSV_DETECT_ORDER, $data);
            // バリデーションを行う前に、'event_day', 'event_time'を合成して'event_date'データを作る
            $vdata = [
                'school_id'    => arrayGet($data, 0, ''),        // 校舎ID
                'kouza_id'     => arrayGet($data, 1, ''),        // 講座ID
                'event_day'    => arrayGet($data, 2, ''),        // イベント開催日
                'event_time'   => arrayGet($data, 3, ''),        // イベント開催時刻
                'event_type'   => arrayGet($data, 4, ''),        // イベント種別ID
                'event_title'  => arrayGet($data, 5, ''),        // タイトル
                'event_body'   => arrayGet($data, 6, ''),        // 本文
                'order_no'     => arrayGet($data, 7, ''),        // 並び順調整用パラメータ
                'is_active'    => arrayGet($data, 8, ''),        // 表示有効/無効フラグ
            ];

            // 空行はエラーにしないで読み飛ばす
            if (('' == $vdata['school_id']) && ('' == $vdata['kouza_id']) && ('' == $vdata['event_day']) && ('' == $vdata['event_time']) &&
                ('' == $vdata['event_type']) && ('' == $vdata['event_title']) && ('' == $vdata['event_body']) && ('' == $vdata['order_no']) &&
                ('' == $vdata['is_active'])) {
                continue;
            }
            $vdata['event_date'] = $vdata['event_day'] . ' ' . $vdata['event_time'];
            $validData = $this->Events->newEntity($vdata);
            
            $n = count($validData->getErrors());
            if($n == 0){
                $vdata['event_day'] = strtotime($vdata['event_day']) ? date('Y-m-d', strtotime($vdata['event_day'])) : $vdata['event_day'];
                $vdata['event_time'] = strtotime($vdata['event_time']) ? date('H:i:s', strtotime($vdata['event_time'])) : $vdata['event_time'];
                $vdata['event_date'] = $vdata['event_day'] . ' ' . $vdata['event_time'];
            }
            $this->m_cnt_errors += $n;
            if ($n > 0) {
                foreach($validData->getErrors() as $key => $value) {
                    $this->m_errors[$this->m_cnt_records][] = reset($value); // 実質的にメッセージは1つしかないので決め打ち
                }
            }

            if (CSV_MAX_ERROR_COUNT < $this->m_cnt_errors) {
                break;
            }
            $this->m_cnt_valid_records++;
        }
        return $this->m_cnt_errors;
    }

    private function _validateCsvHoliday($fp) {
        $this->m_cnt_records = 0;           // レコード(行)カウンタ
        $this->m_cnt_valid_records = 0;     // 有効なレコードカウンタ
        $data = [];
        while (false !== ($data = (fgetcsvWrapper($fp, CSV_FILE_BUFFER_SIZE, ',')))) {
            $this->m_cnt_records++;             // 行番号は1始まりとする
            // $this->clearFormDataAll();          // 使い回すので、まずクリア
            $r = mb_convert_variables(EFP_TARGET_ENCODING, CSV_DETECT_ORDER, $data);
            $vdata = [
                'holiday_date' => arrayGet($data, 0, ''),    // 祝日日付
                'holiday_name' => arrayGet($data, 1, ''),    // 祝日名
                // 'is_active'    => arrayGet($data, 2, ''),    // 表示有効/無効フラグ
            ];

            // 空行はエラーにしないで読み飛ばす
            if (('' == $vdata['holiday_date']) && ('' == $vdata['holiday_name'])) {
                continue;
            }

            $validData = $this->Holidays->newEntity($vdata);
            $n = count($validData->getErrors());
            $this->m_cnt_errors += $n;
            if ($n > 0) {
                foreach($validData->getErrors() as $key => $value) {
                    $this->m_errors[$this->m_cnt_records][] = reset($value); // 実質的にメッセージは1つしかないので決め打ち
                }
            }

            if (CSV_MAX_ERROR_COUNT < $this->m_cnt_errors) {
                break;
            }
            $this->m_cnt_valid_records++;
        }
        return $this->m_cnt_errors;
    }

    private function _getRecordsNews($fp, $offset, $limit) {
        // キャッシュから読み出すのでエラーチェックは不要
        $schools_info = $this->schoolRepository->getKeyValuePairsWithCondition(['id', 'school_name'], ['is_active' => true]) ?? [];

        $cnt_records = 0;           // 作業用レコード(行)カウンタ
        $cnt_cooked_records = 0;
        $data = [];
        $vo_ary = [];
        while (false !== ($data = (fgetcsvWrapper($fp, CSV_FILE_BUFFER_SIZE, ',')))) {
            if ($cnt_records++ < $offset) {
                continue;
            }
            if ($limit <= $cnt_cooked_records++) {
                break;
            }

            $r = mb_convert_variables(EFP_TARGET_ENCODING, CSV_DETECT_ORDER, $data);
            $vo = [
                'school_id'      => arrayGet($data, 0, ''),     // 校舎ID
                'urgency'        => arrayGet($data, 1, ''),     // 緊急度
                'news_date'      => arrayGet($data, 2, ''),     // 日付
                'news_title'     => arrayGet($data, 3, ''),     // タイトル
                'news_title_sub' => arrayGet($data, 4, ''),     // サブタイトル
                'news_url'       => arrayGet($data, 5, ''),     // リンクURL
                'enabled_from'   => arrayGet($data, 6, ''),     // 掲載有効期間開始日時
                'enabled_to'     => arrayGet($data, 7, ''),     // 掲載有効期間終了日時
                'order_no'       => arrayGet($data, 8, ''),     // 並び順調整用パラメータ
                'is_active'      => arrayGet($data, 9, ''),     // 表示有効/無効フラグ
            ];

            // 空行はエラーにしないで読み飛ばす
            if (('' == $vo['school_id']) && ('' == $vo['urgency']) && ('' == $vo['news_date']) && ('' == $vo['news_title']) && ('' == $vo['news_title_sub']) &&
                ('' == $vo['news_url']) && ('' == $vo['enabled_from']) && ('' == $vo['enabled_to']) && ('' == $vo['order_no']) &&
                ('' == $vo['is_active'])) {
                continue;
            }

            $school_id = intval($vo['school_id']);
            $vo['news_date'] = date('Y-m-d', strtotime($vo['news_date']));
            $vo['enabled_from'] = strlen($vo['enabled_from']) ? date('Y-m-d H:i:s', strtotime($vo['enabled_from']) ?: 0) : null;
            $vo['enabled_to'] = strlen($vo['enabled_to']) ? date('Y-m-d H:i:s', strtotime($vo['enabled_to']) ?: 0) : null;
            $vo['school_name'] = $schools_info[$school_id] ?? '';
            $vo['is_active'] = (0 != $vo['is_active']) ? true : false;
            $vo_ary[] = $vo;
        }
        return $vo_ary;
    }

    private function _getRecordsRecommends($fp, $offset, $limit){
        // キャッシュから読み出すのでエラーチェックは不要
        $schools_info = $this->schoolRepository->getKeyValuePairsWithCondition(['id', 'school_name'], ['is_active' => true]) ?? [];
        $kouzas_info = $this->kouzaRepository->getKeyValuePairsWithCondition(['id', 'kouza_name'], ['is_active' => true]) ?? [];

        $cnt_records = 0;           // 作業用レコード(行)カウンタ
        $cnt_cooked_records = 0;
        $data = [];
        $vo_ary = [];
        while (false !== ($data = (fgetcsv_reg($fp, CSV_FILE_BUFFER_SIZE, ',')))) {
            if ($cnt_records++ < $offset) {
                continue;
            }
            if ($limit <= $cnt_cooked_records++) {
                break;
            }
            $r = mb_convert_variables(EFP_TARGET_ENCODING, CSV_DETECT_ORDER, $data);
            $vo = [
                'school_id'           => arrayGet($data, 0, ''),      // 校舎ID
                'kouza_id'            => arrayGet($data, 1, ''),      // 講座ID
                'recommend_title'     => arrayGet($data, 2, ''),      // タイトル
                'recommend_title_sub' => arrayGet($data, 3, ''),      // サブタイトル
                'recommend_url'       => arrayGet($data, 4, ''),      // リンクURL

                'sub_title1'          => arrayGet($data, 5, ''),      // 名称1
                'sub_url1'            => arrayGet($data, 6, ''),      // リンク1
                'sub_title2'          => arrayGet($data, 7, ''),      // 名称2
                'sub_url2'            => arrayGet($data, 8, ''),      // リンク2
                'sub_title3'          => arrayGet($data, 9, ''),      // 名称3
                'sub_url3'            => arrayGet($data, 10, ''),     // リンク3
                'sub_title4'          => arrayGet($data, 11, ''),     // 名称4
                'sub_url4'            => arrayGet($data, 12, ''),     // リンク4

                'image_url1'          => arrayGet($data, 13, ''),     // 画像1
                'image_url2'          => arrayGet($data, 14, ''),     // 画像2
                'image_url3'          => arrayGet($data, 15, ''),     // 画像3

                'enabled_from'        => arrayGet($data, 16, ''),     // 掲載有効期間開始日時
                'enabled_to'          => arrayGet($data, 17, ''),     // 掲載有効期間終了日時
                'order_no'            => arrayGet($data, 18, ''),     // 並び順調整用パラメータ
                'is_active'           => arrayGet($data, 19, ''),     // 表示有効/無効フラグ
            ];

            // 空行はエラーにしないで読み飛ばす
            if (('' == $vo['school_id']) && ('' == $vo['kouza_id']) && ('' == $vo['recommend_title']) && ('' == $vo['recommend_title_sub']) &&
                ('' == $vo['recommend_url']) && ('' == $vo['enabled_from']) && ('' == $vo['enabled_to']) && ('' == $vo['order_no']) &&
                ('' == $vo['is_active']) && ('' == $vo['sub_title1']) && ('' == $vo['sub_url1']) && ('' == $vo['sub_title2']) && ('' == $vo['sub_url2']) &&
                ('' == $vo['sub_title3']) && ('' == $vo['sub_url3']) && ('' == $vo['sub_title4']) && ('' == $vo['sub_url4']) &&
                ('' == $vo['image_url1'])  && ('' == $vo['image_url2'])  && ('' == $vo['image_url3'])) {
                continue;
            }

            $school_id = intval($vo['school_id']);      // データがゼロフィルされているときに備えて明示的に整数値へ変換する
            $kouza_id = intval($vo['kouza_id']);        // データがゼロフィルされているときに備えて明示的に整数値へ変換する
            $vo['enabled_from'] = (strlen($vo['enabled_from'])) ? date('Y-m-d H:i:s', strtotime($vo['enabled_from']) ?: 0) : null;
            $vo['enabled_to'] = (strlen($vo['enabled_to'])) ? date('Y-m-d H:i:s', strtotime($vo['enabled_to']) ?: 0) : null;
            $vo['school_name'] = $schools_info[$school_id] ?? '';
            $vo['kouza_name'] = $kouzas_info[$kouza_id] ?? '';
            $vo['is_active'] = (0 != $vo['is_active']) ? true : false;
            $vo_ary[] = $vo;
        }
        return $vo_ary;
    }

    private function _getRecordsEvents($fp, $offset, $limit) {
        // キャッシュから読み出すのでエラーチェックは不要
        $schools_info = $this->schoolRepository->getKeyValuePairsWithCondition(['id', 'school_name'], ['is_active' => true]) ?? [];
        $kouzas_info = $this->kouzaRepository->getKeyValuePairsWithCondition(['id', 'kouza_name'], ['is_active' => true]) ?? [];
        $event_types_info = $this->eventTypeRepository->getKeyValuePairsWithCondition(['id', 'event_type_name'], ['is_active' => true]) ?? [];

        $cnt_records = 0;           // 作業用レコード(行)カウンタ
        $cnt_cooked_records = 0;
        $data = [];
        $vo_ary = [];

        while (false !== ($data = (fgetcsvWrapper($fp, CSV_FILE_BUFFER_SIZE, ',')))) {
            if ($cnt_records++ < $offset) {
                continue;
            }
            if ($limit <= $cnt_cooked_records++) {
                break;
            }
            $r = mb_convert_variables(EFP_TARGET_ENCODING, CSV_DETECT_ORDER, $data);
            $vo = [
                'school_id'    => arrayGet($data, 0, ''),        // 校舎ID
                'kouza_id'     => arrayGet($data, 1, ''),        // 講座ID
                'event_day'    => arrayGet($data, 2, ''),        // イベント開催日
                'event_time'   => arrayGet($data, 3, ''),        // イベント開催時刻
                'event_type'   => arrayGet($data, 4, ''),        // イベント種別ID
                'event_title'  => arrayGet($data, 5, ''),        // タイトル
                'event_body'   => arrayGet($data, 6, ''),        // 本文
                'order_no'     => arrayGet($data, 7, ''),        // 並び順調整用パラメータ
                'is_active'    => arrayGet($data, 8, ''),        // 表示有効/無効フラグ
            ];

            // 空行はエラーにしないで読み飛ばす
            if (('' == $vo['school_id']) && ('' == $vo['kouza_id']) && ('' == $vo['event_day']) && ('' == $vo['event_time']) &&
                ('' == $vo['event_type']) && ('' == $vo['event_title']) && ('' == $vo['event_body']) && ('' == $vo['order_no']) &&
                ('' == $vo['is_active'])) {
                continue;
            }

            $school_id = intval($vo['school_id']);      // データがゼロフィルされているときに備えて明示的に整数値へ変換する
            $kouza_id = intval($vo['kouza_id']);        // データがゼロフィルされているときに備えて明示的に整数値へ変換する
            $event_type = intval($vo['event_type']);    // データがゼロフィルされているときに備えて明示的に整数値へ変換する
            $vo['event_date'] = date('Y-m-d H:i:s', strtotime($vo['event_day'] . ' ' . $vo['event_time']) ?: 0);
            $vo['school_name'] = $schools_info[$school_id] ?? '';
            $vo['kouza_name'] = $kouzas_info[$kouza_id] ?? '';
            $vo['event_type_name'] = $event_types_info[$event_type] ?? '';
            $vo['is_active'] = (0 != $vo['is_active']) ? true : false;
            $vo_ary[] = $vo;
        }
        return $vo_ary;
    }

    private function _getRecordsHolidays($fp, $offset, $limit) {
        $cnt_records = 0;           // 作業用レコード(行)カウンタ
        $cnt_cooked_records = 0;
        $data = [];
        $vo_ary = [];

        while (false !== ($data = (fgetcsvWrapper($fp, CSV_FILE_BUFFER_SIZE, ',')))) {
            if ($cnt_records++ < $offset) {
                continue;
            }
            if ($limit <= $cnt_cooked_records++) {
                break;
            }
            $r = mb_convert_variables(EFP_TARGET_ENCODING, CSV_DETECT_ORDER, $data);
            $vo = [
                'holiday_date'    => arrayGet($data, 0, ''),        // 祝日日付
                'holiday_name'     => arrayGet($data, 1, ''),        // 祝日名
                'is_active'    => arrayGet($data, 2, ''),        // 表示有効/無効フラグ
            ];

            // 空行はエラーにしないで読み飛ばす
            if (('' == $vo['holiday_date']) && ('' == $vo['holiday_name'])) {
                continue;
            }
            $vo['holiday_date'] = (strlen($vo['holiday_date'])) ? date('Y-m-d', strtotime($vo['holiday_date']) ?: 0) : null;
            $vo['is_active'] = (0 != $vo['is_active']) ? true : false;
            $vo_ary[] = $vo;
        }
        return $vo_ary;
    }

    private function _makeTableTrCsvNewsList($records) {
        $cnt = count($records);
        $tag = [];
        for ($i = 0; $i < $cnt; $i++) {
            // 並び順
            // 校舎、タイトル日付、タイトル、サブタイトル、リンク、並び補正、表示、登録日、有効期間（始）、有効期間（終）
            $r = $records[$i];
            $r['school_id'] = intval($r['school_id']);      // データがゼロフィルされているときに備えて明示的に整数値へ変換する
            if ($r['is_active']) {
                $is_active = '○';
                $visible_class = 'class_active';
            } else {
                $is_active = '×';
                $visible_class = 'class_inactive';
            }
            $r['is_active'] = $is_active;
            $r['visible_class'] = $visible_class;
            $r['enabled_from'] = $r['enabled_from'] ? cleanTags($r['enabled_from']) : '-';
            $r['enabled_to'] = $r['enabled_to'] ? cleanTags($r['enabled_to']) : '-';
            $r['urgency'] = cleanTags($r['urgency'] == 1 ? '高' : '');

            // mb_strimwidthは文字数ではなくバイト数で指定するみたい
            $news_title = mb_strimwidth($r['news_title'], 0, ADMIN_NEWS_TITLE_DISPLAY_LENGTH, '…', EFP_TARGET_ENCODING);
            $r['news_title_limit'] = cleanTags($news_title);
            $news_title_sub = mb_strimwidth($r['news_title_sub'], 0, ADMIN_NEWS_TITLE_DISPLAY_LENGTH, '…', EFP_TARGET_ENCODING);
            $r['news_title_sub_limit'] = cleanTags($news_title_sub);
            $news_url = mb_strimwidth($r['news_url'], 0, ADMIN_NEWS_LINK_DISPLAY_LENGTH, '…', EFP_TARGET_ENCODING);
            $tr_class = ($i % 2) ? 'even' : 'odd';
            $r['tr_class'] = $tr_class;
            $r['title_url_line_limit'] = cleanTags($news_url);

            $tag[$i] = $r;
        }

        return $tag;
    }

    private function _makeTableTrCsvRecommendsList($records) {
        $cnt = count($records);
        $tag = [];
        for ($i = 0; $i < $cnt; $i++) {
            // 並び順
            // 校舎、講座、タイトル、サブタイトル、リンク、並び補正、表示、登録日、有効期間（始）、有効期間（終）
            $r = $records[$i];
            $r['school_id'] = intval($r['school_id']);      // データがゼロフィルされているときに備えて明示的に整数値へ変換する
            $r['kouza_id'] = intval($r['kouza_id']);        // データがゼロフィルされているときに備えて明示的に整数値へ変換する
            if ($r['is_active']) {
                $is_active = '○';
                $visible_class = 'class_active';
            } else {
                $is_active = '×';
                $visible_class = 'class_inactive';
            }
            $r['is_active'] = $is_active;
            $r['visible_class'] = $visible_class;
            $r['enabled_from'] = $r['enabled_from'] ? cleanTags($r['enabled_from']) : '-';
            $r['enabled_to'] = $r['enabled_to'] ? cleanTags($r['enabled_to']) : '-';

            // mb_strimwidthは文字数ではなくバイト数で指定するみたい
            $recommend_title = mb_strimwidth($r['recommend_title'], 0, ADMIN_RECOMMENDS_TITLE_DISPLAY_LENGTH, '…', EFP_SRC_ENCODING);
            $r['recommend_title_limit'] = cleanTags($recommend_title);
            $recommend_title_sub = mb_strimwidth($r['recommend_title_sub'], 0, ADMIN_RECOMMENDS_TITLE_DISPLAY_LENGTH, '…', EFP_SRC_ENCODING);
            $r['recommend_title_sub_limit'] = cleanTags($recommend_title_sub);
            $recommend_url = mb_strimwidth($r['recommend_url'], 0, ADMIN_RECOMMENDS_LINK_DISPLAY_LENGTH, '…', EFP_SRC_ENCODING);
            $r['title_url_line_limit'] = cleanTags($recommend_url);
            $tag[$i] = $r;
        }
        return $tag;
    }

    private function _makeTableTrCsvEventsList($records) {
        $cnt = count($records);
        $tag = [];
        for ($i = 0; $i < $cnt; $i++) {
            // 並び順
            // 校舎、講座、イベント種別、日時、タイトル、本文、並び補正、表示
            $r = $records[$i];
            $r['school_id'] = intval($r['school_id']);      // データがゼロフィルされているときに備えて明示的に整数値へ変換する
            $r['kouza_id'] = intval($r['kouza_id']);        // データがゼロフィルされているときに備えて明示的に整数値へ変換する
            $r['event_type'] = intval($r['event_type']);    // データがゼロフィルされているときに備えて明示的に整数値へ変換する
            if ($r['is_active']) {
                $is_active = '○';
                $visible_class = 'class_active';
            } else {
                $is_active = '×';
                $visible_class = 'class_inactive';
            }
            $r['is_active'] = $is_active;
            $r['visible_class'] = $visible_class;

            // mb_strimwidthは文字数ではなくバイト数で指定するみたい
            $event_title = mb_strimwidth($r['event_title'], 0, ADMIN_EVENTS_TITLE_DISPLAY_LENGTH, '…', EFP_SRC_ENCODING);
            $r['event_title_limit'] = cleanTags($event_title);
            $event_body = mb_strimwidth($r['event_body'], 0, ADMIN_EVENTS_BODY_DISPLAY_LENGTH, '…', EFP_SRC_ENCODING);
            $r['event_body_limit'] = cleanTags($event_body);
            $tag[$i] = $r;
        }
        return $tag;
    }

    private function _makeTableTrCsvHolidaysList($records) {
        $cnt = count($records);
        $tag = [];
        for ($i = 0; $i < $cnt; $i++) {
            // 並び順
            // 日付、祝日名
            $r = $records[$i];
            $r['holiday_date'] = cleanTags($r['holiday_date']);
            $r['holiday_name'] = cleanTags($r['holiday_name']);
            $tag[$i] = $r;
        }
        return $tag;
    }
}