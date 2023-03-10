<?php
/**
 * To change this template use File | Settings | File Templates.
 *
 */
namespace App\Repositories\VoicePartOptions;

interface VoicePartOptionRepositoryInterface {
    public function getListByPartID($partId);
}
