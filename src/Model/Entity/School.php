<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * School Entity
 *
 * @property int $id
 * @property string $school_name
 * @property string|null $school_url
 * @property string $school_tag_name
 * @property int $order_no
 * @property bool $is_active
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Event[] $events
 * @property \App\Model\Entity\News[] $news
 * @property \App\Model\Entity\Recommend[] $recommends
 */
class School extends Entity
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
        'school_name' => true,
        'school_url' => true,
        'school_tag_name' => true,
        'order_no' => true,
        'is_active' => true,
        'created' => true,
        'modified' => true,
        'events' => true,
        'news' => true,
        'recommends' => true,
    ];
}
