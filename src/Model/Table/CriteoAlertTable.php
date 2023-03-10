<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * CriteoAlert Model
 *
 * @method \App\Model\Entity\CriteoAlert newEmptyEntity()
 * @method \App\Model\Entity\CriteoAlert newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\CriteoAlert[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\CriteoAlert get($primaryKey, $options = [])
 * @method \App\Model\Entity\CriteoAlert findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\CriteoAlert patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\CriteoAlert[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\CriteoAlert|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CriteoAlert saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CriteoAlert[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\CriteoAlert[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\CriteoAlert[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\CriteoAlert[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class CriteoAlertTable extends Table
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

        $this->setTable('criteo_alert');
        $this->setDisplayField('name');
    }
}
