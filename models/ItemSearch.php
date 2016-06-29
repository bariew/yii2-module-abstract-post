<?php
/**
 * ItemSearch class file.
 * @copyright (c) 2015, Pavel Bariev
 * @license http://www.opensource.org/licenses/bsd-license.php
 */

namespace bariew\postAbstractModule\models;

use bariew\abstractModule\models\AbstractModelExtender;
use yii\data\ActiveDataProvider;

/**
 * Searches post items.
 * 
 * 
 * @example
 * @author Pavel Bariev <bariew@yandex.ru>
 */
class ItemSearch extends AbstractModelExtender
{
    public $category_id;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status', 'owner_id', 'category_id'], 'integer'],
            [['title', 'brief', 'content', 'image', 'created_at'], 'safe'],
            [['status'], 'boolean'],
        ];
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params = [])
    {
        $query = parent::search();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['created_at' => SORT_DESC]
            ]
        ]);
        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'owner_id' => $this->owner_id,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'brief', $this->brief])
            ->andFilterWhere(['like', 'content', $this->content])
            ->andFilterWhere(['like', 'image', $this->image])
            ->andFilterWhere([
                'like', 'DATE_FORMAT(FROM_UNIXTIME(created_at), "%Y-%m-%d")', $this->created_at
            ])
            ;
        if ($this->category_id) {
            $t = $this->tableName();
            $query->joinWith("categoryToItems", true, "LEFT JOIN")
                ->andWhere(["category_id" => $this->category_id])
                ->groupBy("$t.id");
        }

        return $dataProvider;
    }
}
