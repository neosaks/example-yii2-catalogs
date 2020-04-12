<?php
/**
 * @link
 * @copyright
 * @license
 */

namespace common\modules\catalogs\plugins\countries;

use common\core\editors\EditorWidget;
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
            // description
            [
                'check' => ['description', 'isExists', 'message' => 'Будет создан атрибут "Описание"'],
                'correct' => ['description', 'doCreate', 'message' => 'Не удалось создать атрибут "Описание"',
                    'params' => [
                        'attributes' => [
                            'type' => Attribute::TYPE_TEXT,
                            'label' => 'Описание',
                            'trim' => true,
                            'required' => false
                        ],
                        'options' => [
                            'field' => [
                                'textarea' => true
                            ],
                            'field.widgets' => [
                                [
                                    'class' => EditorWidget::class,
                                    'config' => []
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            [
                'check' => ['description', 'isCompare', 'message' => 'Тип атрибута "Описание" станет текстовым',
                    'params' => [
                        'attributes' => [
                            'type' => Attribute::TYPE_TEXT
                        ]
                    ]
                ],
                'correct' => ['description', 'doUpdate', 'message' => 'Не удалось обновить атрибут "Описание"',
                    'params' => [
                        'attributes' => [
                            'type' => Attribute::TYPE_TEXT
                        ]
                    ]
                ]
            ],
            // iso
            [
                'check' => ['iso', 'isExists', 'message' => 'Будет создан атрибут "ISO Код"'],
                'correct' => ['iso', 'doCreate', 'message' => 'Не удалось создать атрибут "ISO Код"',
                    'params' => [
                        'attributes' => [
                            'type' => Attribute::TYPE_TEXT,
                            'label' => 'ISO Код',
                            'trim' => true,
                            'required' => false
                        ],
                        'options' => [
                            'validation' => [
                                'length' => 2
                            ]
                        ]
                    ]
                ]
            ],
            [
                'check' => ['iso', 'isCompare', 'message' => 'Тип атрибута "ISO Код" станет текстовым',
                    'params' => [
                        'attributes' => [
                            'type' => Attribute::TYPE_TEXT
                        ]
                    ]
                ],
                'correct' => ['iso', 'doUpdate', 'message' => 'Не удалось обновить атрибут "ISO Код"',
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
