<?php
/**
 * @link
 * @copyright
 * @license
 */

namespace common\modules\catalogs\plugins\tours\forms;

use common\modules\catalogs\plugins\tours\models\Person;

/**
 * Customer
 *
 * @author Maxim Chichkanov
 */
class Customer extends Person
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return array_merge_recursive(parent::rules(), [
            ['email', 'required'],
            ['phone', 'required'],
        ]);
    }
}
