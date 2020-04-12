<?php
/**
 * @link
 * @copyright
 * @license
 */

namespace common\modules\catalogs;

use Yii;
use yii\base\Event;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use common\interfaces\InstallInterface;
use common\modules\rbac\RbacManager;
use common\modules\catalogs\models\Catalog;
use common\modules\catalogs\models\Plugin as Model;
use common\modules\catalogs\plugins\Plugin as Plugin;

/**
 * Description.
 *
 * @author Maxim Chichkanov
 */
class Module extends \yii\base\Module implements InstallInterface
{
    /**
     * {@inheritdoc}
     */
    public $layout = 'main';

    /**
     * @var string
     */
    public $workPath = '@common/uploads/imperavi/files';

    /**
     * @var string
     */
    public $workUrl;

    /**
     * @var array
     * @see \Yii::createObject()
     */
    public $uploader = [
        'class' => 'common\core\uploader\Uploader',
        'directory' => '@common/uploads/catalogs/images',
        'extensions' => 'png, jpg, jpeg',
        // 'maxHeight' => 5000,
        // 'minHeight' => 100,
        // 'maxWidth' => 5000,
        // 'minWidth' => 100
    ];

    /**
     * @var Plugin[]
     */
    protected $plugins = [];

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        // requirements

        if (!Yii::$app->assetManager->linkAssets) {
            throw new InvalidConfigException(
                'The "AssetManager" component must be configured to use symbolic links.'
            );
        }

