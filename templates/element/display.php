<?php if (isset($title) && $title !== '') : ?>
    <h4><?= h($title) ?> <span class="badge badge-secondary"><?= $commentCount ?></span></h4>
<?php endif; ?>
<?php foreach ($comments as $comment) : ?>
    <?= $this->element('Kareylo/Comments.comment', [
        'comment' => $comment,
        'connected' => $connected,
        'allowCommenting' => $allowCommenting ?? true,
    ]) ?>
<?php endforeach; ?>
<?php if (isset($allowCommenting) && !$allowCommenting) : ?>
    <div class="alert alert-info"><?= __('This discussion is closed.') ?></div>
<?php endif; ?>
