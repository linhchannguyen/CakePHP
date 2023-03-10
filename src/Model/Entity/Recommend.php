<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Recommend Entity
 *
 * @property int $id
 * @property int $school_id
 * @property int $kouza_id
 * @property string $recommend_title
 * @property string|null $recommend_title_sub
 * @property string|null $recommend_url
 * @property \Cake\I18n\FrozenTime $enabled_from
 * @property \Cake\I18n\FrozenTime $enabled_to
 * @property int $order_no
 * @property bool $is_active
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property string|null $image_url1
 * @property string|null $image_url2
 * @property string|null $image_url3
 * @property string|null $sub_title1
 * @property string|null $sub_title2
 * @property string|null $sub_title3
 * @property string|null $sub_title4
 * @property string|null $sub_url1
 * @property string|null $sub_url2
 * @property string|null $sub_url3
 * @property string|null $sub_url4
 *
 * @property \App\Model\Entity\School $school
 * @property \App\Model\Entity\Kouza $kouza
 */
class Recommend extends Entity
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
        'recommend_title' => true,
        'recommend_title_sub' => true,
        'recommend_url' => true,
        'enabled_from' => true,
        'enabled_to' => true,
        'order_no' => true,
        'is_active' => true,
        'created' => true,
        'modified' => true,
        'image_url1' => true,
        'image_url2' => true,
        'image_url3' => true,
        'sub_title1' => true,
        'sub_title2' => true,
        'sub_title3' => true,
        'sub_title4' => true,
        'sub_url1' => true,
        'sub_url2' => true,
        'sub_url3' => true,
        'sub_url4' => true,
        'school' => true,
        'kouza' => true,
    ];
}
