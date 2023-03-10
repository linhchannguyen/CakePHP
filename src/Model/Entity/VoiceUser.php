<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;
use Authentication\PasswordHasher\LegacyPasswordHasher;

/**
 * VoiceUser Entity
 *
 * @property int $id
 * @property string $username
 * @property string $password
 * @property int $category_id
 * @property int $role_id
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime|null $modified
 */
class VoiceUser extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array<string, bool>
     */
    protected $_accessible = [
        'username' => true,
        'password' => true,
        'category_id' => true,
        'role_id' => true,
        'created' => true,
        'modified' => true,
    ];

    /**
     * Fields that are excluded from JSON versions of the entity.
     *
     * @var array<string>
     */
    protected $_hidden = [
        'password',
    ];

    /**
     * Hashing password before save
     *
     * @var array<string>
     */
    public function _setPassword($password) {
        if (strlen($password) > 0) {
            $hasher = new LegacyPasswordHasher();
            return $hasher->hash($password);
        }
    }
}
