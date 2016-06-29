<?php
/**
 * DeleteAction class file.
 * @copyright (c) 2015, bariew
 * @license http://www.opensource.org/licenses/bsd-license.php
 */

namespace bariew\postAbstractModule\actions;

use yii\base\Action;
use bariew\postAbstractModule\models\SearchItem;
use Yii;
use bariew\postAbstractModule\controllers\ItemController;

/**
 * Description.
 *
 * Usage:
 * @author Pavel Bariev <bariew@yandex.ru>
 *
 * @property ItemController $controller
 */
class TreeDeleteAction extends Action
{
    public $redirectAction = ['index'];
    /**
     * @inheritdoc
     */
    public function run($id)
    {
        $this->controller->findModel($id)->deleteWithChildren();

        return $this->controller->redirect($this->redirectAction);
    }
}