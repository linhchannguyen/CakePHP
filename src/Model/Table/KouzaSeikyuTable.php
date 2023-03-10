<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * KouzaSeikyu Model
 *
 * @method \App\Model\Entity\KouzaSeikyu newEmptyEntity()
 * @method \App\Model\Entity\KouzaSeikyu newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\KouzaSeikyu[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\KouzaSeikyu get($primaryKey, $options = [])
 * @method \App\Model\Entity\KouzaSeikyu findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\KouzaSeikyu patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\KouzaSeikyu[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\KouzaSeikyu|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\KouzaSeikyu saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\KouzaSeikyu[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\KouzaSeikyu[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\KouzaSeikyu[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\KouzaSeikyu[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class KouzaSeikyuTable extends Table
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

        $this->setTable('kouza_seikyu');

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
            ->integer('id')
            ->requirePresence('id', 'create')
            ->notEmptyString('id')
            ->add('id', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->scalar('kouza_name')
            ->maxLength('kouza_name', 60)
            ->requirePresence('kouza_name', 'create')
            ->notEmptyString('kouza_name')
            ->add('kouza_name', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->scalar('selectbox_message')
            ->allowEmptyString('selectbox_message');

        $validator
            ->boolean('delete_flag')
            ->allowEmptyString('delete_flag');

        $validator
            ->scalar('sort_cd')
            ->maxLength('sort_cd', 6)
            ->notEmptyString('sort_cd');

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
        $rules->add($rules->isUnique(['id']), ['errorField' => 'id']);
        $rules->add($rules->isUnique(['kouza_name']), ['errorField' => 'kouza_name']);

        return $rules;
    }
}
