<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Criteo Model
 *
 * @method \App\Model\Entity\Criteo newEmptyEntity()
 * @method \App\Model\Entity\Criteo newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Criteo[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Criteo get($primaryKey, $options = [])
 * @method \App\Model\Entity\Criteo findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Criteo patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Criteo[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Criteo|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Criteo saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Criteo[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Criteo[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Criteo[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Criteo[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class CriteoTable extends Table
{
    const OTHERS = '99999';
    // private $data = [];
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('criteo_feed');
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
            ->integer('no');

        $validator
            ->scalar('courseid')
            ->add('courseid', 'custom', [
                'rule' => array('custom', '/^(?!.*9999).+$/'),
                'message' => __d('validation', 'CRITEO_TABLE_VALID_COURSE_ID_CUSTOM')
            ]);

        $validator
            ->scalar('id')
            ->maxLength('id', 50, __d('validation', 'CRITEO_TABLE_VALID_ID_MAXLENGTH'))
            ->requirePresence('id', 'create')
            ->notEmptyString('id', __d('validation', 'CRITEO_TABLE_VALID_ID_REQUIRED'))
            ->add('id', 'custom', [
                'rule' => array('custom', "/^[0-9]+$/"),
                'message' => __d('validation', 'CRITEO_TABLE_VALID_ID_CUSTOM')
            ]);;

        $validator
            ->scalar('name')
            ->maxLength('name', 500, __d('validation', 'CRITEO_TABLE_VALID_NAME_MAXLENGTH'))
            ->allowEmptyString('name');

        $validator
            ->scalar('url')
            ->maxLength('url', 1024, __d('validation', 'CRITEO_TABLE_VALID_URL_MAXLENGTH'))
            ->requirePresence('url', 'create')
            ->notEmptyString('url', __d('validation', 'CRITEO_TABLE_VALID_URL_REQUIRED'))
            ->add('url', 'valid-url', ['rule' => 'url', 'message' => __d('validation', 'CRITEO_TABLE_VALID_URL')])
            ->add('url', 'unique', ['rule' => 'checkDuplicateUrl', 'provider' => 'table', 'message' => __d('validation', 'CRITEO_TABLE_VALID_URL_UNIQUE')]);

        $validator
            ->scalar('bigimage')
            ->maxLength('bigimage', 1024, __d('validation', 'CRITEO_TABLE_VALID_BIGIMAGE_MAXLENGTH'))
            ->allowEmptyString('bigimage')
            ->add('bigimage', 'custom', [
                'rule' => array('custom', '/^[ -\~]+$/'),
                'message' => __d('validation', 'CRITEO_TABLE_VALID_BIGIMAGE_CUSTOM')
            ]);

        $validator
            ->scalar('description')
            ->maxLength('description', 500, __d('validation', 'CRITEO_TABLE_VALID_DESCRIPTION_MAXLENGTH'))
            ->allowEmptyString('description');

        $validator
            ->scalar('price')
            ->allowEmptyString('price')
            ->add('price', 'custom', [
                'rule' => array('custom', '/^[0-9]+$/'),
                'message' => __d('validation', 'CRITEO_TABLE_VALID_PRICE_CUSTOM')
            ]);

        $validator
            ->scalar('retailprice')
            ->maxLength('retailprice', 128)
            ->allowEmptyString('retailprice');

        $validator
            ->scalar('recommendable')
            ->maxLength('recommendable', 1)
            ->allowEmptyString('recommendable');

        $validator
            ->scalar('cooperation_flag')
            ->maxLength('cooperation_flag', 1)
            ->allowEmptyString('cooperation_flag');

        $validator
            ->scalar('page_type')
            ->maxLength('page_type', 1)
            ->requirePresence('page_type', 'create');

        $validator
            ->scalar('rtime')
            ->date('rtime')
            ->allowEmptyDate('rtime');

        $validator
            ->scalar('mtime')
            ->date('mtime')
            ->allowEmptyDate('mtime');

        $validator
            ->scalar('extra_atp')
            ->maxLength('extra_atp', 200, __d('validation', 'CRITEO_TABLE_VALID_EXTRA_ATP_MAXLENGTH'))
            ->allowEmptyString('extra_atp');

        return $validator;
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->isUnique(['url']), ['errorField' => 'url']);
        return $rules;
    }

    /**
     * URL duplication check
     */
    public function checkDuplicateUrl($check, $data)
    {
        $id = $data['data']['id'];
        $url = $check;
        $count = $this->find()->where(['id <>' => $id, 'url' => $url])->count();
        return $count == 0;
    }

    public function validateMany(&$data)
    {
        $validationErrors = array();
        foreach ($data as $key => $values) {
            $defaultValidator = $this->getValidator('default');
            $validate = $defaultValidator->validate($values);
            if ($validate) {
                foreach ($validate as $field => $msg) {
                    $msg_ = "";
                    foreach ($msg as $message) {
                        $msg_ = $message;
                        break;
                    }
                    $validationErrors[$key][$field]['message'] = $msg_;
                }
            }
        }
        if (!empty($validationErrors)) {
            return $validationErrors;
        } else {
            return [];
        }
    }
}
