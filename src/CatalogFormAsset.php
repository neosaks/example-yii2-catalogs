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
class CatalogFormAsset extends AssetBundle
{
    public $sourcePath = '@common/modules/catalogs/assets';
    public $css = [
    ];
    public $js = [
        'js/catalog-form.js'
    ];
    public $depends = [
        'yii\web\JqueryAsset'
    ];
}
