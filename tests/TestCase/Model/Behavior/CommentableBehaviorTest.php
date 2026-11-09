<?php
declare(strict_types=1);

namespace Kareylo\Comments\Test\TestCase\Model\Behavior;

use Cake\Datasource\FactoryLocator;
use Cake\ORM\Query\SelectQuery;
use Cake\ORM\Table;
use Cake\TestSuite\TestCase;

class CommentableBehaviorTest extends TestCase
{
    /**
     * @var Table|null
     */
    public ?Table $Posts = null;

    /**
     * @var array
     */
    protected array $fixtures = [
        'plugin.Kareylo/Comments.Comments',
        'plugin.Kareylo/Comments.Users',
        'plugin.Kareylo/Comments.Posts',
    ];

    /**
     * setUp
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->Posts = FactoryLocator::get('Table')->get('Posts');
        FactoryLocator::get('Table')->get('Kareylo/Comments.Comments')->getAssociation('CreatedBy')->getTarget()->hasMany('Attachments', ['foreignKey' => 'user_id']);
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Posts);
        FactoryLocator::get('Table')->clear();
        parent::tearDown();
    }

    /**
     * Test the finder with no comments
     *
     * @return void
     */
    public function testFindCommentsWithoutComments(): void
    {
        $this->Posts->addBehavior('Kareylo/Comments.Commentable', []);
        $result = $this->Posts->find()->where(['id' => 1])->find('comments')->first();
        $expected = $this->Posts->get(1, contain: ['Comments' => fn(SelectQuery $q) => $q->find('threaded')->contain('CreatedBy.Attachments')]);
        $this->assertEquals($expected, $result);
    }

    /**
     * Test the finder when there's no datas
     *
     * @return void
     */
    public function testFindCommentsWithEmptyModelData(): void
    {
        $this->Posts->addBehavior('Kareylo/Comments.Commentable', []);
        $result = $this->Posts->find()->where(['id' => 999])->find('comments')->first();
        $this->assertEquals(null, $result);
    }

    /**
     * Test the finder with data
     *
     * @return void
     */
    public function testFindCommentsWithModelData(): void
    {
        $this->Posts->addBehavior('Kareylo/Comments.Commentable', []);
        $result = $this->Posts->find()->where(['id' => 2])->find('comments')->first();
        $expected = $this->Posts->get(2, contain: ['Comments' => function (SelectQuery $q) {
            return $q->find('threaded')->contain('CreatedBy.Attachments');
        }]);
        $this->assertEquals($expected, $result);
    }
}
