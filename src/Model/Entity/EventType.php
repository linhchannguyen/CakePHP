<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * EventType Entity
 *
 * @property int $id
 * @property string $icon
 * @property int $order_no
 * @property string $event_type_name
 * @property string $inner_class
 * @property string $content
 * @property bool $is_active
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 */
class EventType extends Entity
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
        'icon' => true,
        'order_no' => true,
        'event_type_name' => true,
        'inner_class' => true,
        'content' => true,
        'is_active' => true,
        'created' => true,
        'modified' => true,
    ];
}
