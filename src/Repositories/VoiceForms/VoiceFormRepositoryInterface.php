<?php
/**
 * To change this template use File | Settings | File Templates.
 *
 */
namespace App\Repositories\VoiceForms;

interface VoiceFormRepositoryInterface {
    public function getVoiceFormsByCategoryID($categoryId);
    public function getVoiceFormByIDWithAssoc($formId);
    public function getVoiceFormByID($formId);
    public function updateFields($formId, $data);
    public function createOrUpdate($data);
    public function checkExistVoiceFormsWithCategoryID($formId, $categoryId);
}
