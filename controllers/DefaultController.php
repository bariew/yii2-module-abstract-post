<?php
/**
 * UserItemController class file.
 * @copyright (c) 2015, Pavel Bariev
 * @license http://www.opensource.org/licenses/bsd-license.php
 */

namespace bariew\postAbstractModule\controllers;
use bariew\postAbstractModule\actions\IndexAction;
use bariew\postAbstractModule\actions\ViewAction;
use bariew\postAbstractModule\models\Item;

/**
 * Description.
 *
 * Usage:
 * @author Pavel Bariev <bariew@yandex.ru>
 *
 */
class DefaultController extends ItemController
{
    public $modelName = 'Item';
    public function actions()
    {
        return array_intersect_key(parent::actions(), ['index' => '', 'view' => '']);
    }
}
