<?php
declare(strict_types=1);

namespace Kareylo\Comments\Model\Behavior;

use Cake\ORM\Behavior;
use Cake\ORM\Query;

class CommentableBehavior extends Behavior
{
    /**
     * Default settings
     *
     * @var array
     */
    protected array $_defaultConfig = [
        'modelClass' => null,
        'commentClass' => 'Kareylo/Comments.Comments',
        'foreignKey' => 'ref_id',
        'countComments' => false,
        'fieldCounter' => 'comments_count',
    ];

    /**
     * Setup
     *
     * @param array $config default config
     * @return void
     */
    public function initialize(array $config): void
    {
        if (empty($this->getConfig('modelClass'))) {
            $this->setConfig('modelClass', $this->_table->getAlias());
        }

        $this->_table->hasMany('Comments', [
            'className' => $this->getConfig('commentClass'),
            'foreignKey' => $this->getConfig('foreignKey'),
            'order' => 'Comments.created ASC',
            'conditions' => ['Comments.ref' => "{$this->getConfig('modelClass')}"],
            'dependent' => true,
        ]);

        if ($this->getConfig('countComments')) {
            $this->_table->getAssociation('Comments')->getTarget()->addBehavior('CounterCache', [
                $this->_table->getAlias() => [$this->getConfig('fieldCounter')],
            ]);
        }

        $this->_table->getAssociation('Comments')->getTarget()->belongsTo($this->getConfig('modelClass'), [
            'className' => $this->getConfig('modelClass'),
            'foreignKey' => 'ref_id',
        ]);
    }

    /**
     * Create the finder comments
     *
     * @param \Cake\ORM\Query $query the current Query
     * @param array $options Options
     * @return \Cake\ORM\Query
     */
    public function findComments(Query $query, array $options = []): Query
    {
        return $query->contain([
            'Comments' => function (Query $q) use ($options) {
                return $q
                    ->find('threaded')
                    ->contain(['CreatedBy.Attachments'])
                    ->order(['Comments.created' => 'ASC'])
                    ->find('byPrivacy', $options);
            },
        ]);
    }
}
