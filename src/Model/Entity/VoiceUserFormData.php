<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * VoiceUserFormData Entity
 *
 * @property int $id
 * @property int|null $form_id
 * @property string|null $tac_number
 * @property string|null $sei
 * @property string|null $mei
 * @property string|null $f_sei
 * @property string|null $f_mei
 * @property \Cake\I18n\FrozenDate|null $birthday
 * @property string|null $mail_address
 * @property int $release
 * @property string|null $photo
 * @property string|null $initial_name
 * @property int $fix
 * @property int $status
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property int|null $show_photo
 */
class VoiceUserFormData extends Entity
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
        'tac_number' => true,
        'sei' => true,
        'mei' => true,
        'f_sei' => true,
        'f_mei' => true,
        'birthday' => true,
        'mail_address' => true,
        'release' => true,
        'photo' => true,
        'initial_name' => true,
        'fix' => true,
        'status' => true,
        'created' => true,
        'modified' => true,
        'show_photo' => true,
    ];
}
