<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * KouzaSeikyu Entity
 *
 * @property int $id
 * @property string $kouza_name
 * @property string|null $selectbox_message
 * @property bool|null $delete_flag
 * @property string $sort_cd
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime|null $modified
 */
class KouzaSeikyu extends Entity
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
        'id' => true,
        'kouza_name' => true,
        'selectbox_message' => true,
        'delete_flag' => true,
        'sort_cd' => true,
        'created' => true,
        'modified' => true,
    ];
}
