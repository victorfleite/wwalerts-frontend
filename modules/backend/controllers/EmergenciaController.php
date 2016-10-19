<?php

namespace app\modules\backend\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\behaviors\TimeStampBehavior;
use app\modules\backend\models\Emergencia;
use app\modules\backend\models\EmergenciaSearch;
use app\modules\backend\models\VwEmergencia;
use app\modules\backend\models\EmergenciaForm;
use app\modules\backend\models\EmergenciaCancelamentoForm;
use app\modules\backend\models\EmergenciaCancelamento;
use app\modules\backend\models\Log;
use app\modules\backend\models\Classe;
use app\modules\backend\models\EmergenciaLog;
use app\modules\backend\models\behaviors\CapBehavior;
use app\modules\backend\models\Cap12Description;
use app\modules\backend\models\Paises;
use app\modules\backend\models\Country;
use app\modules\backend\models\Cap12;
use app\modules\backend\models\Jurisdicao;

/**
 * EmergenciaController implements the CRUD actions for Emergencia model.
 */
class EmergenciaController extends Controller {
	public function behaviors() {
		return [ 
				'verbs' => [ 
						'class' => VerbFilter::className (),
						'actions' => [ 
								'delete' => [ 
										'POST' 
								] 
						] 
				] 
		];
	}
	
	/**
	 * Lists all Emergencia models.
	 *
	 * @return mixed
	 */
	public function actionIndex() {
		$searchModel = new EmergenciaSearch ();
		$dataProvider = $searchModel->search ( Yii::$app->request->get () );
		
		//\yii::$app->dumper->show($dataProvider, true);
	
		return $this->render ( 'index', [ 
				'searchModel' => $searchModel,
				'dataProvider' => $dataProvider 
		] );
	}
	
	/**
	 * Displays a single Emergencia model.
	 *
	 * @param string $id        	
	 * @return mixed
	 */
	public function actionShow($id) {
		$model = $this->findModel ( $id );
		
		$dataProviderEmergenciasLog = new ActiveDataProvider ( [ 
				'query' => $model->getEmergenciasLog () 
		] );
		
		return $this->render ( 'show', [ 
				'model' => $model,
				'dataProviderEmergenciasLog' => $dataProviderEmergenciasLog 
		] );
	}
	
	/**
	 * Creates a new Emergencia model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @return mixed
	 */
	public function actionCreate() {
		$model = new Emergencia ();
		$model->owner_id = \yii::$app->user->id;
		
		if ($model->load ( Yii::$app->request->post () )) {
			
			// \Yii::$app->dumper->show($model, true);
			// Attach a Behavior Cap
			$model->attachBehavior ( 'gerarCap', CapBehavior::className () );
			
			if (! empty ( $model->codar_id ) && ! empty ( $model->risco_id )) {
				// Adicionar Descricão
				$description = Cap12Description::find ()->where ( [ 
						'codar_id' => $model->codar_id,
						'risco_id' => $model->risco_id 
				] )->one ();
				$model->descricao = $description ['desc_ptb'];
			}
			
			if ($model->save ()) {
				
				//\Yii::$app->dumper->show($model, true);
				
				$emergenciaLog = new EmergenciaLog ();
				$emergenciaLog->responsavel_id = $model->owner_id;
				$emergenciaLog->i18n = "emergencia_log.i18n.emergencia_criada";
				$emergenciaLog->descricao = \yii::t('app', 'emergencia_log.descricao.emergencia.criada');
				$emergenciaLog->data = date ( 'Y-m-d H:i:s' );
				$emergenciaLog->emergencia_id = $model->id;
				$emergenciaLog->save ();
				
				return $this->redirect ( [ 
						'show',
						'id' => $model->id 
				] );
			}
		} 
		$dataAtual = date('Y-m-d H:i:s');
		$emergencias = Emergencia::find()->where(['encerrado' => null])->andWhere("duracao_estimada >= '$dataAtual'")->orderBy('data_inicial asc')->all();
		$jurisdicaoUsuario = Jurisdicao::getJurisdicaoDoUsuario(\yii::$app->user->id);
		return $this->render ( 'create', [
				'model' => $model, 
				'emergencias'=>$emergencias,
				'jurisdicaoUsuario'=>$jurisdicaoUsuario
		] );
	}
	
