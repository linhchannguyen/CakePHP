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
use App\Repositories\EventTypes\EventTypeRepository;


/**
 * Events Model
 *
 * @property \App\Model\Table\SchoolsTable&\Cake\ORM\Association\BelongsTo $Schools
 * @property \App\Model\Table\KouzasTable&\Cake\ORM\Association\BelongsTo $Kouzas
 *
 * @method \App\Model\Entity\Event newEmptyEntity()
 * @method \App\Model\Entity\Event newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Event[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Event get($primaryKey, $options = [])
 * @method \App\Model\Entity\Event findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Event patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Event[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Event|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Event saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Event[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Event[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Event[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Event[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class EventsTable extends BaseTable
{
    protected $_schools;
    protected $_kouzas;
    protected $_eventTypes;
    protected $EventTypes;

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('events');
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
        $this->EventTypes = $this->fetchTable('EventTypes');
    }

    public function validationCustom(Validator $validator): Validator
    {
        $validator
            ->add('school', 'inList', [
                'rule' => ['inList', $this->getSchoolInfoForSelectbox()],
                'message' => __d('validation', 'EVENTS_SELECT_SCHOOL_REQUIRED')
            ]);
        $validator
            ->add('kouza', 'inList', [
                'rule' => ['inList', $this->getKouzaInfoForSelectbox()],
                'message' => __d('validation', 'EVENTS_SELECT_KOUZA_REQUIRED')
            ]);
        $validator
            ->add('event_type', 'inList', [
                'rule' => ['inList', $this->getEventTypeInfoForSelectbox()],
                'message' => __d('validation', 'EVENTS_SELECT_EVENT_TYPE_REQUIRED')
            ]);
        $validator
            ->add('event_ym', 'notInList', ['rule' => 'notInListNegative', 'provider' => 'table', 'message' => __d('validation', 'EVENTS_SELECT_YM')]);
        $validator
            ->add('event_day', 'notInList', ['rule' => 'notInListNegative', 'provider' => 'table', 'message' => __d('validation', 'EVENTS_SELECT_DAY')]);
        $validator
            ->add('event_time_h', 'notInList', ['rule' => 'notInListPositive', 'provider' => 'table', 'message' => __d('validation', 'EVENTS_SELECT_TIME_H')]);
        $validator
            ->add('event_time_m', 'notInList', ['rule' => 'notInListPositive', 'provider' => 'table', 'message' => __d('validation', 'EVENTS_SELECT_TIME_M')]);
        $validator
            ->notEmptyString('text_title', __d('validation', 'EVENTS_TITLE_REQUIRED'))
            ->maxLength('text_title', EVENTS_MAX_TITLE_LEN, "【タイトルは】" . EVENTS_MAX_TITLE_LEN . "文字以内でご入力ください。");
        $validator
            ->allowEmptyString('text_body')
            ->maxLength('text_body', EVENTS_MAX_BODY_LEN, "【本文】は" . EVENTS_MAX_BODY_LEN . "文字以内でご入力ください。");
        $validator
            ->notEmptyString('text_order_no', __d('validation', 'EVENTS_ENTER_SORT_ORDER'))
            ->add('text_order_no', 'inListCustom', ['rule' => 'inListCustom', 'provider' => 'table', 'message' => "【並び順補正値】は" . MIN_ORDER_NO . "以上" . MAX_ORDER_NO . "以下の整数で指定してください。"]);
        $validator
            ->notEmptyString('radio_is_active', __d('validation', 'EVENTS_IS_ACTIVE_REQUIRED'))
            ->add('radio_is_active', 'inList', [
                'rule' => ['inList', [0, 1]],
                'message' => __d('validation', 'EVENTS_IS_ACTIVE_REQUIRED')
            ]);
        $validator
            ->add('event_date', 'validateEventDate', ['rule' => 'validateEventDate', 'provider' => 'table', 'message' => __d('validation', 'EVENTS_DATETIME_INVALID')]);
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
            ->notEmptyString('school_id', __d('csv_form', 'VALIDATE_EVENT_SCHOOL_EMPTY'))
            ->inList('school_id', $this->_schools, __d('csv_form', 'VALIDATE_EVENT_SCHOOL_INVALID'));
        $validator
            ->requirePresence('kouza_id')
            ->notEmptyString('kouza_id', __d('csv_form', 'VALIDATE_EVENT_KOUZA_EMPTY'))
            ->inList('kouza_id', $this->_kouzas, __d('csv_form', 'VALIDATE_EVENT_KOUZA_INVALID'));
        $validator
            ->allowEmptyString('event_date')
            ->add('event_date', 'dateTime', [
                'rule' => 'validateDateTime',
                'provider' => 'table',
                'message' => __d('csv_form', 'VALIDATE_EVENT_DATETIME_FORMAT')
            ]);
        $validator
            ->inList('event_type', $this->_eventTypes, __d('csv_form', 'VALIDATE_EVENT_TYPE_INVALID'));
        $validator
            ->requirePresence('event_title')
            ->notEmptyString('event_title', __d('csv_form', 'VALIDATE_EVENT_TITLE_EMPTY'))
            ->maxLength('event_title', EVENTS_MAX_TITLE_LEN, __d('csv_form', 'VALIDATE_EVENT_TITLE_MAX_LENGTH', [EVENTS_MAX_TITLE_LEN]));
        $validator
            ->allowEmptyString('event_body')
            ->maxLength('event_body', EVENTS_MAX_BODY_LEN, __d('csv_form', 'VALIDATE_EVENT_BODY_MAX_LENGTH', [EVENTS_MAX_BODY_LEN]));
        $validator
            ->requirePresence('order_no')
            ->notEmptyString('order_no',  __d('csv_form', 'VALIDATE_EVENT_ORDER_NO_EMPTY'))
            ->range('order_no', [MIN_ORDER_NO, MAX_ORDER_NO],  __d('csv_form', 'VALIDATE_EVENT_ORDER_NO_MIN_MAX_LENGHT', [MIN_ORDER_NO, MAX_ORDER_NO]));
        $validator
            ->requirePresence('is_active')
            ->notEmptyString('is_active',  __d('csv_form', 'VALIDATE_EVENT_ACTIVE_EMPTY'))
            ->range('is_active', [0, 1],  __d('csv_form', 'VALIDATE_EVENT_ACTIVE_RANGE'));

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
        $event_types_data = (new EventTypeRepository($this->EventTypes))->getKeyValuePairsWithCondition(['id', 'event_type_name'], ['is_active' => true]);
        $this->_schools = !empty($schools_data) ? array_keys($schools_data) : [];
        $this->_kouzas = !empty($kouzas_data) ? array_keys($kouzas_data) : [];
        $this->_eventTypes = !empty($event_types_data) ? array_keys($event_types_data) : [];
    }

    public function getSchoolInfoForSelectbox()
    {
        return $this->Schools->find('list')->toArray();
    }

    public function getKouzaInfoForSelectbox()
    {
        return $this->Kouzas->find('list')->toArray();
    }

    public function getEventTypeInfoForSelectbox()
    {
        $eventTypeRepository = new EventTypeRepository($this->EventTypes);
        $conditions = [
            'is_active' => true
        ];
        $fields = ['id', 'icon', 'order_no', 'event_type_name', 'inner_class', 'content'];
        $orderBy = ['order_no', 'id'];

        $event_types = $eventTypeRepository->getByConditionsOrderBy($conditions, $fields, $orderBy);
        $m_types_info = [];
        for ($i = 0; $i < count($event_types); $i++) {
            $m_types_info[] = $event_types[$i]['id'];
        }
        return $m_types_info;
    }

    public function notInListNegative($check)
    {
        if (!in_array($check, [0])) {
            return true;
        }
        return false;
    }

    public function notInListPositive($check)
    {
        if (!in_array($check, [-1])) {
            return true;
        }
        return false;
    }

    function inListCustom($check)
    {
        return (MIN_ORDER_NO <= $check && $check <= MAX_ORDER_NO) && is_numeric($check);
    }

    //------------------------------------------------------------------------------
    // カスタムバリデーション
    //------------------------------------------------------------------------------
    //------------------------------------------------
    /// @brief  イベント日時の正当性を検査する
    /// @param  $data           検査するデータへの参照
    /// @param  $message        エラーメッセージ
    /// @param  $array_in_model モデルオブジェクトへの参照を保持した配列
    /// @return 結果とエラーメッセージの配列
    ///         - $ary[0] : 結果 (true/false)
    ///         - $ary[1] : エラーメッセージ
    //------------------------------------------------
    function validateEventDate($check, $data)
    {
        $daytime = sprintf(
            "%s-%02d %s:%s:00",
            $data['data']['event_ym'],
            $data['data']['event_day'],
            $data['data']['event_time_h'],
            $data['data']['event_time_m']
        );

        if (isDateTime($daytime)) {
            return true;
        }
        return false;
    }
}
