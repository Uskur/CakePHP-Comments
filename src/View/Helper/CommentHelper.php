<?php

namespace Kareylo\Comments\View\Helper;

use Cake\Datasource\EntityInterface;
use Cake\ORM\TableRegistry;
use Cake\View\Helper;
use Cake\View\Helper\FormHelper;

/**
 * @property FormHelper Form
 */
class CommentHelper extends Helper
{
    public $helpers = ['Html', 'Form'];
    public $_defaultConfig = [
        'loadJS' => false,
    ];

    /**
     * Used to check if user is connected.
     * If user isn't connected, he can't reply and post a comment
     * @var bool
     */
    private $_connected = false;

    /**
     * Setup the helper.
     * @param array $config default config
     * @return void
     */
    public function initialize(array $config)
    {
        $this->_connected = $this->request->session()->read('Auth.User.id') !== null;
    }

    /**
     * Display all comments of the given entity
     * @param EntityInterface|array $entity Contain all comments
     * @return string
     */
    public function display($entity = [])
    {
        $comments = [];
        if ($entity instanceof EntityInterface && $entity->has('comments')) {
            $comments = $entity->comments;
        } elseif (is_array($entity)) {
            $comments = $entity;
        }
               
        $this->_html .= $this->_View->element('Kareylo/Comments.display', ['comments' => $comments, 'connected' => $this->_connected]);

        // Check if user is connected and add JS if needed
        if ($entity instanceof EntityInterface) {
            $this->_html .= $this->form($entity);
            $this->script();
        }

        return $this->_html;
    }

    /**
     * load JS and return CommentForm
     * @param EntityInterface $entity the model entity
     * @return string
     */
    public function loadFormAndJS(EntityInterface $entity)
    {
        $this->script();

        return $this->form($entity);
    }

    /**
     * return the Comment Form
     * @param EntityInterface $entity ModelEntity
     * @return string
     */
    public function form(EntityInterface $entity)
    {
        if ($this->_connected) {
            return "<div class='row {$this->getConfig('class')}'>{$this->_View->element($this->_files['form'], ['comment' => TableRegistry::get('Comments')->newEntity(['ref' => $entity->getSource(), 'ref_id' => $entity->get('id')])])}</div>";
        }

        return '';
    }

    /**
     * Load JS is required
     * @return void
     */
    public function script()
    {
        if ($this->_connected && $this->getConfig('loadJS')) {
            $this->_View->Html->script('Kareylo/Comments.comments.min.js', ['block' => true]);
        }
    }
    
}
