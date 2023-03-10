<?php
/**
 * To change this template use File | Settings | File Templates.
 *
 */
namespace App\Repositories\VoiceCategories;

interface VoiceCategoryRepositoryInterface {
    public function getVoiceCategories($orderBy = null);
    public function getVoiceCategoryByID($categoryId);
    public function getAllCategories();
}
