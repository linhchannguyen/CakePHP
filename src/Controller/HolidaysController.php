<?php
declare(strict_types=1);

namespace App\Controller;

use App\Repositories\Holidays\HolidayRepository;
use Cake\Http\Exception\InternalErrorException;

class HolidaysController extends AppController {
    private $holidayRepository;
    public $paginate = ['limit' => PER_PAGE];

    public function initialize(): void {
        parent::initialize();
        $this->viewBuilder()->setLayout('custom');
        $this->holidayRepository = new HolidayRepository($this->Holidays);
    }

    //------------------------------------------------
    /// @brief  処理を実行
    /// @param  なし
    /// @return なし
    /// @author Hao Trinh
    //------------------------------------------------
    public function index() {
        $this->set('title_head', __d('holiday', 'HOLIDAY_LIST_TITLE'));
        $this->request->getSession()->delete('HolidaysModel');
        $query = $this->holidayRepository->paginate();
        if (!$query) {
            $error_messages = [__d('holiday', 'QUERY_DATABASE_ERROR')];
            $this->set('title_head', __d('holiday', 'PAGE_ERROR_TITLE'));
            $this->set('error_messages', $error_messages);
            return;
        }

        $limit = PER_PAGE; // Number of data displayed on one page
        $lastPage = (int) ceil($query->count() / $limit);
        $page = $this->request->getQuery('page', 1);
        // Calculate page number
        if ($lastPage < $page) {
            $this->request = $this->request->withQueryParams(['page' => $lastPage]);
        }
        $records = $this->paginate($query)->toArray();
        $num_records = $query->count();
        $table_body = $this->_makeTableTrHolidaysList($records, true, 'ids[]');

        $this->set('num_records', $num_records);
        $this->set('table_body', $table_body);
    }

    //------------------------------------------------------------------------------
    /// @brief  祝日情報一括操作Actionクラス
    /// @author Hao Trinh
    //------------------------------------------------------------------------------
    public function holidaysChangeRecords() {
        if(!$this->request->getData('delete')) {
            $this->set('title_head', __d('holiday', 'PAGE_ERROR_TITLE'));
            return;
        }
        $dates = $this->request->getData('ids') ?? [];
        $this->request->getSession()->write('HolidaysModel', $dates);
        $records = $this->holidayRepository->getDataSetByDate($dates);
        if ($records === false) {
            $error_messages = [__d('holiday', 'QUERY_DATABASE_ERROR')];
            $this->set('title_head', __d('holiday', 'PAGE_ERROR_TITLE'));
            $this->set('error_messages', $error_messages);
            return;
        }
        $table_body = $this->_makeTableTrHolidaysList($records, true);

        $this->set('title_head', __d('holiday', 'HOLIDAY_CONFIRM_DELETE_TITLE'));
        $this->set('table_body', $table_body);
        $this->render('holidays_change_records_delete_confirm');
    }

    //------------------------------------------------
    /// @brief  処理を実行
    /// @param  なし
    /// @return なし
    /// @author Hao Trinh
    //------------------------------------------------
    public function holidaysChangeRecordsDeleteConfirm() {
        $this->set('title_head', __d('holiday', 'HOLIDAY_CONFIRM_DELETE_TITLE'));
        $dates = $this->request->getData('ids') ?? [];
        $records = $this->holidayRepository->getDataSetByDate($dates);
        $table_body = $this->_makeTableTrHolidaysList($records, true);
        $this->set('table_body', $table_body);
        $this->render('holidays_change_records_delete_confirm');
    }

    //------------------------------------------------
    /// @brief  処理を実行
    /// @param  なし
    /// @return なし
    /// @author Hao Trinh
    //------------------------------------------------
    public function holidaysChangeRecordsDeleteFinish() {
        $this->set('title_head', __d('holiday', 'HOLIDAY_FINISH_DELETE_TITLE'));
        if ($this->request->is('get')) {
            return;
        }
        $dates = $this->request->getSession()->read('HolidaysModel');
        $result = $this->holidayRepository->deleteDBByDate($dates);
        if ($result === 500) {
            throw new InternalErrorException();
        }
        // 一括操作用ID配列を削除
        $dates = $this->request->getSession()->delete('HolidaysModel');
    }

    //------------------------------------------------
    /// @brief  祝日情報リスト用テーブルのtr行(複数)を生成する
    /// @param  $records        テーブル出力用データ配列
    /// @param  $with_checkbox  チェックボックスを追加するかどうか
    /// @param  $checkbox_name  チェックボックスのnemeフィールドに設定する名前 (デフォルトは'ids[]')
    /// @return $data
    /// @author Hao Trinh
    //------------------------------------------------
    private function _makeTableTrHolidaysList($records, $with_checkbox=false, $checkbox_name='ids[]') {
        $cnt = count($records);
        $tag = [];
        for ($i = 0; $i < $cnt; $i++) {
            // 並び順
            // ID, ファイル名, コメント(オリジナル名), サイズ, 作成日時
            $r = $records[$i];
            $r['checkbox_name'] = $checkbox_name;
            $r['holiday_date'] = cleanTags($r['holiday_date']->format('Y-m-d'));
            $r['holiday_name'] = cleanTags($r['holiday_name']);
            $r['created'] = cleanTags($r['created']->format('Y-m-d H:i'));
            $r['modified'] = cleanTags($r['modified']->format('Y-m-d H:i'));
            $r['tr_class'] = ($i % 2) ? 'even' : 'odd';
            $tag[$i] = $r;
        }

        return $tag;
    }
}
