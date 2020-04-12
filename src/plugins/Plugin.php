<?php
/**
 * @link
 * @copyright
 * @license
 */

namespace common\modules\catalogs\plugins;

use Yii;
use yii\base\Component;
use yii\base\Model;
use yii\base\DynamicModel;
use yii\validators\DefaultValueValidator;
use common\helpers\Framework;
use common\modules\catalogs\models\Catalog;
use common\modules\catalogs\models\Plugin as PluginConnection;

/**
 * Description.
 *
 * @author Maxim Chichkanov
 */
abstract class Plugin extends Component
{
    /**
     * Description.
     *
     * @return Requirement[]
     */
    public function requirements()
    {
        return [];
    }

    /**
     * Description.
     *
     * @return Listener[]
     */
    public function listeners()
    {
        return [];
    }

    /**
     * Description.
     *
     * @return Action[]
     */
    public function actions()
    {
        return [];
    }

    /**
     * Description.
     *
     * @return string
     */
    public function getName()
    {
        return '';
    }

    /**
     * Description.
     *
     * @return string
     */
    public function getDescription()
    {
        return '';
    }

    /**
     * Description.
     *
     * @return string
     */
    public function getVersion()
    {
        return '';
    }

    /**
     * Description.
     *
     * @return string
     */
    public function getAuthor()
    {
        return '';
    }

    /**
     * Description.
     *
     * @return string
     */
    public function getCopyright()
    {
        return '';
    }

    /**
     * Description.
     *
     * @param Catalog $catalog
     * @return DynamicModel
     */
    public function disconnect(Catalog $catalog)
    {
        $model = new DynamicModel($catalog->getAttributes());

        if (!$catalog->isConnected($this)) {
            $model->addError('connection', 'The plugin is not connected to the catalog.');
            $model->defineAttribute('connection', null);

            return $model;
        }

        $connection = $catalog->getConnection($this);

        if (!$connection->delete()) {
            $connection->addError('connection', 'Failed to disconnect plugin.');
            $model->defineAttribute('connection', $connection);
        }

        return $model;
    }

    /**
     * Description.
     *
     * @param Catalog $catalog
     * @return DynamicModel
     */
    public function connect(Catalog $catalog)
    {
        if ($catalog->isConnected($this)) {
            $model = new DynamicModel($catalog->getAttributes());

            $model->addError('connection', 'The plugin is already connected to the catalog.');
            $model->defineAttribute('connection', $catalog->getConnection($this));

            return $model;
        }

        $transaction = Yii::$app->getDb()->beginTransaction();

        $model = $this->correct($catalog);

        if ($model->hasErrors()) {
            return $model;
        }
        
        $connection = new PluginConnection([
            'class_name' => static::class,
            'name' => $this->getName()
        ]);

        $this->applyDefaultValues($connection);

        $connection->link('catalog', $catalog);

        if ($connection->getIsNewRecord()) {
            $model->addError('connection', 'Failed to save plugin attachment information.');
        }

        if (!$model->hasErrors()) {
            $transaction->commit();
        }

        $model->defineAttribute('connection', $connection);

        return $model;
    }

    /**
     * Description.
     *
     * @param Catalog $catalog
     * @return void
     */
    public function run(Catalog $catalog)
    {
        foreach ($this->listeners() as $listener) {
            $listener->register($catalog);
        }
    }

    /**
     * Description.
     *
     * @param Catalog $catalog
     * @return DynamicModel
     */
    public function correct(Catalog $catalog)
    {
        $model = new DynamicModel($catalog->getAttributes());

        foreach ($this->requirements() as $requirement) {
            $requirement->correct($catalog, $model);
        }

        return $model;
    }

    /**
     * Description.
     *
     * @param Catalog $catalog
     * @return DynamicModel
     */
    public function check(Catalog $catalog)
    {
        $model = new DynamicModel($catalog->getAttributes());

        foreach ($this->requirements() as $requirement) {
            $requirement->check($catalog, $model);
        }

        return $model;
    }

    /**
     * Description
     *
     * @return string
     */
    public function getId()
    {
        return Framework::getComponentId(static::class, Yii::$app->controller->module);
    }

    /**
     * Description
     *
     * @param Model
     * @return boolean
     */
    protected function applyDefaultValues(Model $model)
    {
        $validators = $model->getValidators();
        $attributeNames = [];

        foreach ($validators as $validator) {
            if ($validator instanceof DefaultValueValidator) {
                array_push($attributeNames, ...$validator->getAttributeNames());
            }
        }

        return $model->validate($attributeNames);
    }
}
