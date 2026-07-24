<?php
declare(strict_types=1);

namespace Kareylo\Comments\Test\TestCase\Model\Table;

use Cake\Core\Configure;
use Cake\Datasource\FactoryLocator;
use Cake\TestSuite\TestCase;
use Kareylo\Comments\Model\Table\CommentsTable;

/**
 * CakePHP Ratings Plugin
 *
 * Rating model tests
 *
 * @package     ratings
 * @subpackage  ratings.tests.cases.models
 */
class CommentsTableTest extends TestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'plugin.Kareylo/Comments.Users',
        'plugin.Kareylo/Comments.Comments',
        'plugin.Kareylo/Comments.Posts',
    ];

    /**
     * @var CommentsTable
     */
    private CommentsTable $Comments;

    /**
     * Start Test callback
     *
     * @return void
     */
    public function setUp(): void
    {
        Configure::delete('Comments');
        parent::setUp();
        $this->Comments = FactoryLocator::get('Table')->get('Kareylo/Comments.Comments');
    }

    /**
     * testCommentInstance
     *
     * @return void
     */
    public function testCommentsInstance(): void
    {
        $this->assertInstanceOf('Kareylo\Comments\Model\Table\CommentsTable', $this->Comments);
    }
}
