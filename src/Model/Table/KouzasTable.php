<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Kouzas Model
 *
 * @property \App\Model\Table\ActiveEventTypesTable&\Cake\ORM\Association\HasMany $ActiveEventTypes
 * @property \App\Model\Table\EventsTable&\Cake\ORM\Association\HasMany $Events
 * @property \App\Model\Table\RecommendsTable&\Cake\ORM\Association\HasMany $Recommends
 *
 * @method \App\Model\Entity\Kouza newEmptyEntity()
 * @method \App\Model\Entity\Kouza newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Kouza[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Kouza get($primaryKey, $options = [])
 * @method \App\Model\Entity\Kouza findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Kouza patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Kouza[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Kouza|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Kouza saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Kouza[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Kouza[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Kouza[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Kouza[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class KouzasTable extends Table
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

        $this->setTable('kouzas');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('ActiveEventTypes', [
            'foreignKey' => 'kouza_id',
        ]);
        $this->hasMany('Events', [
            'foreignKey' => 'kouza_id',
        ]);
        $this->hasMany('Recommends', [
            'foreignKey' => 'kouza_id',
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
            ->scalar('kouza_name')
            ->maxLength('kouza_name', 255)
            ->requirePresence('kouza_name', 'create')
            ->notEmptyString('kouza_name');

        $validator
            ->scalar('kouza_url')
            ->requirePresence('kouza_url', 'create')
            ->notEmptyString('kouza_url');

        $validator
            ->integer('order_no')
            ->notEmptyString('order_no');

        $validator
            ->boolean('is_active')
            ->notEmptyString('is_active');

        return $validator;
    }
}
