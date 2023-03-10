<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\ORM\TableRegistry;

/**
 * VoicePartOptions Model
 *
 * @method \App\Model\Entity\VoicePartOption newEmptyEntity()
 * @method \App\Model\Entity\VoicePartOption newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\VoicePartOption[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\VoicePartOption get($primaryKey, $options = [])
 * @method \App\Model\Entity\VoicePartOption findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\VoicePartOption patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\VoicePartOption[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\VoicePartOption|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\VoicePartOption saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\VoicePartOption[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\VoicePartOption[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\VoicePartOption[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\VoicePartOption[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class VoicePartOptionsTable extends Table
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

        $this->setTable('voice_part_options');
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

    /**
     * Default validation rules.
     *
     * @param Array data
     * @return void
     */
    public function saveVoicePartOption($data, $partId = null) {
        if (!empty($data['VoicePartOption']['value'])) {
            if (!empty($data['id'])) {
                $partId = $data['id'];

                // Delete all at once
                $this->deleteAll(array('part_id' => $partId));
            }

            foreach ($data['VoicePartOption']['value'] as $key => $value) {
                if (!empty($value)) {
                    $entity = $this->newEntity([
                        'id' => null,
                        'part_id' => $partId,
                        'value' => $key + 1,
                        'name' => $value,
                    ]);
                    $this->save($entity);
                }
            }

        }
    }
}
