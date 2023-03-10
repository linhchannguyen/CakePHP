<?php
/**
 * To change this template use File | Settings | File Templates.
 *
 */
namespace App\Repositories\VoiceUsers;

interface VoiceUserRepositoryInterface {
    public function getByUsernameCategoryID($username, $categoryId);
    public function getAllUsers();
    public function deletebyID($userId);
    public function getbyID($userId);
    public function createOrUpdate($data);
}
