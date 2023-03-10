<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * VoiceSubjectTypes Model
 *
 * @method \App\Model\Entity\VoiceSubjectType newEmptyEntity()
 * @method \App\Model\Entity\VoiceSubjectType newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\VoiceSubjectType[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\VoiceSubjectType get($primaryKey, $options = [])
 * @method \App\Model\Entity\VoiceSubjectType findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\VoiceSubjectType patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\VoiceSubjectType[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\VoiceSubjectType|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\VoiceSubjectType saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\VoiceSubjectType[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\VoiceSubjectType[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\VoiceSubjectType[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\VoiceSubjectType[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class VoiceSubjectTypesTable extends Table
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

        $this->setTable('voice_subject_types');
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
