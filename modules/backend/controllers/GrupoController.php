<?php

namespace app\modules\backend\controllers;

use Yii;
use app\modules\backend\models\Grupo;
use app\modules\backend\models\GrupoSearch;
use app\modules\backend\models\AssociarJurisdicaoGrupoForm;
use app\modules\backend\models\AssociarUsuarioGrupoForm;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\modules\backend\models\RlGrupoJurisdicao;
use app\modules\backend\models\RlGrupoUsuario;

/**
 * GrupoController implements the CRUD actions for Grupo model.
 */
class GrupoController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    //'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Grupo models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new GrupoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Grupo model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }
    
    public function actionAssociarJurisdicoes($id){

    	$model = new AssociarJurisdicaoGrupoForm();
    	$grupo = $this->findModel($id);
    	$model->jurisdicoes = Json::encode($grupo->getIdsJurisdicaosAssociadosArray());
    	
    	    	
    	if ($model->load(Yii::$app->request->post())) {
    		$jurisdicoes = Json::decode($model->jurisdicoes);
    		// Deleta todos as jurisdicoes encontradas do grupo
    		RlGrupoJurisdicao::deleteAll('grupo_id = :grupo_id', [':grupo_id'=>$grupo->id]);
    		// Cria as jurisdicoes 
    		foreach($jurisdicoes as $idJurisdicao){
    				$rl = new RlGrupoJurisdicao();
    				$rl->jurisdicao_id = $idJurisdicao;
    				$rl->grupo_id = $grupo->id;
    				$rl->save();
    		}
    		
    	} 
    	    	
    	return $this->render('associar-jurisdicoes', [
    			'model' => $model,
    			'grupo' => $grupo,
    	]);
    }
    
    public function actionAssociarUsuarios($id){
    
    	$model = new AssociarUsuarioGrupoForm();
    	$grupo = $this->findModel($id);
    	$model->usuarios = Json::encode($grupo->getIdsUsuariosAssociadosArray());
    	 
    
    	if ($model->load(Yii::$app->request->post())) {
    		$usuarios = Json::decode($model->usuarios);
    		// Deleta todos as jurisdicoes encontradas do grupo
    		RlGrupoUsuario::deleteAll('grupo_id = :grupo_id', [':grupo_id'=>$grupo->id]);
    		// Cria as jurisdicoes
    		foreach($usuarios as $idUsuario){
    			$rl = new RlGrupoUsuario();
    			$rl->usuario_id = $idUsuario;
    			$rl->grupo_id = $grupo->id;
    			$rl->save();
    		}
    
    	}
    
    	return $this->render('associar-usuarios', [
    			'model' => $model,
    			'grupo' => $grupo,
    			]);
    }

    /**
     * Creates a new Grupo model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Grupo();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['grupo/associar-usuarios', 'id'=>$model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Grupo model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Grupo model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Grupo model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Grupo the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Grupo::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
