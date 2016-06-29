<?php
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel bariew\postAbstractModule\models\ItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
?>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'summary' => false,
    'columns' => [
        'title',
        'created_at:datetime',
        \bariew\yii2Tools\helpers\GridHelper::listFormat($searchModel, 'status'),
        ['class' => 'yii\grid\ActionColumn'],
    ],
]); ?>