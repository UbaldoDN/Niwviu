<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUserBookTable extends Migration
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
            'book_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned' => true,
            ],
            'user_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned' => true,
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp',
            'deleted_at datetime default null',
        ]);

        $this->forge->addForeignKey('user_id', 'users', 'id');
        $this->forge->addForeignKey('book_id', 'books', 'id');
        $this->forge->addKey('id', true);
        $this->forge->createTable('users_books');
    }

    public function down()
    {
        $this->forge->dropTable('users_books');
    }
}
