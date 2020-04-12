<?php
/**
 * @link
 * @copyright
 * @license
 */

namespace common\modules\catalogs\plugins;

use yii\base\DynamicModel;
use yii\base\InvalidConfigException;
use yii\validators\Validator;
use yii\helpers\ArrayHelper;
use common\modules\catalogs\models\Catalog;
use common\modules\catalogs\models\Attribute;

/**
 * Description.
 *
 * @author Maxim Chichkanov
 */
class AttributesRequirement extends Requirement
{
    /**
     * Description.
     *
     * @return array
     */
    public function requirements()
    {
        return [];
    }

    /**
     * Description.
     *
     * @param Catalog $catalog
     * @param DynamicModel $model
     * @return boolean
     */
    public function correct(Catalog $catalog, DynamicModel $model)
    {
        $this->getValidators()->exchangeArray([]);
        $this->clearErrors();

        $errorMessage = 'Invalid correction rule: a rule must specify both attribute names and corrector type.';

        $attributeModels = Attribute::find()
            ->where(['catalog_id' => $catalog->id])
            ->indexBy('name')
            ->all();

        foreach ($this->requirements() as $rules) {
            if (($check = $this->handle(
                $catalog,
                new AttributesRequirement(),
                $attributeModels,
                $rules['check'],
                $errorMessage
            )) &&
                !$check->validate()
            ) {
                $this->handle($catalog, $this, $attributeModels, $rules['correct'], $errorMessage);
            }
        }

        $result = $this->validate();

        if ($this->hasErrors()) {
            $model->addErrors($this->getErrors());
        }

        return $result;
    }

    /**
     * Description.
     *
     * @param Catalog $catalog
     * @param DynamicModel $model
     * @return boolean
     * @throws InvalidConfigException
     */
    public function check(Catalog $catalog, DynamicModel $model)
    {
        $this->getValidators()->exchangeArray([]);
        $this->clearErrors();

        $errorMessage = 'Invalid validation rule: a rule must specify both attribute names and validator type.';

        $attributeModels = Attribute::find()
            ->where(['catalog_id' => $catalog->id])
            ->indexBy('name')
            ->all();

        foreach ($this->requirements() as $rules) {
            $this->handle($catalog, $this, $attributeModels, $rules['check'], $errorMessage);
        }

        $result = $this->validate();

        if ($this->hasErrors()) {
            $model->addErrors($this->getErrors());
        }

        return $result;
    }

    /**
     * Description.
     *
     * @param string $attribute is the name of the attribute to be validated;
     * @param array $params contains the value of [[params]] that you specify when declaring the inline validation rule;
     * @param \yii\validators\InlineValidator $validator is a reference to related [[InlineValidator]] object.
     * @return void
     * @throws InvalidConfigException
     */
    public function doCreate($attribute, $params, $validator)
    {
        if (!is_array($params['attributes'])) {
            throw new InvalidConfigException();
        }

        $model = new Attribute($params['attributes'] + [
            'catalog_id' => $params['catalog']->id,
            'name' => $attribute
        ]);

        $model->setOptions(ArrayHelper::getValue($params, 'options', []));

        if (!$model->save()) {
            $this->addError($attribute, $validator->message);
        }
    }

    /**
     * Description.
     *
     * @param string $attribute is the name of the attribute to be validated;
     * @param array $params contains the value of [[params]] that you specify when declaring the inline validation rule;
     * @param \yii\validators\InlineValidator $validator is a reference to related [[InlineValidator]] object.
     * @return void
     * @throws InvalidConfigException
     */
    public function doUpdate($attribute, $params, $validator)
    {
        if (!isset($params['attributeModel']) || !$params['attributeModel'] instanceof Attribute) {
            throw new InvalidConfigException();
        }

        if (!is_array($params['attributes'])) {
            throw new InvalidConfigException();
        }

        $params['attributeModel']->setAttributes($params['attributes']);
        $params['attributeModel']->setOptions(ArrayHelper::getValue($params, 'options', []));

        if (!$params['attributeModel']->save()) {
            $this->addError($attribute, $validator->message);
        }
    }

    /**
     * Description.
     *
     * @param string $attribute is the name of the attribute to be validated;
     * @param array $params contains the value of [[params]] that you specify when declaring the inline validation rule;
     * @param \yii\validators\InlineValidator $validator is a reference to related [[InlineValidator]] object.
     * @return void
     * @throws InvalidConfigException
     */
    public function doDelete($attribute, $params, $validator)
    {
        if (!isset($params['attributeModel']) || !$params['attributeModel'] instanceof Attribute) {
            throw new InvalidConfigException();
        }

        if (!$params['attributeModel']->delete()) {
            $this->addError($attribute, $validator->message);
        }
    }

    /**
     * Description.
     *
     * @param string $attribute is the name of the attribute to be validated;
     * @param array $params contains the value of [[params]] that you specify when declaring the inline validation rule;
     * @param \yii\validators\InlineValidator $validator is a reference to related [[InlineValidator]] object.
     * @return void
     * @throws InvalidConfigException
     */
    public function isExists($attribute, $params, $validator)
    {
        if (!isset($params['attributeModel']) || !$params['attributeModel'] instanceof Attribute) {
            $this->addError($attribute, $validator->message);
        }
    }

    /**
     * Description.
     *
     * @param string $attribute is the name of the attribute to be validated;
     * @param array $params contains the value of [[params]] that you specify when declaring the inline validation rule;
     * @param \yii\validators\InlineValidator $validator is a reference to related [[InlineValidator]] object.
     * @return void
     */
    public function isCompare($attribute, $params, $validator)
    {
        if (!isset($params['attributeModel']) || !$params['attributeModel'] instanceof Attribute) {
            return;
        }

        if (!is_array($params['attributes'])) {
            return;
        }

        foreach ($params['attributes'] as $compareAttributeName => $compareAttributeValue) {
            if (!$params['attributeModel']->hasAttribute($compareAttributeName)
                || $params['attributeModel']->$compareAttributeName != $compareAttributeValue) {
                $this->addError($attribute, $validator->message);
                return;
            }
        }
    }

    /**
     * Description.
     *
     * @param Catalog $catalog
     * @param DynamicModel $model
     * @param Attribute[] $attributeModels
     * @param array $rule
     * @param string $errorMessage
     * @return DynamicModel
     * @throws InvalidConfigException
     */
    protected function handle($catalog, $model, $attributeModels, $rule, $errorMessage)
    {
        if (!is_array($rule) || !isset($rule[0], $rule[1])) {
            throw new InvalidConfigException($errorMessage);
        }

        if (!$model->hasAttribute($rule[0])) {
            $model->defineAttribute($rule[0]);
        }

        $attributeModel = ArrayHelper::getValue($attributeModels, $rule[0]);

        if (isset(Validator::$builtInValidators[$rule[1]])) {
            $model->addRule($rule[0], $rule[1], array_slice($rule, 2));
        } else {
            $validator = Validator::createValidator(
                $rule[1],
                $model,
                (array) $rule[0],
                array_merge_recursive(
                    array_slice($rule, 2),
                    [
                        'skipOnError' => false,
                        'skipOnEmpty' => false,
                        'params' => [
                            'attributeModel' => $attributeModel,
                            'catalog' => $catalog
                        ]
                    ]
                )
            );

            $model->getValidators()->append($validator);
        }

        return $model;
    }
}
