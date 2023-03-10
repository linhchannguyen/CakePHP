<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\ORM\TableRegistry;

/**
 * VoiceJyukentikuLists Model
 *
 * @method \App\Model\Entity\VoiceJyukentikuList newEmptyEntity()
 * @method \App\Model\Entity\VoiceJyukentikuList newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\VoiceJyukentikuList[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\VoiceJyukentikuList get($primaryKey, $options = [])
 * @method \App\Model\Entity\VoiceJyukentikuList findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\VoiceJyukentikuList patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\VoiceJyukentikuList[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\VoiceJyukentikuList|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\VoiceJyukentikuList saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\VoiceJyukentikuList[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\VoiceJyukentikuList[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\VoiceJyukentikuList[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\VoiceJyukentikuList[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class VoiceJyukentikuListsTable extends Table
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

        $this->setTable('voice_jyukentiku_lists');
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
     * Edit module process Voice Jyukentiku
     *
     * @param Array data
     * @return Boolean
    */
    public function shapeJyukentikuData($data) {
        $rawData = $data;
        for ($i = 1; $i <= 3; $i++) {
            $oldJyukentikuData = array();
            $oldJyukentikuTextData = array();

            // 受験地区
            $data['slug'] = 'JYUKENTIKU' . $i;
            if ($i == 1) {
                $data['title_name'] = $rawData['title_name'];
            } else {
                $data['title_name'] = $rawData['title_name'].$i;
            }

            $voicePart = TableRegistry::getTableLocator()->get('VoiceParts');

            $oldJyukentikuData = $voicePart->getDetailByFormID($rawData['form_id'], $data['slug']);
            if ($oldJyukentikuData) {
                $data['id'] = $oldJyukentikuData['id'];
            }

            // update or create a new record if id is not exists
            if (!empty($data['id'])) {
                $entity = $voicePart->get($data['id']);
            } else {
                $entity = $voicePart->newEmptyEntity();
            }
            $entity = $voicePart->patchEntity($entity, $data);
            $queryVoicePart = $voicePart->save($entity);

            $voicePartOption = TableRegistry::getTableLocator()->get('VoicePartOptions');
            $voicePartOption->saveVoicePartOption($data, $queryVoicePart->id);

            //先生の名前
            $data['slug'] = 'JYUKENTIKU_TEXT' . $i;
            $data['title_name'] = $rawData['title_name']. ' コメント' .$i;
            $oldJyukentikuTextData = $voicePart->getDetailByFormID($rawData['form_id'], $data['slug']);
            if ($oldJyukentikuTextData) {
                $data['id'] = $oldJyukentikuTextData['id'];
            }

            if (!empty($data['id'])) {
                $entity = $voicePart->get($data['id']);
            } else {
                $entity = $voicePart->newEmptyEntity();
            }
            $entity = $voicePart->patchEntity($entity, $data);
            $voicePart->save($entity);

        }
        return true;
    }
}
