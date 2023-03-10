<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\ORM\TableRegistry;
use Exception;
use Throwable;

/**
 * VoiceParts Model
 *
 * @method \App\Model\Entity\VoicePart newEmptyEntity()
 * @method \App\Model\Entity\VoicePart newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\VoicePart[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\VoicePart get($primaryKey, $options = [])
 * @method \App\Model\Entity\VoicePart findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\VoicePart patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\VoicePart[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\VoicePart|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\VoicePart saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\VoicePart[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\VoicePart[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\VoicePart[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\VoicePart[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class VoicePartsTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('voice_parts');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('VoicePartOptions', ['dependent' => true])
        ->setForeignKey('part_id');

        $this->belongsToMany('VoiceForms', ['dependent' => false])
        ->setForeignKey('form_id');

        $this->hasMany('VoiceUserFormDataOptions', ['dependent' => false])
        ->setForeignKey('part_id');
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->scalar('username')
            ->maxLength('username', 100, __d('validation', 'USERNAME_SHOULD_BE_N_CHARACTERS_OR_LESS', 100))
            ->notEmptyString('username', __d('validation', 'EMPTY_USERID'))
            ->add('username', 'custom', [
                'rule' => array('custom', '/^[a-zA-Z0-9-_.\@]+$/'),
                'message' => __d('validation', 'USERID_ONLY_USING_HALF_WIDTH_ALPHANUMERIC')
            ])
            ->add('username', 'unique', ['rule' => 'validateUnique', 'provider' => 'table', 'message' => __d('validation', 'USERID_ALREADY_IN_USE')]);

        $validator
            ->scalar('password')
            ->maxLength('password', 100, __d('validation', 'PASSWORD_MUST_BE_N_CHARACTERS_OR_LESS', 100))
            ->add('password', 'custom', [
                'rule' => array('custom', '/^[a-zA-Z0-9-_.\@]+$/'),
                'message' => __d('validation', 'PASSWORD_ONLY_USING_HALF_WIDTH_ALPHANUMERIC')
            ])
            ->notEmptyString('password', __d('validation', 'EMPTY_PASSWORD'));

        // add for error messages
        $validator->scalar('form_display_order')
            ->integer('form_display_order');

        $validator->scalar('public_display_order')
            ->integer('public_display_order');

        return $validator;
    }


    /**
     * Edit voice part in module .
     *
     * @param Array $data.
     * @return Array: Int error, String message, Array redirect
     */
    public function saveVoicePart($data) {
        $redirect = [
            'controller' => 'SuccessfulCandidates','action' => 'editForm', '?' => [
                'formId' => $data['form_id']
            ]
        ];

        try {
            $validator = $this->getValidator('default');
            $errors = $validator->validate($data);
            if (empty($errors)) {

                if (empty($data['id'])) {
                    $voicePartEntity = $this->newEmptyEntity();
                } else {
                    $voicePartEntity = $this->get($data['id']);
                }

                $voicePart = $this->patchEntity($voicePartEntity, $data);

                if ($data['slug'] == JYUKENTIKU1) {
                    $jyukentiku = TableRegistry::getTableLocator()->get('VoiceJyukentikuLists');
                    if ($jyukentiku->shapeJyukentikuData($data)) {
                        throw new Exception(__d('successful_candidate', 'REGISTER_COMPLETED'));
                    }

                } else {
                    $queryVoicePart = $this->save($voicePart);
                    if (!$queryVoicePart) {
                        throw new Exception(__d('successful_candidate', 'CONTENT_INCOMPLETE'), 1);
                    }
                    $voicePartOption = TableRegistry::getTableLocator()->get('VoicePartOptions');
                    $voicePartOption->saveVoicePartOption($data, $queryVoicePart->id);

                    throw new Exception(__d('successful_candidate', 'REGISTER_COMPLETED'));
                }
            } else {
                throw new Exception(__d('successful_candidate', 'CONTENT_INCOMPLETE'), 1);
            }
        } catch (Throwable $th) {
            return [
                'error' => $th->getCode(),
                'message' => $th->getMessage(),
                'redirect' => $redirect ?? []
            ];
        }
    }

    public function getDetailByFormID($formId, $slug) {
        return $this->find()
            ->where([
                'slug' => $slug,
                'form_id' => $formId
            ])
            ->first();
    }
}
