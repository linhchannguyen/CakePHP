<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * VoicePart Entity
 *
 * @property int $id
 * @property int $form_id
 * @property string $title_name
 * @property int $head_id
 * @property string|null $place_holder
 * @property int|null $char_limit
 * @property int $required
 * @property string $textbox1
 * @property string $textbox2
 * @property string|null $slug
 * @property int $hidden
 * @property int|null $select_count
 * @property int|null $fix_form
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property int|null $form_display_order
 * @property int|null $initial
 * @property int|null $public_display_order
 */
class VoicePart extends Entity
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
        'form_id' => true,
        'title_name' => true,
        'head_id' => true,
        'place_holder' => true,
        'char_limit' => true,
        'required' => true,
        'textbox1' => true,
        'textbox2' => true,
        'slug' => true,
        'hidden' => true,
        'select_count' => true,
        'fix_form' => true,
        'created' => true,
        'modified' => true,
        'form_display_order' => true,
        'initial' => true,
        'public_display_order' => true,
    ];
}
