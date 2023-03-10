<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Event Entity
 *
 * @property int $id
 * @property int $school_id
 * @property int $kouza_id
 * @property string $event_title
 * @property string|null $event_body
 * @property \Cake\I18n\FrozenTime $event_date
 * @property int $event_type
 * @property int $order_no
 * @property bool $is_active
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\School $school
 * @property \App\Model\Entity\Kouza $kouza
 */
class Event extends Entity
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
        'school_id' => true,
        'kouza_id' => true,
        'event_title' => true,
        'event_body' => true,
        'event_date' => true,
        'event_type' => true,
        'order_no' => true,
        'is_active' => true,
        'created' => true,
        'modified' => true,
        'school' => true,
        'kouza' => true,
    ];
}
