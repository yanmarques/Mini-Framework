<?php

use Core\Database\Migrations\Migration as AbstractMigration;
use Core\Database\Migrations\Table;

class CreateUserTable extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        // Create table from name
        Table::createFrom('user', function (Table $table) {
            $table->timestamps();
        });
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        // Drop table from name if it exists
        Table::dropIfExists('user');
    }
}