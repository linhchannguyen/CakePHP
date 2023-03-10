<?php
/**
 * To change this template use File | Settings | File Templates.
 *
 */
namespace App\Repositories\VoiceUserFormDatas;

interface VoiceUserFormDataRepositoryInterface {
    public function getDetailUserFormDataByFormID($formId);
    public function createOrUpdate($data);
    public function findById($id);
    public function destroy($id);
    public function getByConditions($conditions = [], $fields = [], $orders = [], $options = []);
    public function countByConditions($conditions = [], $fields = [], $orders = []);
}
