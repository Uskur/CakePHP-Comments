<?php
use Migrations\AbstractMigration;

class PrivateComments extends AbstractMigration
{
    public function up()
    {
        $this->table('comments')
            ->addColumn('private', 'boolean', [
                'default' => false,
                'limit' => null,
                'null' => false,
                'after' => 'content'
            ])->update();
    }

    public function down()
    {
        $this->table('comments')->removeColumn('private');
    }
}
