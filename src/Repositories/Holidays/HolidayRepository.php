<?php
namespace App\Repositories\Holidays;

use App\Repositories\BaseRepository;
use App\Repositories\Holidays\HolidayRepositoryInterface;
use Exception;

class HolidayRepository extends BaseRepository implements HolidayRepositoryInterface {
    public function paginate($conditions = []) {
        try {
            return $this->model->find()->where($conditions);
        } catch (\Throwable$th) {
            return false;
        }
    }

    public function createMany($data) {
        $dates = [];
        foreach ($data as $dt) {
            if (in_array($dt['holiday_date'], $dates)) {
                return false;
            }
            $dates[] = $dt['holiday_date'];
            $result  = $this->model->find()->where(['holiday_date' => $dt['holiday_date']])->first();
            if ($result) {
                return false;
            }
        }
        $entity = $this->model->newEntities($data);
        return $this->model->saveMany($entity);
    }

    //------------------------------------------------
    /// @brief  holiday_dateカラムの値を元にレコードを取得する
    /// @param  $dates   holiday_date値 もしくは holiday_dateの配列
    /// @retval !false  レコード
    /// @retval false   エラー
    /// @author Hao Trinh
    //------------------------------------------------
    public function getDataSetByDate($dates) {
        try {
            // DATE配列が空のときは黙って空の結果を返す
            if (empty($dates)) {
                return [];
            }
            return $this->model->find()->where(['holiday_date IN' => $dates])->all()->toList();
        } catch (\Throwable$th) {
            return false;
        }
    }

    //------------------------------------------------
    /// @brief  holiday_dateカラムを元にレコードを削除する
    /// @param  $dates   holiday_date値 もしくは holiday_dateの配列
    /// @retval !false  成功
    /// @retval false   エラー
    /// @author Hao Trinh
    //------------------------------------------------
    public function deleteDBByDate($dates) {
        try {
            $data = $this->getDataSetByDate($dates);
            if (!$data) {
                return false;
            }
            return $this->model->deleteAll(['Holidays.holiday_date IN' => $dates]);
        } catch (Exception $e) {
            return 500;
        }
    }
}
