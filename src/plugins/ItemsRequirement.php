<?php
/**
 * @link
 * @copyright
 * @license
 */

namespace common\modules\catalogs\plugins;

use Yii;
use yii\base\DynamicModel;
use common\modules\catalogs\DataManager;
use common\modules\catalogs\models\Item;
use common\modules\catalogs\models\Catalog;

/**
 * Description.
 *
 * @author Maxim Chichkanov
 */
class ItemsRequirement extends Requirement
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
        set_time_limit(0);

        $transaction = Yii::$app->getDb()->beginTransaction();

        $numberOfMissing = 0;
        $numberOfAdded = 0;

        foreach ($this->requirements() as $requrement) {
            if (isset($requrement['correct']['item'])) {
                if (!Item::find()->where($requrement['correct']['item'])->exists()) {
                    $manager = new DataManager($catalog, new Item());

                    $manager->getItem()->setAttributes($requrement['correct']['item'] + [
                        'catalog_id' => $catalog->id
                    ]);

                    if (!$manager->getItem()->save()) {
                        $numberOfMissing++;
                        continue;
                    }

                    if (isset($requrement['correct']['data'])) {
                        $manager->getModel()->setAttributes($requrement['correct']['data']);
                    }

                    $manager->save() ? $numberOfAdded++ : $numberOfMissing++;
                }
            }
        }

        if ($numberOfMissing) {
            $model->addError('check', "Не удалось добавить $numberOfMissing записи(ей).");
        } else {
            $transaction->commit();
        }

        return !$numberOfMissing;
    }

    /**
     * Description.
     *
     * @param Catalog $catalog
     * @param DynamicModel $model
     * @return boolean
     */
    public function check(Catalog $catalog, DynamicModel $model)
    {
        set_time_limit(0);

        $numberOfMissing = 0;
        $numberOfRecord = 0;

        $query = Item::find();

        foreach ($this->requirements() as $requrement) {
            if (isset($requrement['check']['item'])) {
                $query->orWhere(
                    ['catalog_id' => $catalog->id]
                    +
                    $requrement['check']['item']
                );
                $numberOfRecord++;
            }
        }

        $numberOfMissing = $numberOfRecord - $query->count();

        if ($numberOfMissing) {
            $model->addError('check', "Будет добавлено $numberOfMissing записи(ей).");
        }

        return !$numberOfMissing;
    }
}
