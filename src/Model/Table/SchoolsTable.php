<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Schools Model
 *
 * @property \App\Model\Table\EventsTable&\Cake\ORM\Association\HasMany $Events
 * @property \App\Model\Table\NewsTable&\Cake\ORM\Association\HasMany $News
 * @property \App\Model\Table\RecommendsTable&\Cake\ORM\Association\HasMany $Recommends
 *
 * @method \App\Model\Entity\School newEmptyEntity()
 * @method \App\Model\Entity\School newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\School[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\School get($primaryKey, $options = [])
 * @method \App\Model\Entity\School findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\School patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\School[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\School|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\School saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\School[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\School[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\School[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\School[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class SchoolsTable extends Table
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

        $this->setTable('schools');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('Events', [
            'foreignKey' => 'school_id',
        ]);
        $this->hasMany('News', [
            'foreignKey' => 'school_id',
        ]);
        $this->hasMany('Recommends', [
            'foreignKey' => 'school_id',
        ]);
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
            ->scalar('school_name')
            ->maxLength('school_name', 255)
            ->requirePresence('school_name', 'create')
            ->notEmptyString('school_name');

        $validator
            ->scalar('school_url')
            ->allowEmptyString('school_url');

        $validator
            ->scalar('school_tag_name')
            ->maxLength('school_tag_name', 255)
            ->requirePresence('school_tag_name', 'create')
            ->notEmptyString('school_tag_name')
            ->add('school_tag_name', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->integer('order_no')
            ->notEmptyString('order_no');

        $validator
            ->boolean('is_active')
            ->notEmptyString('is_active');

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
        $rules->add($rules->isUnique(['school_tag_name']), ['errorField' => 'school_tag_name']);

        return $rules;
    }
}
