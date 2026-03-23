<?php foreach ($comments as $comment) : ?>
    <?= $this->element('Kareylo/Comments.comment', ['comment' => $comment, 'connected' => $connected]) ?>
<?php endforeach;
