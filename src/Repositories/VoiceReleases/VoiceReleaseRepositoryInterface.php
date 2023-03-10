<?php
/**
 * To change this template use File | Settings | File Templates.
 *
 */
namespace App\Repositories\VoiceReleases;

interface VoiceReleaseRepositoryInterface {
    public function getListByInitial($initial);
}
