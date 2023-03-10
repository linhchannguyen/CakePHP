<?php
/**
 * To change this template use File | Settings | File Templates.
 *
 */
namespace App\Repositories\VoiceParts;

interface VoicePartRepositoryInterface {
    public function getListByFormID($formId, $orders = []);
    public function getByID($partId);
    public function getAllByFormID($formId, $orders = []);
    public function countByFormID($formId, $hidden = 0, $notConditions = []);
    public function deleteByPartID($partId);
    public function getListPartIDBySlug($slug);
    public function checkExistVoicePartsWithCategoryID($partId, $categoryId);
}
