<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model bariew\postAbstractModule\models\Item */

$this->title = Yii::t('modules/post', 'Create {modelClass}: ', [
    'modelClass' => $model->formName(),
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('modules/post', 'List'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('modules/post', 'Update');
?>
<div class="item-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
