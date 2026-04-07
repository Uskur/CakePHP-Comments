<?php
declare(strict_types=1);

namespace Kareylo\Comments\View\Helper;

use Cake\Datasource\EntityInterface;
use Cake\ORM\TableRegistry;
use Cake\View\Helper;

/**
 * @property \Cake\View\Helper\FormHelper $Form
 */
class CommentHelper extends Helper
{
    protected array $helpers = ['Html', 'Form'];
    protected array $_defaultConfig = [
        'loadJS' => true,
    ];
    protected string $_html = '';

    /**
     * Used to check if user is connected.
     * If user isn't connected, he can't reply and post a comment
     *
     * @var bool
     */
    private bool $_connected = false;

    /**
     * Setup the helper.
     *
     * @param array $config default config
     * @return void
     */
    public function initialize(array $config): void
    {
        $identity = $this->getView()->getRequest()->getAttribute('identity');
        $this->_connected = $identity !== null;
    }

    /**
     * Display all comments of the given entity
     *
     * @param \Cake\Datasource\EntityInterface|array $entity Contain all comments
     * @param bool $private Whether to include private comments.
     * @return string
     */
    public function display(EntityInterface|array $entity = [], bool $private = false): string
    {
        $comments = [];
        if ($entity instanceof EntityInterface && $entity->has('comments')) {
            $comments = $entity->comments;
        } elseif (is_array($entity)) {
            $comments = $entity;
        }

        $this->_html .= $this->getView()->element('Kareylo/Comments.display', [
            'comments' => $comments,
            'connected' => $this->_connected,
            'private' => $private,
        ]);

        // Check if user is connected and add JS if needed
        if ($entity instanceof EntityInterface) {
            $this->_html .= $this->form($entity, $private);
            $this->script();
        }

        return $this->_html;
    }

    /**
     * load JS and return CommentForm
     *
     * @param \Cake\Datasource\EntityInterface $entity the model entity
     * @return string
     */
    public function loadFormAndJS(EntityInterface $entity): string
    {
        $this->script();

        return $this->form($entity);
    }

    /**
     * return the Comment Form
     *
     * @param \Cake\Datasource\EntityInterface $entity ModelEntity
     * @param bool $private Whether to post the comment as private.
     * @return string
     */
    public function form(EntityInterface $entity, bool $private = false): string
    {
        if ($this->_connected) {
            $comment = TableRegistry::getTableLocator()->get('Kareylo/Comments.Comments')->newEmptyEntity();
            $comment->set('ref', $entity->getSource());
            $comment->set('ref_id', $entity->get('id'));

            return $this->getView()->element('Kareylo/Comments.form', [
                'comment' => $comment,
                'connected' => $this->_connected,
                'private' => $private,
                'redirectUrl' => $this->getView()->getRequest()->getRequestTarget(),
            ]);
        }

        return '';
    }

    /**
     * Load JS is required
     *
     * @return void
     */
    public function script(): void
    {
        if ($this->_connected && $this->getConfig('loadJS')) {
            $this->getView()->Html->script('Kareylo/Comments.comments.min.js', ['block' => true]);
        }
    }
}
