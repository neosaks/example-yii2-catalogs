<?php
/**
 * @link
 * @copyright
 * @license
 */

namespace common\modules\catalogs\plugins\countries;

use yii\web\AssetBundle;

/**
 * Description.
 *
 * @author Maxim Chichkanov
 */
class CardBackgroundAsset extends AssetBundle
{
    public $sourcePath = '@common/modules/catalogs/plugins/countries/assets';
    public $css = [
        'css/countries-card-backgound.css'
    ];
    public $js = [
    ];
    public $depends = [
    ];
}
