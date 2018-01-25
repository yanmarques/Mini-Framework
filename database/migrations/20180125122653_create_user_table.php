<?php

use Core\Database\Migrations\Migration as AbstractMigration;

class CreateUserTable extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $users = $this->table('users.user');
        $users->string('name');
        $users->string('email');
        $users->string('password');
        $users->save();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        //
    }
}