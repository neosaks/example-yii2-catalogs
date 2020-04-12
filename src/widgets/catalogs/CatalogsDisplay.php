<?php
/**
 * @link
 * @copyright
 * @license
 */

namespace common\modules\catalogs\widgets\catalogs;

use ReflectionClass;
use Yii;
use yii\base\ViewNotFoundException;
use yii\data\DataProviderInterface;
use common\helpers\Framework;
use common\widgets\display\DisplayWidget;
use common\modules\catalogs\Module;
use common\modules\catalogs\models\Catalog;
use common\modules\catalogs\models\CatalogSearch;

/**
 * Description
 *
 * @author Maxim Chichkanov
 */
class CatalogsDisplay extends DisplayWidget
{
    /**
     * {@inheritdoc}
     */
    public $template = 'masonry';

    /**
     * @var array
     */
    public $condition = ['status' => Catalog::STATUS_ACTIVE];

    /**
     * @var array
     */
    public $params = [];

    /**
     * @var string|null
     */
    protected $viewPath;

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        try {
            return parent::run();
        } catch (ViewNotFoundException $error) {
            $this->changeViewPath();
            return parent::run();
        }
    }

    /**
     * @return DataProviderInterface
     */
    public function getDataProvider()
    {
        if (!$this->dataProvider instanceof DataProviderInterface) {
            $params = $this->params ?? Yii::$app->request->queryParams;
            $condition = $this->condition;

            $this->dataProvider = (new CatalogSearch())->search($params, $condition);
        }

        return $this->dataProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function renderPager()
    {
        if (is_array($this->pager) && !$this->pager) {
            $this->pager['listOptions'] = [
                'class' => 'pagination justify-content-center'
            ];
        }

        return parent::renderPager();
    }

    /**
     * @param object $model
     * @return array|null
     */
    public function getDeatilUrl($model)
    {
        if (!is_callable($this->detailUrl)) {
            $this->detailUrl = function ($model) {
                return Framework::composeUrl(Module::class, 'default/view', ['id' => $model->id]);
            };
        }

        return parent::getDeatilUrl($model);
    }

    /**
     * {@inheritdoc}
     */
    public function getViewPath()
    {
        return $this->viewPath ? $this->viewPath : parent::getViewPath();
    }

    /**
     * Description.
     *
     * @return void
     */
    protected function changeViewPath()
    {
        $classFilename = (new ReflectionClass(parent::class))->getFileName();
        $this->viewPath = dirname($classFilename) . DIRECTORY_SEPARATOR . 'views';
    }
}
