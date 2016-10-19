<?php

namespace app\modules\backend\models\behaviors;

use yii\db\ActiveRecord;
use yii\base\Behavior;
use app\modules\backend\models\Emergencia;
use app\modules\backend\models\Cap12;
use app\modules\backend\models\Cap12Description;

class CapBehavior extends Behavior {
	
	public function events() {
		return [ 
				ActiveRecord::EVENT_AFTER_INSERT => 'gerarCap',
				ActiveRecord::EVENT_AFTER_UPDATE => 'gerarCap',
		];
	}
	public function gerarCap($event) {
				
		$emergencia = $event->sender;
		$codar = $emergencia->codar;
		$risco = $emergencia->risco;
		$usuario = $emergencia->owner;
		$instituicao = $usuario->instituicao;		
						
		// Inicializar Cap
		$cap1_2 = new Cap12();
		$nextSequence = 1;
		$msgType = $cap1_2::MESSAGE_TYPE_ALERT;
		
		$quantidadeCaps = $cap1_2->getQuantidadeCaps($emergencia->id);
		if($quantidadeCaps>0){
			// Get Sequence
			$nextSequence = $cap1_2->getNextSequence($emergencia->id);
			if($emergencia->encerrado == Emergencia::ENCERRADO_SIM){
				$msgType = $cap1_2::MESSAGE_TYPE_CANCEL;				
			}else{
				$msgType = $cap1_2::MESSAGE_TYPE_UPDATE;
			}			
		}			

		$cap1_2->emergencia_id = $emergencia->id;
		$cap1_2->usuario_id = $usuario->id;
		$cap1_2->instituicao_id = $instituicao->id;
		$cap1_2->risco_id = $risco->id;
		$cap1_2->codar_id = $codar->id;
		$cap1_2->identifier = $cap1_2->getIdentifier($instituicao->siglacap, $emergencia->id, $nextSequence);
		$cap1_2->sender = $instituicao->sendercap;
		$cap1_2->status = Cap12::STATUS_ACTUAL;
		$cap1_2->msgtype = $msgType;
		$cap1_2->scope = $cap1_2::SCOPE_PUBLIC;
		$cap1_2->language = $instituicao->languagecap;
		$cap1_2->category = $codar->categoriacap;
		$cap1_2->event = $codar->descricao;
		$cap1_2->responsetype = Cap12::RESPONSETYPE_PREPARE;
		$cap1_2->urgency = Cap12::URGENCY_FUTURE;
		$cap1_2->severity = $risco->severitycap;
		$cap1_2->certainty = Cap12::CERTAINTY_LIKELY;
		$cap1_2->onset = $emergencia->data_inicial;
		$cap1_2->expires = $emergencia->duracao_estimada;
		$cap1_2->sendername = $instituicao->nome;
		$cap1_2->headline = $cap1_2->getHeadline($codar->descricao, $risco->descricao);
		$cap1_2->instruction = Cap12::INSTRUCTION;
		$cap1_2->description = $emergencia->descricao;
		$cap1_2->contact = $instituicao->contatocap;
		$cap1_2->areadesc = $cap1_2->getAreaDesc($emergencia->getStringMesoregioes());
		$cap1_2->polygon = $emergencia->location;
		$cap1_2->sequencecap = $nextSequence;
		$cap1_2->polygontext  = $emergencia->geometryToWKT();
		$ultimoCap = $cap1_2->getUltimoCap($emergencia->id);
		$cap1_2->cap1_2_id_pai = ((empty($ultimoCap))?0:$ultimoCap->id);
		$cap1_2->sent = date ( 'Y-m-d H:i' );
		
		if(!$cap1_2->save()){
			throw new \Exception ("Failed when try to save cap1.2");
		}else{
			//Salvar XML em arquivo
			$cap1_2->address_cap = $cap1_2->saveXML();
			
			//Salvar Address Cap	
			$cap1_2->save();
			
		}
		
		
	}
}