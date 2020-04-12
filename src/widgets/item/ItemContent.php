<?php
/**
 * @link
 * @copyright
 * @license
 */

namespace common\modules\catalogs\widgets\item;

use common\widgets\bootstrap4\Tabs;
use common\widgets\grid\GridWidget;

/**
 * Description
 *
 * @author Maxim Chichkanov
 */
class ItemContent extends \yii\base\Widget
{
    /**
     * @var array
     */
    public $sections = [];

    /**
     * @var string
     */
    public $sidebar = '';

    /**
     * @var array
     */
    public $contentOptions = [];

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->registerAssetBundle();

        return GridWidget::widget([
            'columns' => [
                [
                    'content' => $this->renderSections(),
                    'options' => ['class' => 'content-sections'],
                    'columnOptions' => ['class' => 'col-md-8']
                ],
                [
                    'content' => $this->renderSidebar(),
                    'options' => ['class' => 'content-sidebar'],
                    'columnOptions' => ['class' => 'col-md-4']
                ]
            ],
            'containerOptions' => $this->contentOptions
        ]);
    }

    /**
     * @return string
     */
    public function renderSections()
    {
        return Tabs::widget([
            'items' => $this->sections,
            'navType' => 'nav-pills',
            'options' => ['class' => 'mb-3']
        ]);
    }

    /**
     * @return strnig
     */
    public function renderSidebar()
    {
        return $this->sidebar;
    }

    /**
     * @return void
     */
    public function registerAssetBundle()
    {
    }
}
