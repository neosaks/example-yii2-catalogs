<?php
namespace common\modules\catalogs\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use common\modules\catalogs\Module;
use common\modules\catalogs\DataManager;
use common\modules\catalogs\models\Item;
use common\modules\catalogs\models\Catalog;

/**
 * Items controller
 */
class ItemsController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['view']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create'],
                        'roles' => ['catalogs.createItem']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update'],
                        'roles' => ['catalogs.updateItem'],
                        'roleParams' => function ($rule) {
                            return ['post' => $this->findModel(Yii::$app->request->get('id'))];
                        }
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete'],
                        'roles' => ['catalogs.deleteItem'],
                        'roleParams' => function ($rule) {
                            return ['post' => $this->findModel(Yii::$app->request->get('id'))];
                        }
                    ]
                ]
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'images-get' => [
                'class' => 'vova07\imperavi\actions\GetImagesAction',
                'url' => $this->getWorkUrl(),
                'path' => $this->getWorkPath()
            ],
            'image-upload' => [
                'class' => 'vova07\imperavi\actions\UploadFileAction',
                'url' => $this->getWorkUrl(),
                'path' => $this->getWorkPath()
            ],

            'files-get' => [
                'class' => 'vova07\imperavi\actions\GetFilesAction',
                'url' => $this->getWorkUrl(),
                'path' => $this->getWorkPath()
            ],
            'file-upload' => [
                'class' => 'vova07\imperavi\actions\UploadFileAction',
                'url' => $this->getWorkUrl(),
                'path' => $this->getWorkPath(),
                'uploadOnlyImage' => false
            ],
            'file-delete' => [
                'class' => 'vova07\imperavi\actions\DeleteFileAction',
                'url' => $this->getWorkUrl(),
                'path' => $this->getWorkPath()
            ],
        ];
    }

    /**
     * Lists all Item models.
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Displays a single Item model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id)
        ]);
    }

    /**
     * Creates a new Item model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($catalog)
    {
        $catalog = $this->findCatalog($catalog);
        $model = new Item(['catalog_id' => $catalog->id]);

        $manager = new DataManager($catalog, $model);

        $transaction = Yii::$app->db->beginTransaction();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if ($manager->load(Yii::$app->request->post()) && $manager->save()) {
                // @todo handle
            }

            $transaction->commit();

            return $this->redirect(['view', 'id' => $model->id]);
        }

        if ($manager->getModel()->hasErrors()) {
            $model->addErrors($manager->getModel()->getErrors());
        }

        return $this->render('create', [
            'manager' => $manager,
            'model' => $model
        ]);
    }

    /**
     * Updates an existing Item model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $manager = new DataManager($model->catalog, $model);

        $transaction = Yii::$app->db->beginTransaction();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if ($manager->load(Yii::$app->request->post()) && $manager->save()) {
                $transaction->commit();

                return $this->redirect(['view', 'id' => $model->id]);
            }

            $model->addErrors($manager->getModel()->getErrors());
        }

        return $this->render('update', [
            'manager' => $manager,
            'model' => $model
        ]);
    }

    /**
     * Deletes an existing Item model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        $manager = new DataManager($model->catalog, $model);

        if ($manager->deleteItem()) {
            Yii::$app->session->setFlash('success', 'Элемент удалён.');

            return $this->redirect(['default/view', 'id' => $manager->getCatalog()->id]);
        }

        Yii::$app->session->setFlash('danger', 'Ошибка при удалении элемента.');

        return $this->refresh();
    }

    /**
     * Finds the Item model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Item the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $condition = ['id' => $id, 'status' => Item::STATUS_ACTIVE];
        $with = ['catalog'];

        if (($model = Item::find()->where($condition)->with($with)->one()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Finds the Catalog model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Catalog the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findCatalog($id)
    {
        $condition = ['id' => $id, 'status' => Catalog::STATUS_ACTIVE];

        if (($model = Catalog::find()->where($condition)->one()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Description.
     * @return string
     */
    protected function getWorkUrl()
    {
        return (Module::getInstance())->getWorkUrl();
    }

    /**
     * Description.
     * @return string
     */
    protected function getWorkPath()
    {
        return (Module::getInstance())->getWorkPath();
    }
}
