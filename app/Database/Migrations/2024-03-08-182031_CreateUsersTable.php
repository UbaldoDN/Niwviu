<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true
            ],
            'name' => [
                'type'           => 'VARCHAR',
                'constraint'     => 150
            ],
            'email' => [
                'type'           => 'VARCHAR',
                'constraint'     => 150
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp',
            'deleted_at datetime default null',
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('users');
    }

    public function down()
    {
        $this->forge->dropTable('users');
    }
}
