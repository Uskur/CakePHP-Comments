<?php

use Migrations\AbstractMigration;

class PrivateComments extends AbstractMigration
{
    /**
     * Add the private flag.
     *
     * @return void
     */
    public function up()
    {
        $this->table('comments')
            ->addColumn('private', 'boolean', [
                'default' => false,
                'limit' => null,
                'null' => false,
                'after' => 'content',
            ])->update();
    }

    /**
     * Remove the private flag.
     *
     * @return void
     */
    public function down()
    {
        $this->table('comments')->removeColumn('private');
    }
}
