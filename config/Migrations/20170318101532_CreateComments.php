<?php
use Migrations\AbstractMigration;

class CreateComments extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {
        $table = $this->table('comments', ['id' => false, 'primary_key' => ['id']]);
        $table->addColumn('id', 'uuid', [
            'default' => null,
            'limit' => null,
            'null' => false,
        ]);
        $table->addColumn('content', 'text', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('ref', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => false,
        ]);
        $table->addColumn('ref_id', 'uuid', [
            'default' => null,
            'limit' => 36,
            'null' => false,
        ]);
        $table->addColumn('ip', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => false,
        ]);
        $table->addColumn('parent_id', 'uuid', [
            'default' => null,
            'limit' => 36,
            'null' => true,
        ]);
        $table->addColumn('user_id', 'uuid', [
            'default' => null,
            'limit' => 36,
            'null' => true
        ]);
        $table->addColumn('created', 'datetime', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('modified', 'datetime', [
            'default' => null,
            'null' => false,
        ]);
        //$table->addForeignKey('user_id', 'users', 'id');
        $table->addIndex('ref_id', [
            'unique' => false
        ]);
        $table->create();
    }
}
