<?php

declare(strict_types=1);

namespace App\Model\Table;

use App\Model\Entity\School;
use App\Traits\dateMiscTrait;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use App\Model\Table\BaseTable;
use App\Repositories\Schools\SchoolRepository;

/**
 * News Model
 *
 * @property \App\Model\Table\SchoolsTable&\Cake\ORM\Association\BelongsTo $Schools
 *
 * @method \App\Model\Entity\News newEmptyEntity()
 * @method \App\Model\Entity\News newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\News[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\News get($primaryKey, $options = [])
 * @method \App\Model\Entity\News findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\News patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\News[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\News|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\News saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\News[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\News[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\News[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\News[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class NewsTable extends BaseTable
{
    protected $_schools;
    use dateMiscTrait;
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('news');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Schools', [
            'foreignKey' => 'school_id',
            'joinType' => 'INNER',
        ]);
    }
    public function validationCustom(Validator $validator): Validator
    {
        $validator
            ->add('school', 'inList', [
                'rule' => ['inList', $this->getSchoolInfoForSelectbox()],
                'message' => __d('validation', 'NEWS_SELECT_SCHOOL_REQUIRED')
            ]);

        $validator
            ->add('title_ym', 'notInList', ['rule' => 'notInList', 'provider' => 'table', 'message' => __d('validation', 'NEWS_SELECT_YM_TITLE')]);
        $validator
            ->add('title_day', 'notInList', ['rule' => 'notInList', 'provider' => 'table', 'message' => __d('validation', 'NEWS_SELECT_D_TITLE')]);
        $validator
            ->notEmptyString('text_title', __d('validation', 'NEWS_TITLE_REQUIRED'))
            ->maxLength('text_title', NEWS_MAX_TITLE_LEN, "【タイトル】は" . NEWS_MAX_TITLE_LEN . "文字以内でご入力ください。");
        $validator
            ->allowEmptyString('text_title_sub')
            ->maxLength('text_title_sub', NEWS_MAX_TITLE_SUB_LEN, "【サブタイトル】は" . NEWS_MAX_TITLE_SUB_LEN . "文字以内でご入力ください。");
        $validator
            ->allowEmptyString('text_link_url')
            ->maxLength('text_link_url', NEWS_MAX_URL_LEN, "【URL】は" . NEWS_MAX_URL_LEN . "文字以内でご入力ください。");
        $validator
            ->notEmptyString('radio_link', __d('validation', 'NEWS_LINK_TYPE_REQUIRED'))
            ->add('radio_link', 'inList', [
                'rule' => ['inList', [0, 1]],
                'message' => __d('validation', 'NEWS_LINK_TYPE_REQUIRED')
            ]);
        $validator
            ->notEmptyString('text_order_no', __d('validation', 'NEWS_ENTER_SORT_ORDER'))
            ->add('text_order_no', 'inListCustom', ['rule' => 'inListCustom', 'provider' => 'table', 'message' => "【並び順補正値】は" . MIN_ORDER_NO . "以上" . MAX_ORDER_NO . "以下の整数で指定してください。"]);
        $validator
            ->notEmptyString('radio_is_active', __d('validation', 'NEWS_IS_ACTIVE_REQUIRED'))
            ->add('radio_is_active', 'inList', [
                'rule' => ['inList', [0, 1]],
                'message' => __d('validation', 'NEWS_IS_ACTIVE_REQUIRED')
            ]);
        $validator
            ->add('news_date', 'validateEnabledFrom', ['rule' => 'validateNewsDate', 'provider' => 'table', 'message' => __d('validation', 'NEWS_DATE_INVALID')]);
        $validator
            ->add('enabled_from', 'validateEnabledFrom', ['rule' => 'validateEnabledFrom', 'provider' => 'table', 'message' => __d('validation', 'NEWS_DATETIME_INVALID')]);
        $validator
            ->add('enabled_to', 'validateEnabledTo', ['rule' => 'validateEnabledTo', 'provider' => 'table', 'message' => __d('validation', 'NEWS_DATETIME_INVALID')]);

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
            ->notEmptyString('school_id', __d('csv_form', 'VALIDATE_NEWS_SCHOOL_EMPTY'))
            ->inList('school_id', $this->_schools, __d('csv_form', 'VALIDATE_NEWS_SCHOOL_INVALID'));
        $validator
            ->requirePresence('news_title')
            ->notEmptyString('news_title', __d('csv_form', 'VALIDATE_NEWS_TITLE_EMPTY'))
            ->maxLength('news_title', NEWS_MAX_TITLE_LEN, __d('csv_form', 'VALIDATE_NEWS_TITLE_MAX_LENGTH', [NEWS_MAX_TITLE_LEN]));
        $validator
            ->allowEmptyString('news_title_sub')
            ->maxLength('news_title_sub', NEWS_MAX_TITLE_SUB_LEN, __d('csv_form', 'VALIDATE_NEWS_SUB_TITLE_MAX_LENGTH', [NEWS_MAX_TITLE_SUB_LEN]));
        $validator
            ->allowEmptyString('news_url')
            ->maxLength('news_url', NEWS_MAX_URL_LEN, __d('csv_form', 'VALIDATE_NEWS_URL_MAX_LENGTH', [NEWS_MAX_URL_LEN]))
            ->url('news_url', __d('csv_form', 'VALIDATE_NEWS_URL_FORMAT'));
        $validator
            ->requirePresence('news_date')
            ->notEmptyString('news_date',  __d('csv_form', 'VALIDATE_NEWS_DATE_EMPTY'))
            ->add('news_date', 'date', [
                'rule' => 'validateDate',
                'provider' => 'table',
                'message' =>  __d('csv_form', 'VALIDATE_NEWS_DATE_FORMAT')
            ]);
        $validator
            ->allowEmptyString('enabled_from')
            ->add('enabled_from', 'dateTime', [
                'rule' => 'validateDateTime',
                'provider' => 'table',
                'message' => __d('csv_form', 'VALIDATE_NEWS_ENABLE_FROM_DATETIME_FORMAT')
            ]);
        $validator
            ->allowEmptyString('enabled_to')
            ->add('enabled_to', 'dateTime', [
                'rule' => 'validateDateTime',
                'provider' => 'table',
                'message' => __d('csv_form', 'VALIDATE_NEWS_ENABLE_TO_DATETIME_FORMAT')
            ]);
        $validator
            ->requirePresence('order_no')
            ->notEmptyString('order_no',  __d('csv_form', 'VALIDATE_NEWS_ORDER_NO_EMPTY'))
            ->range('order_no', [MIN_ORDER_NO, MAX_ORDER_NO] ,  __d('csv_form', 'VALIDATE_NEWS_ORDER_NO_MIN_MAX_LENGHT', [MIN_ORDER_NO, MAX_ORDER_NO]));
        $validator
            ->requirePresence('is_active')
            ->notEmptyString('is_active',  __d('csv_form', 'VALIDATE_NEWS_ACTIVE_EMPTY'))
            ->range('is_active', [0, 1] ,  __d('csv_form', 'VALIDATE_NEWS_ACTIVE_RANGE'));

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

        return $rules;
    }

    protected function _setData() {
        $schools_data = (new SchoolRepository($this->Schools))->getKeyValuePairsWithCondition(['id', 'school_name'], ['is_active' => true]);
        $this->_schools = !empty($schools_data) ? array_keys($schools_data) : [];
    }

    public function notInList($check)
    {
        if (!in_array($check, [0])) {
            return true;
        }
        return false;
    }

    public function getSchoolInfoForSelectbox()
    {
        return $this->Schools->find('list')->toArray();
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

    //------------------------------------------------------------------------------
    // custom validation
    //------------------------------------------------------------------------------
    //------------------------------------------------
    /// @brief  Check title date validity
    /// @param  $data           A reference to the data to inspect
    /// @param  $message        Error message
    /// @param  $array_in_model An array holding references to model objects
    /// @return An array of results and error messages
    ///         - $ary[0] : Result (true/false)
    ///         - $ary[1] : Error message
    /// @author ChanNL
    //------------------------------------------------
    function validateNewsDate($check, $data)
    {
        $day = $data['data']['title_ym']
            . '-' . sprintf("%02d", $data['data']['title_day']);

        if ($this->isDate($day)) {
            return true;
        }
        return false;
    }

    function inListCustom($check)
    {
        return (MIN_ORDER_NO <= $check && $check <= MAX_ORDER_NO) && is_numeric($check);
    }
}
