<?php
/**
 * To change this template use File | Settings | File Templates.
 *
 */
namespace App\Repositories\VoiceRoles;

interface VoiceRoleRepositoryInterface {
    public function getRoleByID($roleId);
    public function getListRoles();
    public function getListRoleIDByRole($roleLevel);
}
