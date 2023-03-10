<?php
/**
 * To change this template use File | Settings | File Templates.
 *
 */
namespace App\Repositories\VoiceZeirishiLists;

interface VoiceZeirishiListRepositoryInterface {
    public function getList();
    public function getListID();
    public function getListSelectedFieldsWithKeyValue($keyField, $valueField);
}
