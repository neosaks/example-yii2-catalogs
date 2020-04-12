<?php

/* @var $this yii\web\View */

use common\modules\catalogs\ModuleAsset;
use common\helpers\Framework;

ModuleAsset::register($this);
?>

<?php $this->beginContent(Framework::getMainLayoutFile()); ?>

<div class="catalogs-layout">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <?= $content ?>
            </div>
        </div>
    </div>
</div>

<?php $this->endContent(); ?>
