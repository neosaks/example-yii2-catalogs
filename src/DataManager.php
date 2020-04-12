<?php
/**
 * @link
 * @copyright
 * @license
 */

namespace common\modules\catalogs;

use Yii;
use yii\base\InvalidConfigException;
use yii\base\InvalidArgumentException;
use yii\widgets\ActiveField;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\core\DynamicModel;
use common\modules\catalogs\models\Attribute;
use common\modules\catalogs\models\Catalog;
use common\modules\catalogs\models\Item;
use common\modules\catalogs\models\Data;

/**
 * Description.
 *
 * @author Maxim Chichkanov
 */
class DataManager
{
    /**
     * @var Catalog
     */
    private $catalog;

    /**
     * @var Item
     */
    private $item;

    /**
     * @var Attribute[]
     */
    private $attributes;

    /**
     * @var Data[]
     */
    private $values;

    /**
     * @var DynamicModel
     */
    private $model;

    /**
     * @var array
     */
    private $loadData = [];

    /**
     * Description.
     *
     * @param Catalog $catalog
     * @param Item $item
     * @return void
     */
    public function __construct(Catalog $catalog, Item $item)
    {
        if ($catalog->getIsNewRecord()) {
            throw new InvalidConfigException();
        }

        $this->catalog = $catalog;
        $this->item = $item;
    }

    /**
     * Description.
     *
     * @return Catalog
     */
    public function getCatalog()
    {
        return $this->catalog;
    }

    /**
     * Description.
     *
     * @return Item
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * Description.
     *
     * @return DynamicModel
     */
    public function getModel()
    {
        if ($this->model instanceof DynamicModel) {
            return $this->model;
        }

        $dataset = $this->getValues();
        $model = new DynamicModel();

        foreach ($this->getAttributes() as $attribute) {
            $value = isset($dataset[$attribute->name])
                ? $dataset[$attribute->name]->value : null;

            $model->defineAttribute($attribute->name, $value);
            $model->defineLabel($attribute->name, $attribute->label);
            CatalogHelper::setAttributeRules($attribute, $model);
        }

        return $this->model = $model;
    }

    /**
     * Description.
     *
     * @param array $data
     * @return boolean
     */
    public function load($data)
    {
        $this->loadData = $data;
        return $this->getModel()->load($data);
    }

    /**
     * Description.
     *
     * @return boolean
     * @throws InvalidConfigException
     */
    public function save()
    {
        $model = $this->getModel();

        if (!$model->validate()) {
            return false;
        }

        $attributes = $this->getAttributes();
        $dataset = $this->getValues();
        $item = $this->getItem();

        if ($item->getIsNewRecord()) {
            throw new InvalidConfigException();
        }

        foreach ($model->getAttributes() as $attribute => $value) {
            if (isset($attributes[$attribute])) {
                $attribute = $attributes[$attribute];

                /** @var Data $data */
                $data = ArrayHelper::getValue($dataset, $attribute->name, new Data());

                if (!$data->format) {
                    CatalogHelper::setDataFormat($attribute, $data);
                }

                $data->attribute_id = $attribute->id;
                $data->item_id = $item->id;
                $data->value = $value;

                if (CatalogHelper::isFileAttribute($attribute)) {
                    // $fileManager = new FileManager($attribute, $data);
                    // $fileManager->load($this->loadData);
                    // $fileManager->upload($this);
                } elseif (CatalogHelper::isImageAttribute($attribute)) {
                    $imageManager = new ImageManager($attribute, $data);
                    $imageManager->load($this->loadData);
                    $imageManager->upload($this);
                } else {
                    if ($attribute->unique && $this->isExists($attribute, $value)) {
                        $model->addError($attribute->name, Yii::t('yii', '{attribute} is invalid.'));
                        continue;
                    }

                    if (!$data->save()) {
                        $model->addErrors([
                            $attribute->name => $data->getErrors($attribute->name)
                        ]);

                        continue;
                    }
                }
            }
        }

        return !$model->hasErrors();
    }

    /**
     * Description.
     *
     * @param ActiveForm $form
     * @param Attribute $attribute
     * @return ActiveField
     */
    public function configureField(Attribute $attribute, ActiveField $field)
    {
        if (!$this->hasAttribute($attribute)) {
            throw new InvalidArgumentException();
        }

        CatalogHelper::configureField($attribute, $field);

        return $field;
    }

    /**
     * Description.
     *
     * @return boolean
     */
    public function deleteItem()
    {
        if ($this->getItem()->getIsNewRecord()) {
            return false;
        }

        $transaction = Yii::$app->getDb()->beginTransaction();

        foreach ($this->getValues() as $data) {
            if ($data->delete() === false) {
                $transaction->rollBack();

                return false;
            }
        }

        if ($this->getItem()->delete()) {
            $transaction->commit();

            return true;
        }

        $transaction->rollBack();

        return false;
    }

