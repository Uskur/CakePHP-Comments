<?php
declare(strict_types=1);

namespace Kareylo\Comments\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Comments Model
 *
 * @property \Cake\ORM\Association\BelongsTo $ParentComments
 * @property \Cake\ORM\Association\BelongsTo $CreatedBy
 * @property \Cake\ORM\Association\HasMany $ChildComments
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class CommentsTable extends Table
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

        $this->setTable('comments');
        $this->setDisplayField('content');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('ParentComments', [
            'className' => 'Kareylo/Comments.Comments',
            'foreignKey' => 'parent_id',
        ]);

        $this->hasMany('ChildComments', [
            'className' => 'Kareylo/Comments.Comments',
            'foreignKey' => 'parent_id',
        ]);
        $this->belongsTo('CreatedBy', [
            'className' => 'Users',
            'foreignKey' => 'user_id',
        ]);
    }

    /**
     * Validations rules
     *
     * @param \Cake\Validation\Validator $validator validator
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->requirePresence('content', 'create')
            ->notEmptyString('content', __('Vous devez renseigner un contenu'));
        $validator
            ->requirePresence('ref', 'create')
            ->notEmptyString('ref', __('Vous ne pouvez pas commenter ce contenu'));
        $validator
            ->requirePresence('ref_id', 'create')
            ->notEmptyString('ref_id', __('Vous ne pouvez pas commenter ce contenu'));

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
        $rules->add($rules->existsIn(['parent_id'], 'ParentComments'));
        $rules->add($rules->existsIn(['user_id'], 'CreatedBy'));

        return $rules;
    }

    /**
     * Filter comments by privacy.
     *
     * @param \Cake\ORM\Query $query Query instance.
     * @param array $options Finder options.
     * @return \Cake\ORM\Query
     */
    public function findByPrivacy(Query $query, array $options): Query
    {
        if (isset($options['private'])) {
            return $query->where(['private' => $options['private']]);
        }

        return $query->where(['private' => false]);
    }
}
