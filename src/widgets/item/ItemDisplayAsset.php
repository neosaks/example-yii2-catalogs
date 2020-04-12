<?php
/**
 * @link
 * @copyright
 * @license
 */

namespace common\modules\catalogs\widgets\item;

use yii\web\AssetBundle;

/**
 * Description.
 *
 * @author Maxim Chichkanov
 */
class ItemDisplayAsset extends AssetBundle
{
    public $sourcePath = '@common/modules/catalogs/widgets/item/assets';
    public $css = [
        'css/item-display.css'
    ];
    public $js = [
        'js/item-display.js'
    ];
}
