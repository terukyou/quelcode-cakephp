<?php
use Migrations\AbstractMigration;

class AddDescriptionToBiditems extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     * @return void
     */
    public function up()
    {
        $this->table('biditems')
            ->addColumn(
                'description',
                "string",
                [
                    'default' => null,
                    'limit' => 1000,
                    'null' => false,
                    'after' => 'name',
                ]
            )
            ->addColumn(
                'image_name',
                "string",
                [
                    'default' => null,
                    'limit' => 100,
                    'null' => false,
                    'after' => 'description',
                ]
            )
             ->update();
    }

    public function down()
    {
        $this->table('biditems')
             ->removeColumn('description')
             ->removeColumn('image_name')
             ->update();
    }
}
