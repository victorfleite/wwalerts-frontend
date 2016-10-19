<?php
namespace app\modules\frontend\models;

use yii\helpers\Json;
use app\modules\backend\models\Emergencia;
use app\modules\frontend\models\EmergenciaJson;
use app\modules\backend\models\Cap12;


class EmergenciaJsonManager
{
	private $emergenciasList = array();
	public $evento = '';
	
	public function addEmergencia($emergenciaJson){
		$this->emergenciasList[] = $emergenciaJson;
	}
	public function getEmergenciasDoEvento($evento, $idPais){
		switch ($evento){
			case EmergenciaJson::EVENTOS_HOJE:
				return Emergencia::getEmergenciasHoje($idPais);
			break;	
			case EmergenciaJson::EVENTOS_AMANHA:
				return Emergencia::getEmergenciasAmanha($idPais);
			break;
			case EmergenciaJson::EVENTOS_FUTUROS:
				return Emergencia::getEmergenciasFuturo($idPais);
			break;
		}
	}
	
	public function getEmergenciasList($evento)
	{
		
		$idPais = \yii::$app->params['COUNTRY_DEFAULT_ID'];		
		$emergencias = $this->getEmergenciasDoEvento($evento, $idPais);
				
		foreach ($emergencias as $key => $item){			
			$ufs 		= $item['ufs'];
			$emergencia = $item['emergencia'];			
			
			$cap = Emergencia::getMaxCapFromEmergencia($emergencia['id_emergencia']);
					
			$emergenciaJson = new EmergenciaJson($evento);
			$emergenciaJson->idEmergencia = $emergencia['id_emergencia'];
			$emergenciaJson->idCap = ($cap)? $cap['id']:null;
			$emergenciaJson->eventoClass = $emergencia['codar_classcss'];
			$emergenciaJson->riscoClass = $emergencia['risco_classcss'];
			
			foreach ($ufs as $uf){				
				$emergenciaJson->addUf($uf['uf_sigla'], $uf['uf_nome']);
			}
			// pt-BR
			$codar = $emergencia['codar'];
			$risco = $emergencia['risco'];
			$dataInicio = $emergencia['data_inicial'];
			$dataFim = $emergencia['duracao_estimada'];
			$emergenciaJson->addI18n('pt-BR', $codar, $risco, $dataInicio, $dataFim);
			// en
			$codar = \Yii::t('app', $emergencia['codar_i18n'], [], 'en');
			$risco = \Yii::t('app', $emergencia['risco_i18n'], [], 'en');
			$dataInicio = $emergencia['data_inicial'];
			$dataFim = $emergencia['duracao_estimada'];
			$emergenciaJson->addI18n('en', $codar, $risco, $dataInicio, $dataFim);
			// es
			$codar = \Yii::t('app', $emergencia['codar_i18n'], [], 'es');
			$risco = \Yii::t('app', $emergencia['risco_i18n'], [], 'es');
			$dataInicio = $emergencia['data_inicial'];
			$dataFim = $emergencia['duracao_estimada'];
			$emergenciaJson->addI18n('es', $codar, $risco, $dataInicio, $dataFim);
			
			$emergenciaJson->setDescricaoEmergencia($emergencia['emergencia_descricao']);
			$emergenciaJson->setRecomendacoes($emergencia['recomendacoes']);
			$emergenciaJson->setAreasAfetadas(($cap)? $cap['areadesc']:'');
			$emergenciaJson->setMunicipios(Emergencia::getArrayMunicipios($emergencia['id_emergencia']));
						
			// GeoJson
			$emergenciaJson->setGeoJson($emergencia['id_emergencia'], $emergencia['geo_json'], $emergencia['risco_hex']);
			
			// Marker
			$emergenciaJson->setMarker($emergencia['centroid']);
			
			// Config Center Map			
			$emergenciaJson->setConfigCenterEmergencia($emergencia['centroid']);
			
			// Set Doc.
			$emergenciaJson->setUrlDocumento($cap['identifier']);
			
			$this->addEmergencia($emergenciaJson);
		}
				
		
		
		return $this->emergenciasList;
	}
	
	
	public function getEmergencia($id)
	{
	
		$idPais = \yii::$app->params['COUNTRY_DEFAULT_ID'];
		$result = Emergencia::getEmergencia($id);
			
		$ufs 		= $result['ufs'];
		$emergencia = $result['emergencia'];
			
		$cap = Emergencia::getAreaDescMaxCapFromEmergencia($emergencia['id_emergencia']);
			
		$emergenciaJson = new EmergenciaJson($evento);
		$emergenciaJson->idEmergencia = $emergencia['id_emergencia'];
		$emergenciaJson->idCap = ($cap)? $cap['id']:null;
		$emergenciaJson->eventoClass = $emergencia['codar_classcss'];
		$emergenciaJson->riscoClass = $emergencia['risco_classcss'];
			
		foreach ($ufs as $uf){
			$emergenciaJson->addUf($uf['uf_sigla'], $uf['uf_nome']);
		}
		// pt-BR
		$codar = $emergencia['codar'];
		$risco = $emergencia['risco'];
		$dataInicio = $emergencia['data_inicial'];
		$dataFim = $emergencia['duracao_estimada'];
		$emergenciaJson->addI18n('pt-BR', $codar, $risco, $dataInicio, $dataFim);
		// en
		$codar = \Yii::t('app', $emergencia['codar_i18n'], [], 'en');
		$risco = \Yii::t('app', $emergencia['risco_i18n'], [], 'en');
		$dataInicio = $emergencia['data_inicial'];
		$dataFim = $emergencia['duracao_estimada'];
		$emergenciaJson->addI18n('en', $codar, $risco, $dataInicio, $dataFim);
		// es
		$codar = \Yii::t('app', $emergencia['codar_i18n'], [], 'es');
		$risco = \Yii::t('app', $emergencia['risco_i18n'], [], 'es');
		$dataInicio = $emergencia['data_inicial'];
		$dataFim = $emergencia['duracao_estimada'];
		$emergenciaJson->addI18n('es', $codar, $risco, $dataInicio, $dataFim);
			
		$emergenciaJson->setDescricaoEmergencia($emergencia['emergencia_descricao']);
		$emergenciaJson->setRecomendacoes($emergencia['recomendacoes']);
		$emergenciaJson->setAreasAfetadas(($cap)? $cap['areadesc']:'');
		$emergenciaJson->setMunicipios(Emergencia::getArrayMunicipios($emergencia['id_emergencia']));
	
		// GeoJson
		$emergenciaJson->setGeoJson($emergencia['id_emergencia'], $emergencia['geo_json'], $emergencia['risco_hex']);
			
		// Marker
		$emergenciaJson->setMarker($emergencia['centroid']);
			
		// Config Center Map
		$emergenciaJson->setConfigCenterEmergencia($emergencia['centroid']);
			
	
		return $emergenciaJson;
	}
	
}
