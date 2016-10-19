<?php

namespace app\modules\frontend\models;

use Yii;
use yii\base\Model;
use app\modules\backend\models\Emergencia;
use app\modules\backend\models\Cap12;
use yii\helpers\Json;
use app\components\Util;

class EmergenciaJson extends Model{
	
	const EVENTOS_HOJE = 'hoje';
	const EVENTOS_AMANHA = 'amanha';
	const EVENTOS_FUTUROS = 'futuro';
		
	const EMERGENCIA_DESLOCAMENTO_LONGITUDE = 5.5;
	const EMERGENCIA_ZOOM = 6.8;
	
	public $idEmergencia = '';
	public $idCap = '';
	public $evento = '';
	public $eventoClass = '';
	public $riscoClass = '';
	public $ufsAtingidas = array();
	public $i18n = array();
	public $geoJson = array();
	public $configCenterMap = array();
	public $configMarker = array();
	public $emergenciaDescricao = '';
	public $recomendacoes = '';
	public $areasAfetadas = '';
	public $municipios = array();
	public $urlDocumento = '';
	
	
	public function __construct($evento){
		$this->evento = $evento;
	}
	
	public function addUf($sigla, $nome){		
		$this->ufsAtingidas[] = array('uf'=> $sigla, 'nome-uf' => $nome);		
	}
	public function addI18n($language, $evento, $risco, $dataInicio, $dataFim){
		$this->i18n[$language] = array('evento'=> $evento, 'risco' => $risco, 'dataInicio' => $dataInicio, 'dataFim' => $dataFim);
	}
	
	public function setGeoJson($idEmergencia, $geoJson, $hex){
		
		$this->geoJson =  array(
				'source' => array(
						'type' => 'GeoJSON',
						'geojson' => array(
								'object'=>array(
										'type' => 'FeatureCollection',
										'crs' => array(
												'type'=>'name',
												'properties'=>array(
														'name' => 'EPSG:4326'
												)
										),
										'features'=>array(array(
												'type' => 'Feature',
												'id' => $idEmergencia,
												'properties' => array(
														'name'=>'emergencia-'.$idEmergencia
												),
												'geometry' => Json::decode($geoJson)
										)
										)
								),
								'projection' => 'EPSG:3857'
						)
				),
				'style'=>array(
						'fill'=>array(
								'color' => Util::convertHexToRgb($hex, '0.6')
						),
						'stroke'=>array(
								'color' => '#ccc',
								'width' => 1
						)
				),
				'visible' => true,
				'opacity' => 1
		);
		
		
	}
	
	public function setConfigCenterEmergencia($centroid){	

		$latlon = $this->getLatLonFromCentroid($centroid);
		
		$this->configCenterMap = array(
				'center'=>array(
						'lat' =>$latlon['lat'],
						'lon' =>$latlon['lon'] + self::EMERGENCIA_DESLOCAMENTO_LONGITUDE,
						'projection' => 'EPSG:4326',
						'zoom'=> self::EMERGENCIA_ZOOM
				)
		);
	}
	
	public function getLatLonFromCentroid($centroid){
		$centroid = str_replace("POINT(", "", $centroid);
		$centroid = str_replace(")", "", $centroid);
		$arr = explode(" ", $centroid);
		return array('lon'=>(double)$arr[0], 'lat'=>(double)$arr[1]);
	}
	
	public function setMarker($centroid){

		$latlon = $this->getLatLonFromCentroid($centroid);		
		
		$this->configMarker = array(
				'visible' => true,
				'marker' => array(
						'lat' => $latlon['lat'],
						'lon' => $latlon['lon'],
						'projection' => 'EPSG:4326',
						'label' => array(
								'show'=>true
						),
						'style'=>array(
								'image'=>array(
										'icon'=>array(
												'anchor'=>array(0.5,1),
												'anchorXUnits'=>'fraction',
												'anchorYUnits'=>'fraction',
												'opacity'=>0,
												'src' => 'img/flag-english.png'
										)
								)
						)
				)
		);
		
		
	}
	
	public function setDescricaoEmergencia($descricao){
		$this->emergenciaDescricao = $descricao;
	}
	public function setRecomendacoes($recomendacoes){
		$this->recomendacoes = $recomendacoes;
	}
	public function setAreasAfetadas($areas){
		$this->areasAfetadas = $areas;
	}
	public function setMunicipios($municipios){
		$this->municipios = $municipios;
	}
	public function getNomeDocumento($identifier){
		return $identifier.Cap12::EXTENSAO_DOCUMENTO_PDF;;
	}
	public function setUrlDocumento($identifier){		
		$nome = $this->getNomeDocumento($identifier);
		$path = Yii::$app->params['REPOSITORIO_RELATORIOS_CAP_CAMINHO_LOGICO'].$nome;
		$this->urlDocumento = $path;		
	}
	
}
