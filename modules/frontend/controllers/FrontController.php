<?php

namespace app\modules\frontend\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\modules\backend\models\Country;
use app\modules\frontend\models\EmergenciaJson;
use app\modules\frontend\models\EmergenciaJsonManager;
use app\modules\frontend\models\CodarJsonManager;

/**
 * FrontController implements the CRUD actions for Emergencia model.
 */
class FrontController extends Controller {
	/**
	 * Configuração da Centralizacao e Zoom do Mapa
	 * @return multitype:multitype:number
	 */
	public function actionMapcenterconfig(){
				
		$idPais = \yii::$app->params['COUNTRY_DEFAULT_ID'];
		$countryConfig = Country::getCenterMapConfigurations($idPais);
		
		$config = array(
				'center'=>array(
								'lat'=>doubleval($countryConfig['center_latitude']),
								'lon'=>doubleval($countryConfig['center_longitude']),
								'zoom'=>doubleval($countryConfig['zoom'])
				)
		);		
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		return $config;
	}
	/**
	 * Configurações do WMS background do MAPA
	 * @return string
	 */
	public function actionWmsconfig(){
		
		$idPais = \yii::$app->params['COUNTRY_DEFAULT_ID'];
				
		$wms = array(
			"wms"=>array(
				"source"=>array(
					"type"=>"ImageWMS",
					"url"=>\yii::$app->params['GEOSERVER_WMS'],
					'params'=>array(
						"LAYERS"=>"cvws:".$idPais,
						"transparent"=>true
					)	
				)
			)
				
		);
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		return $wms;			
	}
	
	/**
	 * Recupera Lista de Cobrades
	 */
	public function actionCodar(){
		$manager = new CodarJsonManager();
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		return $manager->getCodarList();
	}
	
	
	
	/**
	 * Listagem de todas as Emergencias de Hoje.
	 *
	 * @return mixed
	 */
	public function actionHoje() {						
		$manager = new EmergenciaJsonManager();	
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		return $manager->getEmergenciasList(EmergenciaJson::EVENTOS_HOJE);	
		
	}
	
	/**
	 * Listagem de todas as Emergencia de Amanha.
	 *
	 * @return mixed
	 */
	public function actionAmanha() {		
		$manager = new EmergenciaJsonManager();
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		return $manager->getEmergenciasList(EmergenciaJson::EVENTOS_AMANHA);
		
	}
	
	
	/**
	 * Listagem de todas as Emergencia de Futuras.
	 *
	 * @return mixed
	 */
	public function actionFuturo() {	
		$manager = new EmergenciaJsonManager();
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		return $manager->getEmergenciasList(EmergenciaJson::EVENTOS_FUTUROS);
	}
	
	/**
	 * Get Emergencia
	 * @param unknown $id
	 * @return \app\modules\frontend\models\EmergenciaJson
	 */
	public function actionEmergencia($id){
		$manager = new EmergenciaJsonManager();
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		return $manager->getEmergencia($id);
	}
	
	
}