	/**
	 * Updates an existing Emergencia model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param string $id        	
	 * @return mixed
	 */
	public function actionUpdate($id) {
		$model = $this->findModel ( $id );
		
		if ($model->load ( Yii::$app->request->post () )) {
			
			$changedAttributes = $model->getDirtyAttributes ();
			
			// Attach a Behavior Cap
			$model->attachBehavior ( 'gerarCap', CapBehavior::className () );
			
			if ($model->save ()) {
				
				$emergenciaLog = new EmergenciaLog ();
				$emergenciaLog->salvarAlteracoes ( $model, $changedAttributes );
				
				return $this->redirect ( [ 
						'show',
						'id' => $model->id 
				] );
			} 
		} 
		$dataAtual = date('Y-m-d H:i:s');
		$emergencias = Emergencia::find()->where(['encerrado' => null])->andWhere("duracao_estimada >= '$dataAtual'")->orderBy('data_inicial asc')->all();
				
		$jurisdicaoUsuario = Jurisdicao::getJurisdicaoDoUsuario(\yii::$app->user->id);
		return $this->render ( 'update', [
				'model' => $model, 
				'emergencias'=>$emergencias,
				'jurisdicaoUsuario'=>$jurisdicaoUsuario
		] );
	}
	
	/**
	 * Deletes an existing Emergencia model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param string $id        	
	 * @return mixed
	 */
	public function actionCancel($id) {
		$emergencia = $this->findModel ( $id );
		$emergenciaCancelamentoForm = new EmergenciaCancelamentoForm();		
		
		if ($emergenciaCancelamentoForm->load ( Yii::$app->request->post () ) && $emergenciaCancelamentoForm->validate ()) {

			if(!$emergencia->encerrar()){
				throw new \Exception('Não foi possivel encerrar o aviso.');
			}
			
			$motivo = Emergencia::findOne ( $form->idMotivo );
			
			$emergenciaLog = new EmergenciaLog ();
			$emergenciaLog->responsavel_id = $emergencia->owner_id;
			$emergenciaLog->i18n = "emergencia_log.i18n.emergencia_cancelada";
			$emergenciaLog->descricao = \yii::t('app', 'emergencia_log.descricao.emergencia.cancelada');
			$emergenciaLog->motivo = $motivo->motivo;
			$emergenciaLog->data = date ( 'Y-m-d H:i:s' );
			$emergenciaLog->emergencia_id = $emergencia->id;
			$emergenciaLog->save ();
					
			return $this->redirect ( [
				'show',
				'id' => $emergencia->id
			] );
		} 		
		
		return $this->render ( 'cancel', [ 
				'model'=>$emergencia, 'emergenciaCancelamentoForm' => $emergenciaCancelamentoForm,
		] );
	}
	
	/*
	public function actionCountry(){
		
		$idPais = 32;
		$id = \yii::$app->request->getQueryParam('id');
		if(!empty($id)){
			$idPais = $id;
		}
		
		//SimpleDateFormat dateFormat = new SimpleDateFormat("yyyy-MM-dd");
		//Calendar cal = Calendar.getInstance();
		//def dataHoje = dateFormat.format(cal.getTime())
	
		//def sql = Sql.newInstance(dataSource)
	
	
		//sql.eachRow('select ST_AsText(ST_Envelope(GeometryFromText(the_geomsimples, 3857))) as result from paises where id=:idCountry', [idCountry:32]) { row ->
		/*sql.eachRow('SELECT (g.gdump).path, ST_Astext((g.gdump).geom) as wkt FROM (SELECT ST_Dump( GeometryFromText(the_geomsimples, 3857) ) AS gdump FROM paises WHERE id=:idCountry) AS g LIMIT 1;', [idCountry:idPais]) { row ->
		theGeom = row.wkt;
		}
		$countryGeom = Paises::getWKT($idPais);		
		$eventosHoje = Emergencia::getEmergenciasHoje($idPais);
		$eventosFuturos = Emergencia::getEmergenciasFuturo($idPais);
		$listUfs = Country::getUfs($idPais);
		$capsHoje = Cap12::getCapsHoje();
		$capsFuturo = Cap12::getCapsFuturo();
		
		$items = [
				'countryGeom'=>$countryGeom,
				'eventosHoje'=>$eventosHoje,
				'eventosFuturos'=>$eventosFuturos,
				'listUfs'=>$listUfs,
				'capsHoje'=>$capsHoje,
				'capsFuturo'=>$capsFuturo
		];
		\Yii::$app->response->format = 'json';
		return $items;
		
	}
	*/
	
	
	
	/**
	 * Finds the Emergencia model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param string $id        	
	 * @return Emergencia the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id) {
		if (($model = Emergencia::findOne ( $id )) !== null) {
			return $model;
		} else {
			throw new NotFoundHttpException ( 'The requested page does not exist.' );
		}
	}
	
	
	
	
	
}
