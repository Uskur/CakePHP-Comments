<div class="media">
    <?= $this->Html->image('blank-profile-picture.png', ['alt' => 'Placeholder', 'class' => 'mr-1']) ?>
    <div class="media-body">
        <h5 class="mt-0">
            <?= __('On {0}, {1} wrote:', $comment->created->format('l, d M y H:i:s'), $comment->created_by->name ?? $comment->user->name ?? '') ?>
        </h5>
        <?= h($comment->content) ?>
        <br><a href="#" class="reply" data-id="<?= h($comment->id) ?>">Reply</a>
        <?php if ($comment->has('children')) : ?>
            <?= $this->element('Kareylo/Comments.display', ['comments' => $comment->children, 'connected' => $connected]) ?>
        <?php endif; ?>
    </div>
</div>
