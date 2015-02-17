<?php

namespace fonclub\menu\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Html;
use yii\helpers\Url;
use fonclub\menu\models\Menu;
use fonclub\menu\models\MenuSearch;

/**
 * MenuController implements the CRUD actions for Menu model.
 */
class MenuController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Menu models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MenuSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Menu model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Menu model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Menu();

        $post = Yii::$app->request->post();

        if ($model->load($post) && $model->save()) {

            // Set flash message
            Yii::$app->getSession()->setFlash('menu', Yii::t('app', '"{item}" has been created', ['item' => $model->name]));
                
            if (isset($post['close'])) {
                return $this->redirect(['index']);
            } elseif (isset($post['new'])) {
                return $this->redirect(['create']);
            } else {
                return $this->redirect(['update', 'id' => $model->id]);
            }
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Menu model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $post = Yii::$app->request->post();

        if ($model->load($post) && $model->save()) {

            // Set flash message
            Yii::$app->getSession()->setFlash('menu', Yii::t('app', '{item} has been updated', ['item' => $model->name]));
            
            if (isset($post['close'])) {
                return $this->redirect(['index']);
            } elseif (isset($post['new'])) {
                return $this->redirect(['create']);
            } else {
                return $this->redirect(['update', 'id' => $model->id]);
            }

        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Menu model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        try {
            // Only the superadmin can delete menu's
            if (!Yii::$app->user->can('Superadmin'))
                throw new \yii\base\Exception(Yii::t('app', 'You do not have the right permissions to delete this item'));
            
            $model = $this->findModel($id);
            
            $transaction = Yii::$app->db->beginTransaction();
            $model->delete();
            $transaction->commit();   
        } catch(\yii\base\Exception $e) {
            // Set flash message
            Yii::$app->getSession()->setFlash('menu-error', $e->getMessage());    
        }        

        // Set flash message
        Yii::$app->getSession()->setFlash('menu', Yii::t('app', '{item} has been deleted', ['item' => $model->name]));
            
        return $this->redirect(['index']);
    }

    public function actionMenuItems()
    {
        return $this->redirect(['menu-item/index']);
    }

    /**
     * Finds the Menu model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Menu the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Menu::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested item does not exist'));
        }
    }
}
