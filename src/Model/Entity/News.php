<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * News Entity
 *
 * @property int $id
 * @property int $school_id
 * @property string $news_title
 * @property string|null $news_title_sub
 * @property \Cake\I18n\FrozenDate $news_date
 * @property string|null $news_url
 * @property \Cake\I18n\FrozenTime $enabled_from
 * @property \Cake\I18n\FrozenTime $enabled_to
 * @property int $order_no
 * @property bool $is_active
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property string|null $urgency
 *
 * @property \App\Model\Entity\School $school
 */
class News extends Entity
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
        'news_title' => true,
        'news_title_sub' => true,
        'news_date' => true,
        'news_url' => true,
        'enabled_from' => true,
        'enabled_to' => true,
        'order_no' => true,
        'is_active' => true,
        'created' => true,
        'modified' => true,
        'urgency' => true,
        'school' => true,
    ];
}
