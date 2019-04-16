<?php
/**
 * Item class file.
 * @copyright (c) 2015, Pavel Bariev
 * @license http://www.opensource.org/licenses/bsd-license.php
 */

namespace bariew\postAbstractModule\models;

use bariew\abstractModule\models\AbstractModel;
use bariew\yii2Tools\behaviors\RelationViaBehavior;
use bariew\yii2Tools\validators\ListValidator;
use Yii;
use yii\base\DynamicModel;
use \bariew\yii2Tools\behaviors\FileBehavior;
use yii\db\ActiveQuery;
use yii\helpers\Url;

/**
 * Description.
 *
 * Usage:
 * @author Pavel Bariev <bariew@yandex.ru>
 * @property integer $id
 * @property integer $owner_id
 * @property string $title
 * @property string $brief
 * @property string $content
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $image
 *
 * @mixin FileBehavior
 *
 */
class Item extends AbstractModel
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            'required' => [['title', 'brief', 'content'], 'required'],
            [['status'], ListValidator::className()],
            [['brief', 'content'], 'string'],
            [['title'], 'string', 'max' => 255],
            ['image', 'image', 'maxFiles' => 10],
            ['categories', ListValidator::className(),
                'model' => $this,
                'when' => function($model){ return $model instanceof DynamicModel;}],
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestampBehavior' => [
                'class' => \yii\behaviors\TimestampBehavior::className(),
            ],
            'fileBehavior' => [
                'class' => FileBehavior::className(),
                'storage' => [$this, 'getStoragePath'],
                'fileField' => 'image',
                'imageSettings' => [
                    'thumb1' => ['method' => 'thumbnail', 'width' => 50, 'height' => 50],
                    'thumb2' => ['method' => 'thumbnail', 'width' => 100, 'height' => 100],
                    'thumb3' => ['method' => 'thumbnail', 'width' => 200, 'height' => 200],
                ]
            ],
            'relationViaBehavior' => [
                'class' => RelationViaBehavior::className(),
                'relations' => ['categories']
            ]
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getCategoryToItems()
    {
        return static::hasMany(CategoryToItem::childClass(), ['item_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getCategories()
    {
        return static::hasMany(Category::childClass(), ['id' => 'category_id'])
            ->via('categoryToItems');
    }

    public function getCategoriesString()
    {
        return implode(', ', $this->getCategories()->orderBy('title')->select('title')->column());
    }

    /**
     * @return array
     */
    public function categoriesList()
    {
        $class = Category::childClass();
        return $class::find()->indexBy('id')->select('name')->column();
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('modules/post', 'ID'),
            'owner_id' => Yii::t('modules/post', 'Owner ID'),
            'title' => Yii::t('modules/post', 'Title'),
            'brief' => Yii::t('modules/post', 'Brief'),
            'content' => Yii::t('modules/post', 'Content'),
            'status' => Yii::t('modules/post', 'Status'),
            'created_at' => Yii::t('modules/post', 'Created At'),
            'updated_at' => Yii::t('modules/post', 'Updated At'),
            'image' => Yii::t('modules/post', 'Image'),
        ];
    }

    /**
     * status field available values.
     * @return array
     */
    public function statusList()
    {
        return [
            0 => Yii::t('modules/post', 'Inactive'),
            1 => Yii::t('modules/post', 'Active'),
        ];
    }

    /**
     * Relative path for saving model files.
     * @return string path.
     */
    public function getStoragePath()
    {
        $moduleName = AbstractModel::moduleName(static::className());
        $owner_id = $this->getAttribute('owner_id') ? : Yii::$app->user->id;
        return "@app/web/files/{$owner_id}/{$moduleName}/"
            . $this->formName() . '/' . $this->id; 
    }

    /**
     * @param $mainTitle
     * @param $mainDesc
     * @param $mainLink
     * @param $mainRssLink
     * @return string
     */
    public static function rss($mainTitle, $mainDesc, $mainLink, $mainRssLink)
    {
        $dom = new \DOMDocument('1.0', 'utf-8');

        $rss = $dom->createElement('rss');
        $rss->setAttribute('version','2.0');
        $rss->setAttribute('xmlns:dc', "http://purl.org/dc/elements/1.1/");
        $rss->setAttribute('xmlns:sy', "http://purl.org/rss/1.0/modules/syndication/");
        $rss->setAttribute('xmlns:admin', "http://webns.net/mvcb/");
        $rss->setAttribute('xmlns:rdf', "http://www.w3.org/1999/02/22-rdf-syntax-ns#");
        $rss->setAttribute('xmlns:content', "http://purl.org/rss/1.0/modules/content/");
        $rss->setAttribute('xmlns:atom', "http://www.w3.org/2005/Atom");


        $chanel = $dom->createElement( 'channel' );

        $title = $dom->createElement('title');
        $title->appendChild($dom->createTextNode($mainTitle));
        $chanel->appendChild($title);

        $link = $dom->createElement('link');
        $link->appendChild($dom->createTextNode($mainLink));
        $chanel->appendChild($link);

        $description = $dom->createElement('description');
        $description->appendChild($dom->createTextNode($mainDesc));
        $chanel->appendChild($description);

        $atomLink = $dom->createElement('atom:link');
        $atomLink->setAttribute('href' , $mainRssLink);
        $atomLink->setAttribute('rel' , 'self');
        $atomLink->setAttribute('type', 'application/rss+xml');
        $chanel->appendChild( $atomLink );

//        $lastBuildDate = $dom->createElement( 'lastBuildDate' );
//        $lastBuildDate->appendChild( $dom->createTextNode( static::formDate( $this->options['lastBuildDate'] ) ) );
//        $chanel->appendChild( $lastBuildDate );

        $pubDate = $dom->createElement('pubDate');
        $pubDate->appendChild($dom->createTextNode(static::formDate(time())));
        $chanel->appendChild($pubDate);
        /** @var static $model */
        foreach( static::find()->andWhere(['>', 'created_at', strtotime('-1week')])->all() as $model) {
            $item = $dom->createElement('item');

            $_pubDate = $dom->createElement('pubDate');
            $_pubDate->appendChild($dom->createTextNode(static::formDate($model->created_at)));
            $item->appendChild($_pubDate);

            $_title = $dom->createElement('title');
            $_title->appendChild($dom->createTextNode($model->title));
            $item->appendChild($_title);

            $_link = $dom->createElement('link');
            $_link->appendChild($dom->createTextNode(Url::to(['default/view', 'id' => $model->id], true)));
            $item->appendChild($_link);

            $_description = $dom->createElement('description');
            $_description->appendChild( $dom->createCDATASection($model->brief));
            $item->appendChild($_description);

            $chanel->appendChild($item);
        }

        $rss->appendChild($chanel);
        $dom->appendChild($rss);

        return $dom->saveXML();
    }

    /**
     * @param $date
     * @return false|string
     */
    protected static function formDate( $date )
    {
        return date(DATE_RSS, (is_int($date) ? $date : strtotime($date)));
    }
}
