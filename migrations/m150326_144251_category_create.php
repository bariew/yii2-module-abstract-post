<?php

use yii\db\Schema;
use yii\db\Migration;
use bariew\postAbstractModule\models\Category;
use bariew\postAbstractModule\models\CategoryToItem;
use bariew\postAbstractModule\models\Item;
use bariew\yii2Tools\helpers\MigrationHelper;
class m150326_144251_category_create extends Migration
{
    public function up()
    {
        $this->createTable(Category::tableName(), [
            'id' => Schema::TYPE_PK,
            'owner_id' => $this->integer(),
            'status' => Schema::TYPE_SMALLINT,
            'title' => Schema::TYPE_STRING,
            'name' => Schema::TYPE_STRING,
            'image' => Schema::TYPE_STRING,
            'content' => Schema::TYPE_TEXT,
            'lft' => Schema::TYPE_INTEGER,
            'rgt' => Schema::TYPE_INTEGER,
            'depth' => Schema::TYPE_INTEGER,
        ]);
        $this->createTable(CategoryToItem::tableName(), [
            'category_id' => Schema::TYPE_INTEGER,
            'item_id' => Schema::TYPE_INTEGER
        ]);
        MigrationHelper::addForeignKey(CategoryToItem::tableName(), 'category_id', Category::tableName(), 'id', 'CASCADE', 'CASCADE');
        MigrationHelper::addForeignKey(CategoryToItem::tableName(), 'item_id', Item::tableName(), 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable(CategoryToItem::tableName());
        $this->dropTable(Category::tableName());
    }
}
