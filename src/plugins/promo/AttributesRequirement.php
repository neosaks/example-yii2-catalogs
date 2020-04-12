<?php
/**
 * @link
 * @copyright
 * @license
 */

namespace common\modules\catalogs\plugins\promo;

use common\modules\catalogs\models\Attribute;
use common\modules\catalogs\plugins\AttributesRequirement as BaseAttributesRequirement;

/**
 * Description.
 *
 * @author Maxim Chichkanov
 */
class AttributesRequirement extends BaseAttributesRequirement
{
    /**
     * Description.
     *
     * @return array
     */
    public function requirements()
    {
        return [
            // footer text
            [
                'check' => ['vegas_url', 'isExists',
                    'message' => 'Будет создан атрибут "Ссылка для Vegas"'
                ],
                'correct' => ['vegas_url', 'doCreate',
                    'message' => 'Не удалось создать атрибут "Ссылка для Vegas"',
                    'params' => [
                        'attributes' => [
                            'type' => Attribute::TYPE_TEXT,
                            'label' => 'Ссылка для Vegas',
                            'trim' => true,
                            'required' => false
                        ]
                    ]
                ]
            ],
            [
                'check' => ['vegas_url', 'isCompare',
                    'message' => 'Тип атрибута "Ссылка для Vegas" станет текстовым',
                    'params' => [
                        'attributes' => [
                            'type' => Attribute::TYPE_TEXT
                        ]
                    ]
                ],
                'correct' => ['vegas_url', 'doUpdate',
                    'message' => 'Не удалось обновить атрибут "Ссылка для Vegas"',
                    'params' => [
                        'attributes' => [
                            'type' => Attribute::TYPE_TEXT
                        ]
                    ]
                ]
            ]
        ];
    }
}
