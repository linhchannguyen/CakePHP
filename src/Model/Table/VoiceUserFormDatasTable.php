<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * VoiceUserFormDatas Model
 *
 * @method \App\Model\Entity\VoiceUserFormData newEmptyEntity()
 * @method \App\Model\Entity\VoiceUserFormData newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\VoiceUserFormData[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\VoiceUserFormData get($primaryKey, $options = [])
 * @method \App\Model\Entity\VoiceUserFormData findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\VoiceUserFormData patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\VoiceUserFormData[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\VoiceUserFormData|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\VoiceUserFormData saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\VoiceUserFormData[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\VoiceUserFormData[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\VoiceUserFormData[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\VoiceUserFormData[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class VoiceUserFormDatasTable extends Table
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

        $this->setTable('voice_user_form_datas');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany(
            'VoiceUserFormDataOptions', [
                'dependent' => true
            ],
        )
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
