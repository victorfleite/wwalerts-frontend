<?php

namespace app\modules\backend\models\behaviors;

use Yii;
use yii\db\ActiveRecord;
use yii\base\Behavior;
use app\modules\backend\models\Cap12;
use app\modules\backend\models\Instituicao;
use app\components\Writer;
use app\components\Util;
use app\modules\backend\models\CapEmailTemplate;
use app\modules\backend\models\CapEmail;
use kartik\mpdf\Pdf;
use yii\swiftmailer\Mailer;

class EmailCapBehavior extends Behavior {
	
	public function events() {
		return [ 
				ActiveRecord::EVENT_AFTER_INSERT => 'enviarEmail',
				ActiveRecord::EVENT_AFTER_UPDATE => 'enviarEmail',
		];
	}
	public function enviarEmail($event) {
			
		$cap1_2 = $event->sender;
		$capEmailTemplate = CapEmailTemplate::findOne(['instituicao_id'=>$cap1_2->instituicao_id]);
			if(!$capEmailTemplate){
			throw \Exception("O Template inexistente para esta instituição. ID:".$cap1_2->instituicao->id. "; Nome:".$cap1_2->instituicao->nome);
		}
		
		$template = new Writer($capEmailTemplate->template);
		
		$sentDate = \DateTime::createFromFormat('Y-m-d H:i', $cap1_2->sent);
		$onSetDate = \DateTime::createFromFormat('Y-m-d H:i:s', $cap1_2->onset);
		$onExpiresDate = \DateTime::createFromFormat('Y-m-d H:i:s', $cap1_2->expires);
		
		// Alterar Valores do Template
		$template->replace("{{numero-aviso}}", $cap1_2->identifier)
				 ->replace("{{data-por-extenso}}", "Brasília, ".Util::getDataPorExtenso($sentDate))
				 ->replace("{{aviso-de}}", $cap1_2->event)
				 ->replace("{{severidade}}", \yii::t("app", "mapa.emergencia.capSeverity".$cap1_2->severity))
				 ->replace("{{color-risk}}", $cap1_2->getColorRisk())
				 ->replace("{{evento}}", $cap1_2->event)
				 ->replace("{{status}}",  \yii::t("app", "mapa.emergencia.capMsgType".$cap1_2->msgtype))
				 ->replace("{{inicio}}", $onSetDate->format("d/m/Y H:i"))
				 ->replace("{{fim}}", $onExpiresDate->format("d/m/Y H:i"))
				 ->replace("{{meteorologista}}", $cap1_2->usuario->getNomeCompleto())
				 ->replace("{{instituicao}}", $cap1_2->instituicao->getNomeCompleto())
				 ->replace("{{descricao}}", $cap1_2->description)
				 ->replace("{{areas-atingidas}}", $cap1_2->areadesc)
				 ->replace("{{path-images}}", \Yii::$app->params['IMAGENS_DO_ENVIADOR_EMAIL'])
				 ->replace("{{path-cap}}", \Yii::$app->params['REPOSITORIO_DE_CAPS'].$cap1_2->id);
		
	
		$fileName = $cap1_2->identifier.Cap12::EXTENSAO_DOCUMENTO_PDF;
		$file = \Yii::$app->params['REPOSITORIO_DE_RELATORIOS_CAP'].$fileName;
		
		$pdf = new Pdf();
		$mpdf = $pdf->api; // fetches mpdf api
		
		$mpdf->WriteHtml($template->getString()); // call mpdf write html
		$mpdf->Output($file, 'F'); // call the mpdf api output as needed
		
		
		$plainText = new Writer();
		$plainText->writeln("Data: " . "Brasília, " . Util::getDataPorExtenso($sentDate))
				  ->writeln("Numero do aviso: " . $cap1_2->identifier)
				  ->writeln("Aviso de: " . $cap1_2->event + ". Nível de Severidade: ". $cap1_2->severity)
				  ->writeln("Evento: " . $cap1_2->event)
				  ->writeln("Status: " . $cap1_2->status)
				  ->writeln("Início: " . $onSetDate->format("d/m/Y H:i"))
				  ->writeln("Fim: " . $onExpiresDate->format("d/m/Y H:i"))
				  ->writeln("Meteorologista: " . $cap1_2->usuario->getNomeCompleto()) 
				  ->writeln("Instituição: " .  $cap1_2->instituicao->getNomeCompleto())
				  ->writeln("Descrição: " . $cap1_2->description)
				  ->writeln("Áreas Atingidas: " . $cap1_2->areadesc);		
				
		$mail = Yii::$app->mailer->compose();
		$mail->setFrom(array(\Yii::$app->params['EMAIL_SENDER_EMAIL']=>\Yii::$app->params['EMAIL_SENDER_NOME']));
		
		$mail->setTo($cap1_2->usuario->email);
		$capEmails = CapEmail::findAll(["instituicao_id"=>$cap1_2->instituicao_id, "status"=>CapEmail::STATUS_ATIVO]);		
		foreach ($capEmails as $capEmail){
			if($capEmail->email != $cap1_2->usuario->email){
				$mail->setBcc($capEmails->email);
			}
		}
		
		$mail->setSubject("Aviso: ".$cap1_2->identifier)
    		 ->setTextBody($plainText->getString())
    		 ->setHtmlBody($template->getString());
		
		$mail->attach($file);
		$mail->send();
		
	}
}