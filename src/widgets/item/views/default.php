<?php

/** @var yii\web\View $this */
/** @var \common\modules\catalogs\widgets\item\ItemDisplay $widget */

use common\modules\catalogs\widgets\item\ItemHeader;
use common\modules\catalogs\widgets\item\ItemContent;
?>

<div class="display-widget default">
    <!-- container -->
    <div class="display-container">
        <div class="header-container row mb-3">
            <?= ItemHeader::widget([
                'background' => $widget->getImage(),
                'backgroundOptions' => [],
                // 'image' => null,
                // 'imageOptions' => [],
                'title' => $widget->getTitle(),
                'buttons' => $widget->getButtons(),
                'description' => $widget->getOptions('description', null, $widget->getModel())
            ]); ?>
        </div>
        <div class="content-container">
            <?= ItemContent::widget([
                'sections' => $widget->getSections(),
                'sidebar' => $widget->getSidebar()
            ]); ?>
        </div>
    </div><!-- /container -->
</div>
