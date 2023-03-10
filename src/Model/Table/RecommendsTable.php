<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use App\Model\Table\BaseTable;
use App\Repositories\Schools\SchoolRepository;
use App\Repositories\Kouzas\KouzaRepository;
use App\Traits\dateMiscTrait;

/**
 * Recommends Model
 *
 * @property \App\Model\Table\SchoolsTable&\Cake\ORM\Association\BelongsTo $Schools
 * @property \App\Model\Table\KouzasTable&\Cake\ORM\Association\BelongsTo $Kouzas
 *
 * @method \App\Model\Entity\Recommend newEmptyEntity()
 * @method \App\Model\Entity\Recommend newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Recommend[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Recommend get($primaryKey, $options = [])
 * @method \App\Model\Entity\Recommend findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Recommend patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Recommend[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Recommend|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Recommend saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Recommend[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Recommend[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Recommend[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Recommend[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class RecommendsTable extends BaseTable
{
    use dateMiscTrait;
    protected $_schools;
    protected $_kouzas;

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('recommends');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Schools', [
            'foreignKey' => 'school_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Kouzas', [
            'foreignKey' => 'kouza_id',
            'joinType' => 'INNER',
        ]);
    }

    public function validationCustom(Validator $validator): Validator
    {
        $validator
            ->allowEmptyString('image_url1')
            ->maxLength('image_url1', RECOMMENDS_MAX_URL_LEN, "【URL】は" . RECOMMENDS_MAX_URL_LEN . "文字以内でご入力ください。");
        $validator
            ->allowEmptyString('image_url2')
            ->maxLength('image_url2', RECOMMENDS_MAX_URL_LEN, "【URL】は" . RECOMMENDS_MAX_URL_LEN . "文字以内でご入力ください。");
        $validator
            ->allowEmptyString('image_url3')
            ->maxLength('image_url3', RECOMMENDS_MAX_URL_LEN, "【URL】は" . RECOMMENDS_MAX_URL_LEN . "文字以内でご入力ください。");
        $validator
            ->allowEmptyString('sub_title1')
            ->maxLength('sub_title1', 120, "【サブタイトル】は" . 120 . "文字以内でご入力ください。");
        $validator
            ->allowEmptyString('sub_title2')
            ->maxLength('sub_title2', 120, "【サブタイトル】は" . 120 . "文字以内でご入力ください。");
        $validator
            ->allowEmptyString('sub_title3')
            ->maxLength('sub_title3', 120, "【サブタイトル】は" . 120 . "文字以内でご入力ください。");
        $validator
            ->allowEmptyString('sub_title4')
            ->maxLength('sub_title4', 120, "【サブタイトル】は" . 120 . "文字以内でご入力ください。");
        $validator
            ->allowEmptyString('sub_url1')
            ->maxLength('sub_url1', RECOMMENDS_MAX_URL_LEN, "【URL】は" . RECOMMENDS_MAX_URL_LEN . "文字以内でご入力ください。");
        $validator
            ->allowEmptyString('sub_url2')
            ->maxLength('sub_url2', RECOMMENDS_MAX_URL_LEN, "【URL】は" . RECOMMENDS_MAX_URL_LEN . "文字以内でご入力ください。");
        $validator
            ->allowEmptyString('sub_url3')
            ->maxLength('sub_url3', RECOMMENDS_MAX_URL_LEN, "【URL】は" . RECOMMENDS_MAX_URL_LEN . "文字以内でご入力ください。");
        $validator
            ->allowEmptyString('sub_url4')
            ->maxLength('sub_url4', RECOMMENDS_MAX_URL_LEN, "【URL】は" . RECOMMENDS_MAX_URL_LEN . "文字以内でご入力ください。");
        $validator
            ->add('school', 'inList', [
                'rule' => ['inList', $this->getSchoolInfoForSelectbox()],
                'message' => __d('validation', 'RECOMMENDS_SELECT_SCHOOL_REQUIRED')
            ]);
        $validator
            ->add('kouza', 'inList', [
                'rule' => ['inList', $this->getKouzaInfoForSelectbox()],
                'message' => __d('validation', 'RECOMMENDS_SELECT_COURSE_REQUIRED')
            ]);
        $validator
            ->notEmptyString('text_title', __d('validation', 'RECOMMENDS_TITLE_REQUIRED'))
            ->maxLength('text_title', RECOMMENDS_MAX_TITLE_LEN, "【タイトル】は" . RECOMMENDS_MAX_TITLE_LEN . "文字以内でご入力ください。");
        $validator
            ->maxLength('text_title_sub', RECOMMENDS_MAX_TITLE_SUB_LEN, "【サブタイトル】は" . RECOMMENDS_MAX_TITLE_SUB_LEN . "文字以内でご入力ください。");
        $validator
            ->allowEmptyString('text_link_url')
            ->maxLength('text_link_url', RECOMMENDS_MAX_URL_LEN, "【URL】は" . RECOMMENDS_MAX_URL_LEN . "文字以内でご入力ください。");
        $validator
            ->notEmptyString('radio_link', __d('validation', 'RECOMMENDS_LINK_TYPE_REQUIRED'))
            ->add('radio_link', 'inList', [
                'rule' => ['inList', [0, 1]],
                'message' => __d('validation', 'RECOMMENDS_LINK_TYPE_REQUIRED')
            ]);
        $validator
            ->notEmptyString('text_order_no', __d('validation', 'RECOMMENDS_ENTER_SORT_ORDER'))
            ->add('text_order_no', 'inListCustom', ['rule' => 'inListCustom', 'provider' => 'table', 'message' => "【並び順補正値】は" . MIN_ORDER_NO . "以上" . MAX_ORDER_NO . "以下の整数で指定してください。"]);
        $validator
            ->notEmptyString('radio_is_active', __d('validation', 'RECOMMENDS_IS_ACTIVE_REQUIRED'))
            ->add('radio_is_active', 'inList', [
                'rule' => ['inList', [0, 1]],
                'message' => __d('validation', 'RECOMMENDS_IS_ACTIVE_REQUIRED')
            ]);
        $validator
            ->add('enabled_from', 'validateEnabledFrom', ['rule' => 'validateEnabledFrom', 'provider' => 'table', 'message' => __d('validation', 'RECOMMENDS_DATETIME_INVALID')]);
        $validator
            ->add('enabled_to', 'validateEnabledTo', ['rule' => 'validateEnabledTo', 'provider' => 'table', 'message' => __d('validation', 'RECOMMENDS_DATETIME_INVALID')]);

        return $validator;
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $this->_setData();
        $validator
            ->requirePresence('school_id')
            // ->notEmptyString('school_id', '【校舎ID】が入力されていません。')
            ->notEmptyString('school_id', __d('csv_form', 'VALIDATE_RECOMMEND_SCHOOL_EMPTY'))
            ->inList('school_id', $this->_schools, __d('csv_form', 'VALIDATE_RECOMMEND_SCHOOL_INVALID'));
        $validator
            ->requirePresence('kouza_id')
            ->notEmptyString('kouza_id', __d('csv_form', 'VALIDATE_RECOMMEND_KOUZA_EMPTY'))
            ->inList('kouza_id', $this->_kouzas, __d('csv_form', 'VALIDATE_RECOMMEND_KOUZA_INVALID'));
        $validator
            ->requirePresence('recommend_title')
            ->notEmptyString('recommend_title', __d('csv_form', 'VALIDATE_RECOMMEND_TITLE_EMPTY'))
            ->maxLength('recommend_title', RECOMMENDS_MAX_TITLE_LEN, __d('csv_form', 'VALIDATE_RECOMMEND_TITLE_MAX_LENGTH', [RECOMMENDS_MAX_TITLE_LEN]));
        $validator
            ->allowEmptyString('recommend_title_sub')
            ->maxLength('recommend_title_sub', RECOMMENDS_MAX_TITLE_SUB_LEN, __d('csv_form', 'VALIDATE_RECOMMEND_TITLE_SUB_MAX_LENGTH', [RECOMMENDS_MAX_TITLE_SUB_LEN]));
        $validator
            ->allowEmptyString('recommend_url')
            ->maxLength('recommend_url', RECOMMENDS_MAX_URL_LEN, __d('csv_form', 'VALIDATE_RECOMMEND_URL_MAX_LENGTH', [RECOMMENDS_MAX_URL_LEN]))
            ->url('recommend_url', __d('csv_form', 'VALIDATE_RECOMMEND_URL_FORMAT'));
        $validator
            ->allowEmptyString('image_url1')
            ->maxLength('image_url1', RECOMMENDS_MAX_URL_LEN, __d('csv_form', 'VALIDATE_RECOMMEND_IMAGE_URL_MAX_LENGTH', [1, RECOMMENDS_MAX_URL_LEN]));
        $validator
            ->allowEmptyString('image_url2')
            ->maxLength('image_url2', RECOMMENDS_MAX_URL_LEN,  __d('csv_form', 'VALIDATE_RECOMMEND_IMAGE_URL_MAX_LENGTH', [2, RECOMMENDS_MAX_URL_LEN]));
        $validator
            ->allowEmptyString('image_url3')
            ->maxLength('image_url3', RECOMMENDS_MAX_URL_LEN,  __d('csv_form', 'VALIDATE_RECOMMEND_IMAGE_URL_MAX_LENGTH', [3, RECOMMENDS_MAX_URL_LEN]));
        $validator
            ->allowEmptyString('sub_title1')
            ->maxLength('sub_title1', 120, __d('csv_form', 'VALIDATE_RECOMMEND_SUB_TITLE_MAX_LENGTH', [1, 120]));
        $validator
            ->allowEmptyString('sub_title2')
            ->maxLength('sub_title2', 120, __d('csv_form', 'VALIDATE_RECOMMEND_SUB_TITLE_MAX_LENGTH', [2, 120]));
        $validator
            ->allowEmptyString('sub_title3')
            ->maxLength('sub_title3', 120, __d('csv_form', 'VALIDATE_RECOMMEND_SUB_TITLE_MAX_LENGTH', [3, 120]));
        $validator
            ->allowEmptyString('sub_title4')
            ->maxLength('sub_title4', 120, __d('csv_form', 'VALIDATE_RECOMMEND_SUB_TITLE_MAX_LENGTH', [4, 120]));
        $validator
            ->allowEmptyString('sub_url1')
            ->maxLength('sub_url1', RECOMMENDS_MAX_URL_LEN, __d('csv_form', 'VALIDATE_RECOMMEND_SUB_URL_MAX_LENGTH', [1, RECOMMENDS_MAX_URL_LEN]));
        $validator
            ->allowEmptyString('sub_url2')
            ->maxLength('sub_url2', RECOMMENDS_MAX_URL_LEN, __d('csv_form', 'VALIDATE_RECOMMEND_SUB_URL_MAX_LENGTH', [2, RECOMMENDS_MAX_URL_LEN]));
        $validator
            ->allowEmptyString('sub_url3')
            ->maxLength('sub_url3', RECOMMENDS_MAX_URL_LEN, __d('csv_form', 'VALIDATE_RECOMMEND_SUB_URL_MAX_LENGTH', [3, RECOMMENDS_MAX_URL_LEN]));
        $validator
            ->allowEmptyString('sub_url4')
            ->maxLength('sub_url4', RECOMMENDS_MAX_URL_LEN, __d('csv_form', 'VALIDATE_RECOMMEND_SUB_URL_MAX_LENGTH', [4, RECOMMENDS_MAX_URL_LEN]));
        $validator
            ->allowEmptyString('enabled_from')
            ->add('enabled_from', 'dateTime', [
                'rule' => 'validateDateTime',
                'provider' => 'table',
                'message' =>  __d('csv_form', 'VALIDATE_RECOMMEND_ENABLE_FROM_DATETIME_FORMAT')
            ]);
        $validator
            ->allowEmptyString('enabled_to')
            ->add('enabled_to', 'dateTime', [
                'rule' => 'validateDateTime',
                'provider' => 'table',
                'message' =>  __d('csv_form', 'VALIDATE_RECOMMEND_ENABLE_TO_DATETIME_FORMAT')
            ]);
        $validator
            ->requirePresence('order_no')
            ->notEmptyString('order_no',  __d('csv_form', 'VALIDATE_RECOMMEND_ORDER_NO_EMPTY'))
            ->range('order_no', [MIN_ORDER_NO, MAX_ORDER_NO],  __d('csv_form', 'VALIDATE_RECOMMEND_ORDER_NO_MIN_MAX_LENGHT', [MIN_ORDER_NO, MAX_ORDER_NO]));
        $validator
            ->requirePresence('is_active')
            ->notEmptyString('is_active',  __d('csv_form', 'VALIDATE_RECOMMEND_ACTIVE_EMPTY'))
            ->range('is_active', [0, 1],  __d('csv_form', 'VALIDATE_RECOMMEND_ACTIVE_RANGE'));
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
        $rules->add($rules->existsIn('school_id', 'Schools'), ['errorField' => 'school_id']);
        $rules->add($rules->existsIn('kouza_id', 'Kouzas'), ['errorField' => 'kouza_id']);

        return $rules;
    }

    /**
     * Make data to validate
     */
    protected function _setData()
    {
        $schools_data = (new SchoolRepository($this->Schools))->getKeyValuePairsWithCondition(['id', 'school_name'], ['is_active' => true]);
        $kouzas_data = (new KouzaRepository($this->Kouzas))->getKeyValuePairsWithCondition(['id', 'kouza_name'], ['is_active' => true]);
        $this->_schools = !empty($schools_data) ? array_keys($schools_data) : [];
        $this->_kouzas = !empty($kouzas_data) ? array_keys($kouzas_data) : [];
    }

    public function getSchoolInfoForSelectbox()
    {
        return $this->Schools->find('list')->toArray();
    }

    public function getKouzaInfoForSelectbox()
    {
        return $this->Kouzas->find('list')->toArray();
    }

    function inListCustom($check)
    {
        return MIN_ORDER_NO <= $check && $check <= MAX_ORDER_NO;
    }
    
    //------------------------------------------------
    /// @brief  Validate validity period (start)
    /// @param  $data           A reference to the data to inspect
    /// @param  $message        Error message
    /// @param  $array_in_model An array holding references to model objects
    /// @return An array of results and error messages
    ///         - $ary[0] : Result (true/false)
    ///         - $ary[1] : Error message
    /// @author ChanNL
    //------------------------------------------------
    function validateEnabledFrom($check, $data)
    {
        // If the value of year-month or day is 0, it means that it is not specified and is not processed.
        if ((0 == $data['data']['from_ym']) &&
            (0 == $data['data']['from_day']) &&
            ('_' == $data['data']['from_time'])
        ) {
            return true;
        }
        $daytime = sprintf(
            "%s-%02d %s:00",
            $data['data']['from_ym'],
            $data['data']['from_day'],
            $data['data']['from_time']
        );

        if ($this->isDateTime($daytime)) {
            return true;
        }
        return false;
    }

    //------------------------------------------------
    /// @brief  Check validity period (expiration) validity
    /// @param  $data           A reference to the data to inspect
    /// @param  $message        Error message
    /// @param  $array_in_model An array holding references to model objects
    /// @return An array of results and error messages
    ///         - $ary[0] : Result (true/false)
    ///         - $ary[1] : Error message
    /// @author ChanNL
    //------------------------------------------------
    function validateEnabledTo($check, $data)
    {
        // If the value of year-month or day is 0, it means that it is not specified and is not processed.
        if ((0 == $data['data']['to_ym']) &&
            (0 == $data['data']['to_day']) &&
            ('_' == $data['data']['to_time'])
        ) {
            return true;
        }
        $daytime = sprintf(
            "%s-%02d %s:00",
            $data['data']['to_ym'],
            $data['data']['to_day'],
            $data['data']['to_time']
        );

        if ($this->isDateTime($daytime)) {
            return true;
        }
        return false;
    }
}
