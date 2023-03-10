<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * VoiceZeirishiKamokuLists Model
 *
 * @method \App\Model\Entity\VoiceZeirishiKamokuList newEmptyEntity()
 * @method \App\Model\Entity\VoiceZeirishiKamokuList newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\VoiceZeirishiKamokuList[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\VoiceZeirishiKamokuList get($primaryKey, $options = [])
 * @method \App\Model\Entity\VoiceZeirishiKamokuList findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\VoiceZeirishiKamokuList patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\VoiceZeirishiKamokuList[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\VoiceZeirishiKamokuList|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\VoiceZeirishiKamokuList saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\VoiceZeirishiKamokuList[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\VoiceZeirishiKamokuList[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\VoiceZeirishiKamokuList[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\VoiceZeirishiKamokuList[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class VoiceZeirishiKamokuListsTable extends Table
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

        $this->setTable('voice_zeirishi_kamoku_lists');
        $this->setDisplayField('name');
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
        return $validator;
    }
}
