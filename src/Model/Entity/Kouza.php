<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Kouza Entity
 *
 * @property int $id
 * @property string $kouza_name
 * @property string $kouza_url
 * @property int $order_no
 * @property bool $is_active
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\ActiveEventType[] $active_event_types
 * @property \App\Model\Entity\Event[] $events
 * @property \App\Model\Entity\Recommend[] $recommends
 */
class Kouza extends Entity
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
        'kouza_name' => true,
        'kouza_url' => true,
        'order_no' => true,
        'is_active' => true,
        'created' => true,
        'modified' => true,
        'active_event_types' => true,
        'events' => true,
        'recommends' => true,
    ];
}
