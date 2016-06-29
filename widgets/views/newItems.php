<?php
/** @var \bariew\postAbstractModule\models\Item[] $items */
?>
<ul class="new-items">
    <?php foreach($items as $item): ?>
        <li>
            <?= \yii\helpers\Html::a(
                \yii\helpers\Html::img($item->getFileLink('thumb1')),
                ['view', 'id' => $item->id]
            ) ?>
            <span><?= $item->title . "({$item->getCategoriesString()})" ?></span>
        </li>
    <?php endforeach; ?>
</ul>