<?= $this->Form->create($comment, ['id' => 'commentForm', 'url' => ['prefix'=>false, 'controller' => 'Comments', 'action' => 'add', 'plugin' => 'Kareylo/Comments']]); ?>
    <?= $this->Form->control('content'); ?>
    <?= $this->Form->hidden('ref'); ?>
    <?= $this->Form->hidden('ref_id'); ?>
    <?= $this->Form->unlockField('parent_id'); ?>
    <?= $this->Form->hidden('parent_id', ['default' => null]); ?>
    <?= $this->Form->button(__('Comment')) ?>
<?= $this->Form->end() ?>
