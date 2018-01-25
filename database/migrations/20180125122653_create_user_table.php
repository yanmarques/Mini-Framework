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
        Table::createFrom('user', function (Table $table) {
            $table->string('name');
            $table->string('email');
            $table->string('password');
            $table->timestamps();
        });
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        Table::dropIfExists('user');
    }
}