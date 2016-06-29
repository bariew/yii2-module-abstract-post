<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model bariew\postAbstractModule\models\Category */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('modules/post', 'Categories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="category-view">

    <h1>
        <?= Html::encode($this->title) ?>
        <p class="pull-right">
            <?= Html::a(Yii::t('modules/post', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a(Yii::t('modules/post', 'Delete'), ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('modules/post', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]) ?>
        </p>
    </h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'image',
                'format' => 'raw',
                'value' => \bariew\postAbstractModule\widgets\ImageGallery::widget(['model' => $model, 'field' => 'thumb1'])
            ],
            'id',
            'title',
            'name',
            'content:ntext',
            \bariew\yii2Tools\helpers\GridHelper::listFormat($model, 'status'),
        ],
    ]) ?>

</div>
