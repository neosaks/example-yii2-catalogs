<?php
/**
 * @link
 * @copyright
 * @license
 */

namespace common\modules\catalogs\widgets\images;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

/**
 * Description.
 *
 * @author Maxim Chichkanov
 */
class ImagesAsset extends AssetBundle
{
    public $sourcePath = '@common/modules/catalogs/widgets/images/assets';
    public $css = [];
    public $js = [
        'js/images.js'
    ];
    public $depends = [
        JqueryAsset::class
    ];
}
