<?php

use Migrations\AbstractMigration;

class AddShippedToBiditems extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {
        $table = $this->table('biditems');
        $table->addColumn('shipped', 'boolean', [
            'default' => null,
            'null' => false,
            'after' => 'finished'
        ]);
        $table->update();
    }
    public function down()
    {
        $table = $this->table('biditems');
        $table->removeColumn('shipped');
        $table->update();
    }
}
