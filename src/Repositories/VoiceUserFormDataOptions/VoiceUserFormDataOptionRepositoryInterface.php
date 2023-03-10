<?php
/**
 * To change this template use File | Settings | File Templates.
 *
 */
namespace App\Repositories\VoiceUserFormDataOptions;

interface VoiceUserFormDataOptionRepositoryInterface {
    public function getUserFormDataOptionByPartID($partId, $zeiriSearchLists);
    public function getUserFormDataOptionByUserFormID($userFormId);
    public function createOrUpdate($data);
}
