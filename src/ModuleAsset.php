<?php
/**
 * @link
 * @copyright
 * @license
 */

namespace common\modules\catalogs;

use yii\web\AssetBundle;

/**
 * Description.
 *
 * @author Maxim Chichkanov
 */
class ModuleAsset extends AssetBundle
{
    public $sourcePath = '@common/modules/catalogs/assets';
    public $css = [
        'css/catalogs-module.css'
    ];
    public $js = [
    ];
    public $depends = [
    ];
}
