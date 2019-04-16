<?php
/**
 * UserItemController class file.
 * @copyright (c) 2015, Pavel Bariev
 * @license http://www.opensource.org/licenses/bsd-license.php
 */

namespace bariew\postAbstractModule\controllers;
use bariew\postAbstractModule\models\Item;
use yii\helpers\Url;

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

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return array_intersect_key(parent::actions(), ['index' => '', 'view' => '']);
    }

    /**
     * @return mixed|string
     */
    public function actionRss()
    {
        $this->layout = null;
        if (!$xml = \Yii::$app->cache->get('rss')) {
            /** @var Item $model */
            $model = $this->findModel();
            $xml = $model::rss(
                \Yii::$app->name . " RSS feed",
                ucwords($this->module->id),
                \Yii::$app->request->hostInfo,
                Url::current([], true)
            );
            \Yii::$app->cache->set('rss', $xml, 60*60);
        }
        \Yii::$app->response->headers->add('Content-Type', 'text/xml');
        echo $xml;
        \Yii::$app->end();
    }
}
