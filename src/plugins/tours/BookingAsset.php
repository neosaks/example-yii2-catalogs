<?php
/**
 * @link
 * @copyright
 * @license
 */

namespace common\modules\catalogs\plugins\tours;

use kartik\daterange\DateRangePickerAsset;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;
use yii\widgets\ActiveFormAsset;

/**
 * Description.
 *
 * @author Maxim Chichkanov
 */
class BookingAsset extends AssetBundle
{
    public $sourcePath = '@common/modules/catalogs/plugins/tours/assets';
    public $css = [
    ];
    public $js = [
        'js/booking.js'
    ];
    public $depends = [
        JqueryAsset::class,
        ActiveFormAsset::class,
        DateRangePickerAsset::class
    ];
}
