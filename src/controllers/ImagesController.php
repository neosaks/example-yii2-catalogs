<?php
namespace common\modules\catalogs\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\FileHelper;
use common\helpers\ImageHelper;
use common\core\uploader\Uploader;
use common\modules\catalogs\Module;
use common\modules\catalogs\models\Image;
use common\modules\catalogs\models\ImageSearch;

/**
 * Images controller
 */
class ImagesController extends Controller
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
                        'actions' => ['select']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['index']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete'],
                        'roles' => ['catalogs.deleteImage'],
                        'roleParams' => function ($rule) {
                            return ['post' => $this->findModel(Yii::$app->request->get('id'))];
                        }
                    ],
                    [
                        'allow' => true,
                        'actions' => ['upload'],
                        'roles' => ['catalogs.uploadImage']
                    ]
                ]
            ]
        ];
    }

    /**
     *
     */
    public function actionSelect()
    {
        $dataProvider = $this->findModels('created_at DESC');

        return $this->renderAjax('select', [
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * Lists all Image models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = $this->findModels('created_at DESC');

        return $this->render('index', [
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * Deletes an existing Image model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $path = $model->getPath();

        $transaction = Yii::$app->db->beginTransaction();
        if ($model->delete() && FileHelper::unlink($path)) {
            Yii::$app->session->setFlash('success', 'Изображение удалено.');
            $transaction->commit();
        } else {
            Yii::$app->session->setFlash(
                'error',
                'Не удалось удалить файл. Если ошибка повторится, свяжитесь со службой технической поддержки.'
            );
            $transaction->rollBack();
        }

        return $this->redirect(['index']);
    }

    /**
     *
     */
    public function actionUpload()
    {
        if ($this->isLimitExceeded()) {
            throw new ForbiddenHttpException();
        }

        $uploader = $this->getUploader();

        $uploader->on(Uploader::EVENT_BEFORE_UPLOAD, function ($event) {
            $resolution = ImageHelper::calcResolution($event->file->tempName);

            $model = new Image([
                'extension' => $event->file->extension,
                'basename' => $event->file->baseName,
                'size' => $event->file->size,
                'filename' => $event->filename,
                'subDirectory' => $event->suffix,
                'resolution' => $resolution
            ]);

            $event->isValid = $model->save();
        });

        if ($uploader->load(Yii::$app->request->post()) && $uploader->upload()) {
            Yii::$app->session->setFlash('success', 'Изображения загружены.');
            return $this->redirect(['index']);
        }

        return $this->render('upload', ['model' => $uploader]);
    }

    /**
     * Finds the Image model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Image the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $condition = ['id' => $id, 'status' => Image::STATUS_ACTIVE];

        if (($model = Image::find()->where($condition)->one()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Description.
     * @param string $orderBy
     * @param integer|null $limit
     * @return \yii\data\DataProviderInterface
     */
    protected function findModels($orderBy, $limit = null)
    {
        $condition = ['status' => Image::STATUS_ACTIVE];

        return (new ImageSearch())
                ->search(Yii::$app->request->queryParams, $condition, $orderBy, $limit);
    }

    /**
     * Description.
     * @return Uploader
     */
    protected function getUploader()
    {
        return Module::getInstance()->getUploader();
    }

    /**
     * @todo implement
     */
    protected function isLimitExceeded()
    {
        return false;
    }
}