        Event::on(Catalog::class, ActiveRecord::EVENT_AFTER_FIND, function ($event) {
            $this->initPlugins($event->sender);
        });
    }

    /**
     * Plugin initialization.
     *
     * @param Catalog $catalog
     * @return void
     */
    public function initPlugins($catalog)
    {
        foreach ($catalog->plugins as $plugin) {
            $plugin = $this->getPlugin($plugin, false);
            if ($plugin instanceof Plugin) {
                $plugin->run($catalog);
            }
        }
    }

    /**
     * Returns the plugin instance with the specified ID.
     *
     * @param string|integer $connection
     * @param boolean $throwException
     * @return Plugin|null
     */
    public function getPlugin($connection, $throwException = true)
    {
        foreach ($this->getComponents() as $id => $definition) {
            if ((
                    is_string($connection) && $id === $connection
                    && ($plugin = $this->get($id, false)) instanceof Plugin
                )
                ||
                (
                    $connection instanceof Model
                    && is_string($definition) && $definition === $connection->class_name
                    && ($plugin = $this->get($id, false)) instanceof Plugin
                )
                ||
                (
                    $connection instanceof Model
                    && is_array($definition) && isset($definition['class'])
                    && $definition['class'] === $connection->class_name
                    && ($plugin = $this->get($id, false)) instanceof Plugin
                )
            ) {
                return $plugin;
            }
        }

        if ($throwException) {
            throw new InvalidConfigException("Not found plugin: $connection");
        }
    }

    /**
     * Returns the list of the loaded plugin instances.
     *
     * @return Plugin[]
     */
    public function getPlugins()
    {
        if (!$this->plugins) {
            foreach ($this->getComponents() as $id => $definition) {
                if (($plugin = $this->get($id, false)) instanceof Plugin) {
                    $this->plugins[$id] = $plugin;
                }
            }
        }

        return $this->plugins;
    }

    /**
     * @return string
     */
    public function getWorkUrl()
    {
        if (!$this->workUrl) {
            $this->workUrl = Yii::$app->assetManager
                 ->publish($this->getWorkPath())[1];
        }

        return $this->workUrl;
    }

    /**
     * @return string
     */
    public function getWorkPath()
    {
        return Yii::getAlias($this->workPath);
    }

    /**
     * @return \common\core\uploader\Uploader
     */
    public function getUploader()
    {
        return Yii::createObject($this->uploader);
    }

    /**
     * @return array
     * @see InstallInterface
     */
    public static function install()
    {
        /**
         * Role Based Access Control
         */

        $auth = Yii::$app->getAuthManager();

        $manager = $auth->getRole(RbacManager::ROLE_MANAGER);
        $editor = $auth->getRole(RbacManager::ROLE_EDITOR);
        $author = $auth->getRole(RbacManager::ROLE_AUTHOR);

        $authorRule = $auth->getRule('isAuthor');

        if (!$manager || !$editor || !$author || !$authorRule) {
            throw new InvalidConfigException('Incorrect RBAC configuration.');
        }

        $transaction = Yii::$app->db->beginTransaction();

        /**
         * Permissions
         */

        // add permission "createCatalog"
        $createCatalog = $auth->createPermission('catalogs.createCatalog');
        $createCatalog->description = 'Создание каталогов';
        $auth->add($createCatalog);

        // add permission "updateCatalog"
        $updateCatalog = $auth->createPermission('catalogs.updateCatalog');
        $updateCatalog->description = 'Редактирование каталогов';
        $auth->add($updateCatalog);

        // add permission "deleteCatalog"
        $deleteCatalog = $auth->createPermission('catalogs.deleteCatalog');
        $deleteCatalog->description = 'Удаление каталогов';
        $auth->add($deleteCatalog);

        // add permission "updateOwnCatalog"
        $updateOwnCatalog = $auth->createPermission('catalogs.updateOwnCatalog');
        $updateOwnCatalog->description = 'Редактирование своих каталогов';
        $updateOwnCatalog->ruleName = $authorRule->name;
        $auth->add($updateOwnCatalog);
        $auth->addChild($updateOwnCatalog, $updateCatalog);

        // add permission "deleteOwnCatalog"
        $deleteOwnCatalog = $auth->createPermission('catalogs.deleteOwnCatalog');
        $deleteOwnCatalog->description = 'Удаление своих каталогов';
        $deleteOwnCatalog->ruleName = $authorRule->name;
        $auth->add($deleteOwnCatalog);
        $auth->addChild($deleteOwnCatalog, $deleteCatalog);

        // add permission "createItem"
        $createItem = $auth->createPermission('catalogs.createItem');
        $createItem->description = 'Создание элементов';
        $auth->add($createItem);

        // add permission "updateItem"
        $updateItem = $auth->createPermission('catalogs.updateItem');
        $updateItem->description = 'Редактирование элементов';
        $auth->add($updateItem);

        // add permission "deleteItem"
        $deleteItem = $auth->createPermission('catalogs.deleteItem');
        $deleteItem->description = 'Удаление элементов';
        $auth->add($deleteItem);

        // add permission "updateOwnItem"
        $updateOwnItem = $auth->createPermission('catalogs.updateOwnItem');
        $updateOwnItem->description = 'Редактирование своих элементов';
        $updateOwnItem->ruleName = $authorRule->name;
        $auth->add($updateOwnItem);
        $auth->addChild($updateOwnItem, $updateItem);

        // add permission "deleteOwnItem"
        $deleteOwnItem = $auth->createPermission('catalogs.deleteOwnItem');
        $deleteOwnItem->description = 'Удаление своих элементов';
        $deleteOwnItem->ruleName = $authorRule->name;
        $auth->add($deleteOwnItem);
        $auth->addChild($deleteOwnItem, $deleteItem);

        // add permission "uploadImage"
        $uploadImage = $auth->createPermission('catalogs.uploadImage');
        $uploadImage->description = 'Загрузка изображений';
        $auth->add($uploadImage);

        // add permission "deleteImages"
        $deleteImages = $auth->createPermission('catalogs.deleteImage');
        $deleteImages->description = 'Удаление изображений';
        $auth->add($deleteImages);

        // add permission "deleteOwnImages"
        $deleteOwnImages = $auth->createPermission('catalogs.deleteOwnImage');
        $deleteOwnImages->description = 'Удаление своих изображений';
        $deleteOwnImages->ruleName = $authorRule->name;
        $auth->add($deleteOwnImages);
        $auth->addChild($deleteOwnImages, $deleteImages);

        /**
         * Hierarchy
         */

        $auth->addChild($manager, $updateCatalog);
        $auth->addChild($manager, $deleteCatalog);

        $auth->addChild($editor, $createCatalog);
        $auth->addChild($editor, $updateOwnCatalog);
        $auth->addChild($editor, $deleteOwnCatalog);
        $auth->addChild($editor, $updateItem);
        $auth->addChild($editor, $deleteItem);
        $auth->addChild($editor, $deleteImages);

        $auth->addChild($author, $createItem);
        $auth->addChild($author, $updateOwnItem);
        $auth->addChild($author, $deleteOwnItem);
        $auth->addChild($author, $uploadImage);
        $auth->addChild($author, $deleteOwnImages);

        $transaction->commit();

        return [];
    }
}
