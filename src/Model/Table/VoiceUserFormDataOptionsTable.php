<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * VoiceUserFormDataOptions Model
 *
 * @method \App\Model\Entity\VoiceUserFormDataOption newEmptyEntity()
 * @method \App\Model\Entity\VoiceUserFormDataOption newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\VoiceUserFormDataOption[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\VoiceUserFormDataOption get($primaryKey, $options = [])
 * @method \App\Model\Entity\VoiceUserFormDataOption findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\VoiceUserFormDataOption patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\VoiceUserFormDataOption[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\VoiceUserFormDataOption|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\VoiceUserFormDataOption saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\VoiceUserFormDataOption[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\VoiceUserFormDataOption[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\VoiceUserFormDataOption[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\VoiceUserFormDataOption[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class VoiceUserFormDataOptionsTable extends Table
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

        $this->setTable('voice_user_form_data_options');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('VoiceParts', ['dependent' => true])
        ->setForeignKey('part_id');

        $this->belongsTo('VoiceUserFormDatas', ['dependent' => true])
        ->setForeignKey('user_form_data_id');
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        return $validator;
    }
}
