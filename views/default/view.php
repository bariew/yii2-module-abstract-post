<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model bariew\postAbstractModule\models\Item */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('modules/post', 'Items'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="item-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'owner_id',
            'title',
            'brief:ntext',
            'content:ntext',
            \bariew\yii2Tools\helpers\GridHelper::listFormat($model, 'status'),
            'created_at:datetime',
            [
                'attribute' => 'image',
                'format' => 'raw',
                'value' => \bariew\postAbstractModule\widgets\ImageGallery::widget(['model' => $model, 'field' => 'thumb1'])
            ],
        ],
    ]) ?>

</div>
