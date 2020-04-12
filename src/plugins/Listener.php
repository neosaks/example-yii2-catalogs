<?php
/**
 * @link
 * @copyright
 * @license
 */

namespace common\modules\catalogs\plugins;

use Yii;
use ReflectionClass;
use yii\base\Component;
use yii\base\ViewContextInterface;
use common\modules\catalogs\models\Catalog;

/**
 * Description.
 *
 * @author Maxim Chichkanov
 */
abstract class Listener extends Component implements ViewContextInterface
{
    /**
     * Description.
     *
     * @return void
     */
    abstract public function register(Catalog $catalog);

    /**
     * Description
     *
     * @param string $view
     * @param array $params
     * @return string
     * @throws InvalidArgumentException
     */
    public function renderPartial($view, $params = [])
    {
        return Yii::$app->getView()->render($view, $params, $this);
    }

    /**
     * Returns the directory containing the view files for this action.
     * The default implementation returns the 'views' subdirectory under the directory containing the action class file.
     * @return string the directory containing the view files for this action.
     */
    public function getViewPath()
    {
        $class = new ReflectionClass($this);

        return dirname($class->getFileName()) . DIRECTORY_SEPARATOR . 'views';
    }
}
