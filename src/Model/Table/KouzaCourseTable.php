<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * KouzaCourse Model
 *
 * @method \App\Model\Entity\KouzaCourse newEmptyEntity()
 * @method \App\Model\Entity\KouzaCourse newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\KouzaCourse[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\KouzaCourse get($primaryKey, $options = [])
 * @method \App\Model\Entity\KouzaCourse findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\KouzaCourse patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\KouzaCourse[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\KouzaCourse|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\KouzaCourse saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\KouzaCourse[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\KouzaCourse[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\KouzaCourse[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\KouzaCourse[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class KouzaCourseTable extends Table
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

        $this->setTable('kouza_course');

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
            ->notEmptyString('id');

        $validator
            ->integer('kbn_cd')
            ->requirePresence('kbn_cd', 'create')
            ->notEmptyString('kbn_cd');

        $validator
            ->scalar('kbn_name')
            ->maxLength('kbn_name', 60)
            ->allowEmptyString('kbn_name');

        $validator
            ->scalar('kouza_cd')
            ->requirePresence('kouza_cd', 'create')
            ->notEmptyString('kouza_cd');

        $validator
            ->scalar('kouza_name')
            ->maxLength('kouza_name', 60)
            ->allowEmptyString('kouza_name');

        $validator
            ->scalar('course_cd')
            ->requirePresence('course_cd', 'create')
            ->notEmptyString('course_cd');

        $validator
            ->scalar('course_name')
            ->maxLength('course_name', 60)
            ->allowEmptyString('course_name');

        $validator
            ->scalar('note')
            ->allowEmptyString('note');

        $validator
            ->integer('brand_id')
            ->allowEmptyString('brand_id');

        $validator
            ->boolean('delete_flag')
            ->allowEmptyString('delete_flag');

        $validator
            ->scalar('sort_cd')
            ->maxLength('sort_cd', 6)
            ->notEmptyString('sort_cd');

        return $validator;
    }
}
