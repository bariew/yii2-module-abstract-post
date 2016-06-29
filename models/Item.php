<?php
/**
 * Item class file.
 * @copyright (c) 2015, Pavel Bariev
 * @license http://www.opensource.org/licenses/bsd-license.php
 */

namespace bariew\postAbstractModule\models;

use bariew\abstractAbstractModule\models\AbstractModel;
use bariew\yii2Tools\behaviors\RelationViaBehavior;
use bariew\yii2Tools\validators\ListValidator;
use Yii;
use yii\base\DynamicModel;
use \bariew\yii2Tools\behaviors\FileBehavior;
use yii\db\ActiveQuery;

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
}
