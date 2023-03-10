<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use App\Model\Table\BaseTable;

/**
 * Holidays Model
 *
 * @method \App\Model\Entity\Holiday newEmptyEntity()
 * @method \App\Model\Entity\Holiday newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Holiday[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Holiday get($primaryKey, $options = [])
 * @method \App\Model\Entity\Holiday findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Holiday patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Holiday[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Holiday|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Holiday saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Holiday[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Holiday[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Holiday[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Holiday[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class HolidaysTable extends BaseTable
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

        $this->setTable('holidays');
        $this->setDisplayField('holiday_date');
        $this->setPrimaryKey('holiday_date');

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
        $validator
            ->requirePresence('holiday_date')
            ->notEmptyString('holiday_date', __d('csv_form', 'VALIDATE_HOLIDAY_DATE_EMPTY'))
            ->add('holiday_date', 'date', [
                'rule' => 'validateDate',
                'provider' => 'table',
                'message' => __d('csv_form', 'VALIDATE_HOLIDAY_DATE_FORMAT')
            ]);
        $validator
            ->requirePresence('holiday_name')
            ->notEmptyString('holiday_name', __d('csv_form', 'VALIDATE_HOLIDAY_NAME_EMPTY'))
            ->maxLength('holiday_name', HOLIDAYS_MAX_NAME_LEN, __d('csv_form', 'VALIDATE_HOLIDAY_NAME_MAX_LENGTH', [HOLIDAYS_MAX_NAME_LEN]));
        // $validator
        //     ->requirePresence('is_active')
        //     ->notEmptyString('is_active', '【表示許可フラグ値】が入力されていません。')
        //     ->range('is_active', [0, 1] , '【表示許可フラグ値】は0(非表示)か1(表示)で指定してください。');

        return $validator;
    }
}
