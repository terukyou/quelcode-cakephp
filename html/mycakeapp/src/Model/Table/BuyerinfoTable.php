<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Buyerinfo Model
 *
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 *
 * @method \App\Model\Entity\Buyerinfo get($primaryKey, $options = [])
 * @method \App\Model\Entity\Buyerinfo newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Buyerinfo[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Buyerinfo|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Buyerinfo saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Buyerinfo patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Buyerinfo[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Buyerinfo findOrCreate($search, callable $callback = null, $options = [])
 */
class BuyerinfoTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('buyerinfo');
        $this->setDisplayField('name');
        $this->setPrimaryKey('biditem_id');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Biditems', [
            'foreignKey' => 'biditem_id',
            'joinType' => 'INNER',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator->provider('custom', 'App\Model\Validation\CustomValidation');
        $validator
            ->integer('biditem_id')
            ->allowEmptyString('biditem_id', null, 'create');

        $validator
            ->scalar('name')
            ->maxLength('name', 100, '100文字以内で入力ください')
            ->requirePresence('name', 'create')
            ->notEmptyString('name', '必ず入力してください');

        $validator
            ->scalar('home')
            ->maxLength('home', 1000, '1000文字以内で入力ください')
            ->requirePresence('home', 'create')
            ->notEmptyString('home', '必ず入力してください');

        $validator
            ->scalar('phone')
            ->maxLength('phone', 13, '13文字以内で入力ください')
            ->requirePresence('phone', 'create')
            ->notEmptyString('phone', '必ず入力してください')
            ->add('phone', 'isPhoneNumber', [
                'rule' => ['isPhoneNumber'],
                'provider' => 'custom',
                'message' => '数字と「-」のみでご入力ください',
            ]);

        $validator
            ->boolean('received')
            ->notEmptyString('received');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['user_id'], 'Users'));

        return $rules;
    }
}
