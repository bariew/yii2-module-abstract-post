
<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel bariew\postAbstractModule\models\ItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('modules/post', 'List');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="item-index">

    <h1><?= Html::encode($this->title) ?>
        <?= Html::a(
            Yii::t('modules/post', 'Create Item'),
            ['create'],
            ['class' => 'btn btn-success pull-right']
        ) ?>
    </h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            \bariew\yii2Tools\helpers\GridHelper::linkFormat($searchModel, 'title'),
            \bariew\yii2Tools\helpers\GridHelper::listFormat($searchModel, 'status'),
            \bariew\yii2Tools\helpers\GridHelper::dateFormat($searchModel, 'created_at'),
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
