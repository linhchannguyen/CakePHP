<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * KouzaCourse Entity
 *
 * @property int $id
 * @property int $kbn_cd
 * @property string|null $kbn_name
 * @property string $kouza_cd
 * @property string|null $kouza_name
 * @property string $course_cd
 * @property string|null $course_name
 * @property string|null $note
 * @property int|null $brand_id
 * @property bool|null $delete_flag
 * @property string $sort_cd
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime|null $modified
 */
class KouzaCourse extends Entity
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
        'kbn_cd' => true,
        'kbn_name' => true,
        'kouza_cd' => true,
        'kouza_name' => true,
        'course_cd' => true,
        'course_name' => true,
        'note' => true,
        'brand_id' => true,
        'delete_flag' => true,
        'sort_cd' => true,
        'created' => true,
        'modified' => true,
    ];
}
