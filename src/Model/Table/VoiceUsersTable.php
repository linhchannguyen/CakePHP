<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * VoiceUsers Model
 *
 * @method \App\Model\Entity\VoiceUser newEmptyEntity()
 * @method \App\Model\Entity\VoiceUser newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\VoiceUser[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\VoiceUser get($primaryKey, $options = [])
 * @method \App\Model\Entity\VoiceUser findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\VoiceUser patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\VoiceUser[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\VoiceUser|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\VoiceUser saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\VoiceUser[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\VoiceUser[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\VoiceUser[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\VoiceUser[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class VoiceUsersTable extends Table
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

        $this->setTable('voice_users');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
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
            ->setStopOnFailure()
            ->notBlank('username', __d('validation', 'EMPTY_USERID'))
            ->add('username', 'custom', [
                'rule' => array('custom', '/^[a-zA-Z0-9-_.\@]+$/'),
                'message' => __d('validation', 'USERID_ONLY_USING_HALF_WIDTH_ALPHANUMERIC')
            ])
            ->add('username', [
                'unique' => ['rule' => 'validateUnique', 'provider' => 'table', 'message' => __d('validation', 'USERID_ALREADY_IN_USE')]
            ])
            ->maxLength('username', 100, __d('validation', 'USERNAME_MUST_BE_N_CHARACTERS_OR_LESS', 100));

        $validator
            ->setStopOnFailure()
            ->add('password', 'custom', [
                'rule' => array('custom', '/^[a-zA-Z0-9-_.\@]+$/'),
                'message' => __d('validation', 'PASSWORD_ONLY_USING_HALF_WIDTH_ALPHANUMERIC')
            ])
            ->notBlank('password', __d('validation', 'EMPTY_PASSWORD'))
            ->maxLength('password', 100, __d('validation', 'PASSWORD_MUST_BE_N_CHARACTERS_OR_LESS', 100));

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->isUnique(['username'], __d('validation', 'USERID_ALREADY_IN_USE')), ['errorField' => 'username']);

        return $rules;
    }
}
