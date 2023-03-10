<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * CriteoCourse Model
 *
 * @method \App\Model\Entity\CriteoCourse newEmptyEntity()
 * @method \App\Model\Entity\CriteoCourse newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\CriteoCourse[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\CriteoCourse get($primaryKey, $options = [])
 * @method \App\Model\Entity\CriteoCourse findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\CriteoCourse patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\CriteoCourse[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\CriteoCourse|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CriteoCourse saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CriteoCourse[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\CriteoCourse[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\CriteoCourse[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\CriteoCourse[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class CriteoCourseTable extends Table
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

        $this->setTable('criteo_master');
    }
}
