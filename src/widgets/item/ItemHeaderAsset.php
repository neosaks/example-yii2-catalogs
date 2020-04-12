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
class ItemHeaderAsset extends AssetBundle
{
    public $sourcePath = '@common/modules/catalogs/widgets/item/assets';
    public $css = [
        'css/item-header.css'
    ];
    public $js = [
        'js/jquery.scroolly.min.js',
        'js/item-header.js'
    ];
    public $depends = [
        'yii\web\JqueryAsset'
    ];
}
