<?php
declare(strict_types=1);

namespace Kareylo\Comments\Test\TestCase\Controller;

use Cake\Datasource\FactoryLocator;
use Cake\Http\Exception\MethodNotAllowedException;
use Cake\Http\ServerRequest;
use Cake\Http\Session;
use Cake\ORM\Exception\MissingBehaviorException;
use Cake\ORM\Table;
use Cake\TestSuite\TestCase;
use Kareylo\Comments\Model\Table\CommentsTable;
use OutOfBoundsException;

class CommentsControllerTest extends TestCase
{
    /**
     * @var CommentsTable
     */
    public CommentsTable $Controller;

    /**
     * @var Session
     */
    public Session $session;

    /**
     * @var Table
     */
    public Table $model;

    /**
     * @var array
     */
    protected array $fixtures = [
        'plugin.Kareylo/Comments.Comments',
        'plugin.Kareylo/Comments.Posts',
        'plugin.Kareylo/Comments.Articles',
        'plugin.Kareylo/Comments.Users',
    ];
    /**
     * @var \Cake\Http\ServerRequest
     */
    private ServerRequest $request;

    /**
     * setup
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->session = new Session();

        $this->Controller = FactoryLocator::get('Table')->get('Kareylo/Comments.Comments');
        $this->request = new ServerRequest();
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown(): void
    {
        $this->session->destroy();
        unset($this->Controller);
        FactoryLocator::get('Table')->clear();
        parent::tearDown();
    }

    /**
     * init
     *
     * @param string $method
     * @param array $options
     * @param bool $removeBehavior
     */
    private function _init(string|array $method = 'POST', array $options = [], bool $removeBehavior = false): void
    {
        if (is_array($method)) {
            $options = $method;
            $method = 'POST';
        }
        $_SERVER['REQUEST_METHOD'] = $method;
        $this->session->write('Auth.User.id', 1);
        $data = array_merge([
            'content' => 'Lorem Ipsum',
            'ref' => 'Posts',
            'ref_id' => '2',
            'parent_id' => '',
        ], $options);
        $this->request = $this->request->withParsedBody($data);
        $this->model = FactoryLocator::get('Table')->get($data['ref']);
        if ($data['ref'] !== 'Posts' || $removeBehavior) {
            $this->model->behaviors()->unload('Commentable');
        } else {
            $this->model->addBehavior('Kareylo/Comments.Commentable');
        }
    }

    /**
     * Test to add a comment with bad method
     *
     * @return void
     */
    public function testAddCommentWithBadMethod(): void
    {
        $this->_init('GET');
        $this->expectException(MethodNotAllowedException::class);
        $this->expectExceptionMessage('Only Post');
        $this->_add();
    }

    /**
     * Correct comment add (correct ref and ref_id)
     *
     * @return void
     */
    public function testAddCommentWithCorrectRefIdAndWithoutParentId(): void
    {
        $this->_init();
        $result = $this->_add();
        $this->assertTrue($result);
    }

    /**
     * Add comment with incorrect RefId
     *
     * @return void
     */
    public function testAddCommentWithIncorrectRefIdAndWithoutParentId(): void
    {
        $this->_init(['ref_id' => '999999']);
        $this->expectException(OutOfBoundsException::class);
        $this->expectExceptionMessage('This Model is not Commentable');
        $this->_add();
    }

    /**
     * Add comment with parent_id
     *
     * @return void
     */
    public function testAddCommentWithCorrectParentId(): void
    {
        $this->_init(['parent_id' => '1']);
        $result = $this->_add();
        $this->assertTrue($result);
    }

    /**
     * Add Comment with incorrect parent_id
     *
     * @return void
     */
    public function testAddCommentWithIncorrectParentId(): void
    {
        $this->_init(['parent_id' => '999999']);
        $this->expectException(OutOfBoundsException::class);
        $this->expectExceptionMessage("You can't comment this record");
        $this->_add();
    }

    /**
     * Add comment while ref not exists
     *
     * @return void
     */
    public function testAddCommentWithModelNotExists(): void
    {
        $this->_init(['ref' => 'Articles']);
        $this->expectException(MissingBehaviorException::class);
        $this->expectExceptionMessage('Behavior is not loaded');
        $this->_add();
    }

    /**
     * add comment with behavior not loaded
     *
     * @return void
     */
    public function testAddCommentWithoutBehavior(): void
    {
        $this->_init('POST', [], true);
        $this->expectException(MissingBehaviorException::class);
        $this->expectExceptionMessage('Behavior is not loaded');
        $this->_add();
    }

    /**
     * Same method as action add in CommentsController but throw exception, not flash messages
     *
     * @return bool
     * @throws \Exception
     */
    protected function _add(): bool
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = array_merge($this->request->getData(), ['ip' => $this->request->clientIp(), 'user_id' => $this->session->read('Auth.User.id')]);

            if (!$this->model->hasBehavior('Commentable')) {
                throw new MissingBehaviorException('Behavior is not loaded');
            }

            // check if we can comment this content
            if ($this->model->hasBehavior('Commentable') && !$this->model->exists(['id' => $data['ref_id']])) {
                throw new OutOfBoundsException('This Model is not Commentable');
            }

            // Check if parent exists with the correct model
            if ($data['parent_id'] && !$this->Controller->exists(['id' => $data['parent_id'], 'ref' => $data['ref']])) {
                throw new OutOfBoundsException("You can't comment this record");
            }

            $comment = $this->model->Comments->newEmptyEntity();
            $comment = $this->model->Comments->patchEntity($comment, $data);
            if ($this->model->Comments->save($comment)) {
                return true;
            } else {
                return false;
            }
        } else {
            throw new MethodNotAllowedException('Only Post');
        }
    }
}
