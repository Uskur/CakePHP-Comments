<?php
declare(strict_types=1);

namespace Kareylo\Comments\Controller;

use Cake\Http\Response;

/**
 * Class CommentsController
 *
 * @package Comments\Controller
 * @property CommentsTable Comments
 */
class CommentsController extends AppController
{
    /**
     * Setup
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();
    }

    /**
     * Add a comment to specific model
     *
     * @return \Cake\Http\Response
     */
    public function add(): Response
    {
        $redirectUrl = (string)$this->request->getData('redirect_url');
        if ($redirectUrl === '') {
            $redirectUrl = $this->request->referer('/', true);
        }
        if ($redirectUrl === '' || str_contains($redirectUrl, '/comments/comments/add')) {
            $redirectUrl = '/';
        }
        $redirectUrl .= '#commentForm';
        if (!$this->request->is('post')) {
            return $this->redirect($redirectUrl);
        }

        $userId = $this->currentUser('id');
        if (!$userId) {
            $this->Flash->set(__("You can't comment this"), ['element' => 'Kareylo/Comments.comment_error']);

            return $this->redirect($redirectUrl);
        }

        $data = array_merge($this->request->getData(), [
            'ip' => (string)$this->request->clientIp(),
            'user_id' => $userId,
        ]);
        $ref = (string)($data['ref'] ?? '');
        if ($ref === '') {
            $this->Flash->set(__("You can't comment this"), ['element' => 'Kareylo/Comments.comment_error']);

            return $this->redirect($redirectUrl);
        }

        try {
            $model = $this->fetchTable($ref);
        } catch (\UnexpectedValueException) {
            $this->Flash->set(__("You can't comment this"), ['element' => 'Kareylo/Comments.comment_error']);

            return $this->redirect($redirectUrl);
        }

        if (
            !$model->hasBehavior('Commentable') ||
            !$model->exists(['id' => $data['ref_id']])
        ) {
            $this->Flash->set(__("You can't comment this"), ['element' => 'Kareylo/Comments.comment_error']);

            return $this->redirect($redirectUrl);
        }

        if (
            !empty($data['parent_id']) &&
            !$this->Comments->exists(['id' => $data['parent_id'], 'ref' => $data['ref']])
        ) {
            $this->Flash->set(__("You can't answer to this comment !"), ['element' => 'Kareylo/Comments.comment_error']);

            return $this->redirect($redirectUrl);
        }

        $comment = $model->Comments->newEmptyEntity();
        $comment = $model->Comments->patchEntity($comment, $data);
        if ($model->Comments->save($comment)) {
            $this->Flash->set(__('Your comment has been correctly added !'), ['element' => 'Kareylo/Comments.comment_success']);
        } else {
            $this->Flash->set(__('An error occured while saving your comment ! '), ['element' => 'Kareylo/Comments.comment_error']);
        }

        return $this->redirect($redirectUrl);
    }
}
