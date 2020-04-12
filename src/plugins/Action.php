<?php
/**
 * @link
 * @copyright
 * @license
 */

namespace common\modules\catalogs\plugins;

use Yii;
use ReflectionClass;
use yii\base\InvalidArgumentException;
use yii\base\ViewContextInterface;

/**
 * Description.
 *
 * @author Maxim Chichkanov
 */
abstract class Action extends \yii\base\Action implements ViewContextInterface
{
    /**
     * Description
     *
     * @param string $view
     * @param array $params
     * @return string
     * @throws InvalidArgumentException
     */
    public function render($view, $params = [])
    {
        $content = $this->controller->getView()->render($view, $params, $this);
        return $this->controller->renderContent($content);
    }

    /**
     * Description
     *
     * @param string $view
     * @param array $params
     * @return string
     * @throws InvalidArgumentException
     */
    public function renderFile($view, $params = [])
    {
        $content = $this->controller->getView()->renderFile($view, $params, $this);
        return $this->controller->renderContent($content);
    }

    /**
     * Description
     *
     * @param string $view
     * @param array $params
     * @return string
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
