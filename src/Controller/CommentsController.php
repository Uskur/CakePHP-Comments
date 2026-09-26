<?php
declare(strict_types=1);

namespace Kareylo\Comments\Controller;

use Cake\Event\Event;
use Cake\Http\Response;
use Cake\Log\Log;
use UnexpectedValueException;

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

        $identity = $this->request->getAttribute('identity');
        $userId = $identity?->getIdentifier();
        if ($userId === null) {
            Log::write('warning', 'Comment could not be added: user is not authenticated.');
            $this->Flash->error(__('You must log in to add a comment.'));

            return $this->redirect($redirectUrl);
        }

        $data = array_merge($this->request->getData(), [
            'ip' => (string)$this->request->clientIp(),
            'user_id' => $userId,
        ]);
        $ref = (string)($data['ref'] ?? '');
        if ($ref === '') {
            Log::write('warning', 'Comment could not be added: model reference is missing.', [
                'user_id' => $userId,
                'ref_id' => $data['ref_id'] ?? null,
            ]);
            $this->Flash->error(__('A comment cannot be added to this item.'));

            return $this->redirect($redirectUrl);
        }

        try {
            $model = $this->fetchTable($ref);
        } catch (UnexpectedValueException) {
            Log::write('warning', 'Comment could not be added: model reference is invalid.', [
                'user_id' => $userId,
                'ref' => $ref,
                'ref_id' => $data['ref_id'] ?? null,
            ]);
            $this->Flash->error(__('A comment cannot be added to this item.'));

            return $this->redirect($redirectUrl);
        }

        $refId = (string)($data['ref_id'] ?? '');
        $target = $refId !== ''
            ? $model->find()->where([$model->aliasField('id') => $refId])->first()
            : null;
        if (!$model->hasBehavior('Commentable') || $target === null) {
            Log::write('warning', 'Comment could not be added: target is not commentable or does not exist.', [
                'user_id' => $userId,
                'ref' => $ref,
                'ref_id' => $data['ref_id'] ?? null,
            ]);
            $this->Flash->error(__('A comment cannot be added to this item.'));

            return $this->redirect($redirectUrl);
        }

        $authorizationEvent = new Event('Kareylo.Comments.beforeAdd', $this, [
            'identity' => $identity,
            'model' => $model,
            'target' => $target,
        ]);
        $this->getEventManager()->dispatch($authorizationEvent);
        $authorization = $authorizationEvent->getResult();
        if (
            $authorizationEvent->isStopped()
            || (is_array($authorization) && ($authorization['allowed'] ?? true) === false)
        ) {
            $message = is_array($authorization) && is_string($authorization['message'] ?? null)
                ? $authorization['message']
                : __('A comment cannot be added to this item.');
            Log::write('warning', 'Comment could not be added: target authorization rejected.', [
                'user_id' => $userId,
                'ref' => $ref,
                'ref_id' => $refId,
                'reason' => is_array($authorization) ? ($authorization['reason'] ?? null) : null,
            ]);
            $this->Flash->error($message);

            return $this->redirect($redirectUrl);
        }

        if (
            !empty($data['parent_id']) &&
            !$this->Comments->exists([
                'id' => $data['parent_id'],
                'ref' => $data['ref'],
                'ref_id' => $refId,
            ])
        ) {
            Log::write('warning', 'Comment reply could not be added: parent comment does not exist.', [
                'user_id' => $userId,
                'ref' => $ref,
                'ref_id' => $data['ref_id'] ?? null,
                'parent_id' => $data['parent_id'],
            ]);
            $this->Flash->error(__('A reply cannot be added to this comment.'));

            return $this->redirect($redirectUrl);
        }

        $comment = $model->Comments->newEmptyEntity();
        $comment = $model->Comments->patchEntity($comment, $data);
        if ($model->Comments->save($comment)) {
            Log::write('info', 'Comment added.', [
                'comment_id' => $comment->id,
                'user_id' => $userId,
                'ref' => $ref,
                'ref_id' => $data['ref_id'] ?? null,
            ]);
            $this->Flash->success(__('The comment has been saved.'));
        } else {
            Log::write('warning', 'Comment could not be added.', [
                'user_id' => $userId,
                'ref' => $ref,
                'ref_id' => $data['ref_id'] ?? null,
                'errors' => $comment->getErrors(),
            ]);
            $this->Flash->error(__('The comment could not be saved. Please, try again.'));
        }

        return $this->redirect($redirectUrl);
    }
}
