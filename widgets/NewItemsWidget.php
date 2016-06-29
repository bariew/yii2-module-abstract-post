<?php
/**
 * Created by PhpStorm.
 * User: pt
 * Date: 16.06.16
 * Time: 10:01
 */

namespace bariew\postAbstractModule\widgets;


use bariew\postAbstractModule\models\Item;
use yii\base\Widget;

class NewItemsWidget extends Widget
{
    public $limit = 6;
    public $statuses = [1];
    public $viewName = 'newItems';

    public function run()
    {
        $items = Item::childClass(true)->search()
            ->andWhere(['status' => $this->statuses])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit($this->limit)
            ->all();
        return $this->render($this->viewName, compact('items'));
    }
}