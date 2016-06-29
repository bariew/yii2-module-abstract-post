<?php

use yii\db\Schema;
use yii\db\Migration;
use bariew\postAbstractModule\models\Item;

class m150207_124151_item_create extends Migration
{
    public function up()
    {
        $this->createTable(Item::tableName(), [
            'id' => Schema::TYPE_PK,
            'owner_id' => Schema::TYPE_INTEGER,
            'status' => Schema::TYPE_SMALLINT,
            'title' => Schema::TYPE_STRING,
            'brief' => Schema::TYPE_TEXT,
            'content' => Schema::TYPE_TEXT,
            'created_at' => Schema::TYPE_INTEGER,
            'updated_at' => Schema::TYPE_INTEGER,
            'image' => Schema::TYPE_STRING,
        ]);
    }

    public function down()
    {
        $this->dropTable(Item::tableName());
    }
}
