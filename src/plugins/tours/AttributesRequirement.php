<?php
/**
 * @link
 * @copyright
 * @license
 */

namespace common\modules\catalogs\plugins\tours;

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
            // night_count
            [
                'check' => ['night_count', 'isExists', 'message' => 'Будет создан атрибут "Количество ночей"'],
                'correct' => ['night_count', 'doCreate', 'message' => 'Не удалось создать атрибут "Количество ночей"',
                    'params' => [
                        'attributes' => [
                            'type' => Attribute::TYPE_NUMBER,
                            'label' => 'Количество ночей',
                            'trim' => true,
                            'required' => true
                        ]
                    ]
                ]
            ],
            [
                'check' => ['night_count', 'isCompare', 'message' => 'Атрибут "Количество ночей" станет обязательным',
                    'params' => [
                        'attributes' => [
                            'required' => true
                        ]
                    ]
                ],
                'correct' => ['night_count', 'doUpdate', 'message' => 'Не удалось обновить атрибут "Количество ночей"',
                    'params' => [
                        'attributes' => [
                            'required' => true
                        ]
                    ]
                ]
            ],
            [
                'check' => ['night_count', 'isCompare', 'message' => 'Тип атрибута "Количество ночей" станет числовым',
                    'params' => [
                        'attributes' => [
                            'type' => Attribute::TYPE_NUMBER
                        ]
                    ]
                ],
                'correct' => ['night_count', 'doUpdate', 'message' => 'Не удалось обновить атрибут "Количество ночей"',
                    'params' => [
                        'attributes' => [
                            'type' => Attribute::TYPE_NUMBER
                        ]
                    ]
                ]
            ],
            // formula
            [
                'check' => ['formula', 'isExists', 'message' => 'Будет создан атрибут "Формула"'],
                'correct' => ['formula', 'doCreate', 'message' => 'Не удалось создать атрибут "Формула"',
                    'params' => [
                        'attributes' => [
                            'type' => Attribute::TYPE_TEXT,
                            'label' => 'Формула',
                            'trim' => true
                        ]
                    ]
                ]
            ],
            [
                'check' => ['formula', 'isCompare', 'message' => 'Тип атрибута "Формула" станет "Текст"',
                    'params' => [
                        'attributes' => [
                            'type' => Attribute::TYPE_TEXT
                        ]
                    ]
                ],
                'correct' => ['formula', 'doUpdate', 'message' => 'Не удалось обновить атрибут "Формула"',
                    'params' => [
                        'attributes' => [
                            'type' => Attribute::TYPE_TEXT
                        ]
                    ]
                ]
            ],
            // fix price
            [
                'check' => ['fix_price', 'isExists', 'message' => 'Будет создан атрибут "Фиксированная цена"'],
                'correct' => ['fix_price', 'doCreate', 'message' => 'Не удалось создать атрибут "Фиксированная цена"',
                    'params' => [
                        'attributes' => [
                            'type' => Attribute::TYPE_CURRENCY,
                            'label' => 'Фиксированная цена',
                            'trim' => true
                        ]
                    ]
                ]
            ],
            [
                'check' => ['fix_price', 'isCompare', 'message' => 'Тип атрибута "Фиксированная цена" станет "Денежная единица"',
                    'params' => [
                        'attributes' => [
                            'type' => Attribute::TYPE_CURRENCY
                        ]
                    ]
                ],
                'correct' => ['fix_price', 'doUpdate', 'message' => 'Не удалось обновить атрибут "Фиксированная цена"',
                    'params' => [
                        'attributes' => [
                            'type' => Attribute::TYPE_CURRENCY
                        ]
                    ]
                ]
            ],
            // min price
            [
                'check' => ['min_price', 'isExists', 'message' => 'Будет создан атрибут "Минимальная цена"'],
                'correct' => ['min_price', 'doCreate', 'message' => 'Не удалось создать атрибут "Минимальная цена"',
                    'params' => [
                        'attributes' => [
                            'type' => Attribute::TYPE_CURRENCY,
                            'label' => 'Минимальная цена',
                            'trim' => true
                        ]
                    ]
                ]
            ],
            [
                'check' => ['min_price', 'isCompare', 'message' => 'Тип атрибута "Минимальная цена" станет "Денежная единица"',
                    'params' => [
                        'attributes' => [
                            'type' => Attribute::TYPE_CURRENCY
                        ]
                    ]
                ],
                'correct' => ['min_price', 'doUpdate', 'message' => 'Не удалось обновить атрибут "Минимальная цена"',
                    'params' => [
                        'attributes' => [
                            'type' => Attribute::TYPE_CURRENCY
                        ]
                    ]
                ]
            ],
            // day price
            [
                'check' => ['day_price', 'isExists', 'message' => 'Будет создан атрибут "Цена за сутки"'],
                'correct' => ['day_price', 'doCreate', 'message' => 'Не удалось создать атрибут "Цена за сутки"',
                    'params' => [
                        'attributes' => [
                            'type' => Attribute::TYPE_CURRENCY,
                            'label' => 'Цена за сутки',
                            'trim' => true
                        ]
                    ]
                ]
            ],
            [
                'check' => ['day_price', 'isCompare', 'message' => 'Тип атрибута "Цена за сутки" станет "Денежная единица"',
                    'params' => [
                        'attributes' => [
                            'type' => Attribute::TYPE_CURRENCY
                        ]
                    ]
                ],
                'correct' => ['day_price', 'doUpdate', 'message' => 'Не удалось обновить атрибут "Цена за сутки"',
                    'params' => [
                        'attributes' => [
                            'type' => Attribute::TYPE_CURRENCY
                        ]
                    ]
                ]
            ],
            // persons min
            [
                'check' => ['persons_min', 'isExists',
                    'message' => 'Будет создан атрибут "Минимальное количество человек"'
                ],
                'correct' => ['persons_min', 'doCreate',
                    'message' => 'Не удалось создать атрибут "Минимальное количество человек"',
                    'params' => [
                        'attributes' => [
                            'type' => Attribute::TYPE_NUMBER,
                            'label' => 'Минимальное количество человек',
                            'trim' => true,
                            'required' => true
                        ]
                    ]
                ]
            ],
            [
                'check' => ['persons_min', 'isCompare',
                    'message' => 'Атрибут "Минимальное количество человек" станет обязательным',
                    'params' => [
                        'attributes' => [
                            'required' => true
                        ]
                    ]
                ],
                'correct' => ['persons_min', 'doUpdate',
                    'message' => 'Не удалось обновить атрибут "Минимальное количество человек"',
                    'params' => [
                        'attributes' => [
                            'required' => true
                        ]
                    ]
                ]
            ],
            [
                'check' => ['persons_min', 'isCompare',
                    'message' => 'Тип атрибута "Минимальное количество человек" станет числовым',
                    'params' => [
                        'attributes' => [
                            'type' => Attribute::TYPE_NUMBER
                        ]
                    ]
                ],
                'correct' => ['persons_min', 'doUpdate',
                    'message' => 'Не удалось обновить атрибут "Минимальное количество человек"',
                    'params' => [
                        'attributes' => [
                            'type' => Attribute::TYPE_NUMBER
                        ]
                    ]
                ]
            ],
            // footer text
            [
                'check' => ['footer_text', 'isExists',
                    'message' => 'Будет создан атрибут "Подвал карточки"'
                ],
                'correct' => ['footer_text', 'doCreate',
                    'message' => 'Не удалось создать атрибут "Подвал карточки"',
                    'params' => [
                        'attributes' => [
                            'type' => Attribute::TYPE_TEXT,
                            'label' => 'Подвал карточки',
                            'trim' => true,
                            'required' => false
                        ]
                    ]
                ]
            ],
            [
                'check' => ['footer_text', 'isCompare',
                    'message' => 'Тип атрибута "Подвал карточки" станет текстовым',
                    'params' => [
                        'attributes' => [
                            'type' => Attribute::TYPE_TEXT
                        ]
                    ]
                ],
                'correct' => ['footer_text', 'doUpdate',
                    'message' => 'Не удалось обновить атрибут "Подвал карточки"',
                    'params' => [
                        'attributes' => [
                            'type' => Attribute::TYPE_TEXT
                        ]
                    ]
                ]
            ],
            // description
            [
                'check' => ['description', 'isExists',
                    'message' => 'Будет создан атрибут "Описание"'
                ],
                'correct' => ['description', 'doCreate',
                    'message' => 'Не удалось создать атрибут "Описание"',
                    'params' => [
                        'attributes' => [
                            'type' => Attribute::TYPE_TEXT,
                            'label' => 'Описание',
                            'trim' => true,
                            'required' => true
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
                            ],
                            'is.section' => true
                        ]
                    ]
                ]
            ],
            [
                'check' => ['description', 'isCompare',
                    'message' => 'Атрибут "Описание" станет обязательным',
                    'params' => [
                        'attributes' => [
                            'required' => true
                        ]
                    ]
                ],
                'correct' => ['description', 'doUpdate',
                    'message' => 'Не удалось обновить атрибут "Описание"',
                    'params' => [
                        'attributes' => [
                            'required' => true
                        ]
                    ]
                ]
            ],
            [
                'check' => ['description', 'isCompare',
                    'message' => 'Тип атрибута "Описание" станет текстовым',
                    'params' => [
                        'attributes' => [
                            'type' => Attribute::TYPE_TEXT
                        ]
                    ]
                ],
                'correct' => ['description', 'doUpdate',
                    'message' => 'Не удалось обновить атрибут "Описание"',
                    'params' => [
                        'attributes' => [
                            'type' => Attribute::TYPE_TEXT
                        ]
                    ]
                ]
            ],
            // tour program
            [
                'check' => ['tour_program', 'isExists',
                    'message' => 'Будет создан атрибут "Программа тура"'
                ],
                'correct' => ['tour_program', 'doCreate',
                    'message' => 'Не удалось создать атрибут "Программа тура"',
                    'params' => [
                        'attributes' => [
                            'type' => Attribute::TYPE_TEXT,
                            'label' => 'Программа тура',
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
                            ],
                            'is.section' => true
                        ]
                    ]
                ]
            ],
            [
                'check' => ['tour_program', 'isCompare',
                    'message' => 'Тип атрибута "Программа тура" станет текстовым',
                    'params' => [
                        'attributes' => [
                            'type' => Attribute::TYPE_TEXT
                        ]
                    ]
                ],
                'correct' => ['tour_program', 'doUpdate',
                    'message' => 'Не удалось обновить атрибут "Программа тура"',
                    'params' => [
                        'attributes' => [
                            'type' => Attribute::TYPE_TEXT
                        ]
                    ]
                ]
            ],
            // images
            [
                'check' => ['images', 'isExists',
                    'message' => 'Будет создан атрибут "Изображения"'
                ],
                'correct' => ['images', 'doCreate',
                    'message' => 'Не удалось создать атрибут "Изображения"',
                    'params' => [
                        'attributes' => [
                            'type' => Attribute::TYPE_IMAGE,
                            'label' => 'Изображения',
                            'trim' => false,
                            'required' => false
                        ]
                    ]
                ]
            ],
            [
                'check' => ['images', 'isCompare',
                    'message' => 'Тип атрибута "Изображения" будет адаптирован для загрузки файлов',
                    'params' => [
                        'attributes' => [
                            'type' => Attribute::TYPE_IMAGE
                        ]
                    ]
                ],
                'correct' => ['images', 'doUpdate',
                    'message' => 'Не удалось обновить атрибут "Изображения"',
                    'params' => [
                        'attributes' => [
                            'type' => Attribute::TYPE_IMAGE
                        ]
                    ]
                ]
            ]
        ];
    }
}