    /**
     * Description.
     *
     * @param array $condition
     * @return Attribute[]
     */
    public function findAttributes($condition)
    {
        $result = [];
        foreach ($this->getAttributes() as $attribute) {
            foreach ($condition as $name => $value) {
                if ($name === 'options' && is_array($value)
                    && $this->checkOptions($attribute, $value)) {
                    continue 1;
                }

                if (!isset($attribute[$name])) {
                    continue 2;
                }

                if ($attribute[$name] !== $value) {
                    continue 2;
                }
            }
            $result[] = $attribute;
        }
        return $result;
    }

    /**
     * Description.
     *
     * @param array $condition
     * @return Attribute|null
     */
    public function findAttribute($condition)
    {
        foreach ($this->getAttributes() as $attribute) {
            foreach ($condition as $name => $value) {
                if ($name === 'options' && is_array($value)
                    && $this->checkOptions($attribute, $value)) {
                    continue 1;
                }

                if (!isset($attribute[$name])) {
                    continue 2;
                }

                if ($attribute[$name] !== $value) {
                    continue 2;
                }
            }
            return $attribute;
        }
    }

    /**
     * Description.
     *
     * @return Attribute[]
     */
    public function getAttributes()
    {
        if ($this->attributes === null) {
            $this->attributes = Attribute::find()
                ->where(['catalog_id' => $this->getCatalog()->id])
                ->orderBy('position')
                ->indexBy('name')
                ->active()
                ->all();
        }

        return $this->attributes;
    }

    /**
     * Description.
     *
     * @param Attribute|string $attribute
     * @return boolean
     */
    public function hasAttribute($attribute)
    {
        $attributes = $this->getAttributes();

        if ($attribute instanceof Attribute) {
            return isset($attributes[$attribute->name]) &&
                $attributes[$attribute->name]->id === $attribute->id;
        } elseif (is_string($attribute)) {
            return isset($attributes[$attribute]);
        }

        return false;
    }

    /**
     * Description.
     *
     * @return Data[]
     */
    public function getValues()
    {
        /** @todo refactoring */

        // if ($this->getItem()->getIsNewRecord()) {
        //     return [];
        // }

        // if ($this->values === null) {
        //     $this->values = [];

        //     $values = Data::find()
        //         ->where(['item_id' => $this->getItem()->id])
        //         ->with('attribute')
        //         ->active()
        //         ->all();

        //     foreach ($values as $value) {
        //         $attribute = $value->attribute->name;
        //         if (isset($this->values[$attribute])) {
        //             if (!is_array($this->values[$attribute])) {
        //                 $this->values[$attribute] = [$this->values[$attribute]];
        //             }
        //             $this->values[$attribute][] = $value;
        //         } else {
        //             $this->values[$attribute] = $value;
        //         }
        //     }
        // }

        // return $this->values;

        if ($this->getItem()->getIsNewRecord()) {
            return [];
        }

        if ($this->values === null) {
            $this->values = Data::find()
                ->where(['item_id' => $this->getItem()->id])
                ->with('attribute')
                ->indexBy('attribute.name')
                ->orderBy('position')
                ->active()
                ->all();
        }

        return $this->values;
    }

    /**
     * Description.
     *
     * @param Attribute|string $attribute
     * @return boolean
     */
    public function hasValue($attribute)
    {
        $dataset = $this->getValues();

        if ($this->hasAttribute($attribute)) {
            if ($attribute instanceof Attribute) {
                return isset($dataset[$attribute->name]);
            } elseif (is_string($attribute)) {
                return isset($dataset[$attribute]);
            }
        }

        return false;
    }

    /**
     * Description.
     *
     * @param Attribute|string
     * @return Data[]|Data|null
     */
    public function getValue($attribute)
    {
        $dataset = $this->getValues();

        if ($this->hasAttribute($attribute)) {
            if ($attribute instanceof Attribute
                && isset($dataset[$attribute->name])) {
                return $dataset[$attribute->name];
            } elseif (is_string($attribute)
                && isset($dataset[$attribute])) {
                return $dataset[$attribute];
            }
        }
    }

    /**
     * @param Attribute $attribute
     * @param array $condition
     * @return boolean
     */
    protected function checkOptions(Attribute $attribute, $condition)
    {
        foreach ($condition as $key => $value) {
            if (is_integer($key)) {
                if (!$attribute->hasOptions($value)) {
                    return false;
                }
            } else {
                if (!$attribute->hasOptions($key)) {
                    return false;
                }

                if ($attribute->getOptions($key) !== $value) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Description.
     *
     * @param Attribute $attribute
     * @param mixed $value
     * @return boolean
     */
    protected function isExists(Attribute $attribute, $value)
    {
        return Data::find()->where([
            'catalog_id' => $this->getCatalog()->id,
            'attribute_id' => $attribute->id,
            'value' => $value
        ])->active()->exists();
    }
}
