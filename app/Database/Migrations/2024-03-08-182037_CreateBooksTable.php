<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBooksTable extends Migration
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
            'author' => [
                'type'           => 'VARCHAR',
                'constraint'     => 150
            ],
            'published_at datetime default current_timestamp',
            'is_available' => [
                'type'           => 'TINYINT',
                'constraint'     => 1,
                'default'        => 1
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp',
            'deleted_at datetime default null',
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('books');
    }

    public function down()
    {
        $this->forge->dropTable('books');
    }
}
