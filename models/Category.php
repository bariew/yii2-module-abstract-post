<?php

namespace bariew\postAbstractModule\models;

use bariew\abstractAbstractModule\models\AbstractModel;
use bariew\postAbstractModule\components\NestedQuery;
use bariew\postAbstractModule\Module;
use bariew\yii2Tools\behaviors\FileBehavior;
use creocoder\nestedsets\NestedSetsBehavior;
use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "post_category".
 *
 * @property integer $id
 * @property string $title
 * @property string $name
 * @property string $content
 * @property integer $lft
 * @property integer $rgt
 * @property integer $depth
 * @property integer $status
 * @property integer $owner_id
 * @property Item[] $items
 *
 * @mixin NestedSetsBehavior
 * @mixin FileBehavior
 */
class Category extends AbstractModel
{
    public $items = [];

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['content'], 'string'],
            [['status'], 'integer'],
            [['title', 'name'], 'string', 'max' => 255],
            ['image', 'image', 'maxFiles' => 10],
        ];
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
            'name' => Yii::t('modules/post', 'Name'),
            'content' => Yii::t('modules/post', 'Content'),
            'lft' => Yii::t('modules/post', 'Lft'),
            'rgt' => Yii::t('modules/post', 'Rgt'),
            'depth' => Yii::t('modules/post', 'Depth'),
            'status' => Yii::t('modules/post', 'Status'),
            'image' => Yii::t('modules/post', 'Image'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            'tree' => ['class' => NestedSetsBehavior::className()],
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
        ];
    }

    /**
     * status available value list.
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
     * @return NestedQuery
     */
    public static function find()
    {
        return new NestedQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function beforeDelete() 
    {
        if ($this->depth == 0) {
            throw new \BadMethodCallException(
                \Yii::t('modules/post', "Root category can not be deleted.")
            );
        }
        return parent::beforeDelete();
    }

    public function updateChildren($attributes)
    {
        $childrenIds = $this->children()->select(['id'])->column();
        return $this->updateAll($attributes, ['id' => $childrenIds]);
    }

    /**
     * @return ActiveQuery
     */
    public function getCategoryToItems()
    {
        return static::hasMany(CategoryToItem::childClass(), ['category_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getItems()
    {
        return static::hasMany(Item::childClass(), ['id' => 'item_id'])
            ->via('categoryToItems');
    }

    public static function activeItems($items)
    {
        $result = [];
        $exclude = [];
        foreach ($items as $k => $item) {
            if (!$item['status']) {
                $exclude[] = $item;
                continue;
            }
            if (static::isChildOfArray($exclude, $item)) {
                continue;
            }
            $result[$k] = $item;
        }
        return $result; // keys are kept!
    }

    public static function toTree($items)
    {
        $result = [];
        $parents = [];
        foreach ($items as $item) {
            $parents[$item['depth']][$item['lft']] = $item;
            if (!isset($parents[$item['depth']-1])) {
                $resultEnd = end($result);
                if (!$resultEnd || ($resultEnd['depth'] == $item['depth'])) {
                } else if ($resultEnd['depth'] > $item['depth']) {
                    $result = []; // We will unset previous result if its depth more than current
                } else {
                    continue; // we will not include current to result as its depth more that results roots
                }
                $result[$item['lft']] = &$parents[$item['depth']][$item['lft']];
                continue;
            }
            $parent = end($parents[$item['depth']-1]);
            if (!static::isChildOfArray([$parent], $item)) {
                continue;
            }
            $key = key($parents[$item['depth']-1]);
            $parents[$item['depth']-1][$key]['items'][$item['lft']]
                = &$parents[$item['depth']][$item['lft']];
        }
        return $result;
    }

    public static function isChildOfArray($parents, $child)
    {
        foreach ($parents as $parent) {
            if ($child['lft'] > $parent['lft']
                && $child['rgt'] < $parent['rgt']) {
                return true;
            }
        }
        return false;
    }

    /**
     * Relative path for saving model files.
     * @return string path.
     */
    public function getStoragePath()
    {
        $moduleName = AbstractModel::moduleName(static::className());
        return "@app/web/files/{$moduleName}/"
            . $this->formName() . '/' . $this->id;
    }
}
