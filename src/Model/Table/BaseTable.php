<?php
declare(strict_types=1);

namespace App\Model\Table;
use Cake\ORM\Locator\LocatorAwareTrait;

use Cake\ORM\Table;

class BaseTable extends Table {
    use LocatorAwareTrait;
    
    public function initialize(array $config): void {
        parent::initialize($config);
    }

    //------------------------------------------------------------------------------
    // カスタムバリデーション
    //------------------------------------------------------------------------------
    //------------------------------------------------
    /// @brief  日付の正当性を検査する
    /// @param  $data           検査するデータへの参照
    /// @return 結果 (true/false)
    /// @author Yuichi Nakamura
    //------------------------------------------------
    public function validateDate($data) {
        if ((0 < strlen($data)) && isDate($data)) {
            return true;
        }
        return false;
    }

    //------------------------------------------------
    /// @brief  日時の正当性を検査する
    /// @param  $data           検査するデータへの参照
    /// @return 結果 (true/false)
    /// @author Yuichi Nakamura
    //------------------------------------------------
    public function validateDateTime($data) {
        // 時刻を省略可とするかどうかは、呼び出す検査関数で選択してください。
        // - isDateTime       : 日時文字列 (秒は省略可)
        // - isDateOrDateTime : 日付 or 日時文字列 (時刻は省略可)
        if ((0 == strlen($data)) || isDateTime($data) || in_array($data, ['-infinity', 'infinity'])) {
            return true;
        }
        return false;
    }
}