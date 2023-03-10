<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Criteo Entity
 *
 * @property int $no
 * @property string $courseid
 * @property string $id
 * @property string|null $name
 * @property string $url
 * @property string $bigimage
 * @property string|null $description
 * @property string|null $price
 * @property string|null $retailprice
 * @property string|null $recommendable
 * @property string|null $cooperation_flag
 * @property string $page_type
 * @property \Cake\I18n\FrozenDate|null $rtime
 * @property \Cake\I18n\FrozenDate|null $mtime
 * @property string|null $extra_atp
 */
class Criteo extends Entity
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
        'no' => true,
        'courseid' => true,
        'id' => true,
        'name' => true,
        'url' => true,
        'bigimage' => true,
        'description' => true,
        'price' => true,
        'retailprice' => true,
        'recommendable' => true,
        'cooperation_flag' => true,
        'page_type' => true,
        'rtime' => true,
        'mtime' => true,
        'extra_atp' => true,
    ];
}
