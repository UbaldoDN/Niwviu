<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCategorieBookTable extends Migration
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
            'category_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned' => true,
            ],
            'book_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned' => true,
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp',
            'deleted_at datetime default null',
        ]);

        $this->forge->addForeignKey('category_id', 'categories', 'id');
        $this->forge->addForeignKey('book_id', 'books', 'id');
        $this->forge->addKey('id', true);
        $this->forge->createTable('categories_books');
    }

    public function down()
    {
        $this->forge->dropTable('categories_books');
    }
}
