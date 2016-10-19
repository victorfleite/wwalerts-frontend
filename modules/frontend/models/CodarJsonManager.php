<?php
namespace app\modules\frontend\models;

use yii\helpers\Json;
use app\modules\backend\models\Codar;
use app\modules\frontend\models\CodarJson;


class CodarJsonManager
{
	private $codarList = array();
	
	public function addCodar($codarJson){
		$this->codarList[] = $codarJson;
	}
	
	public function getCodarList()
	{
		$codarList = Codar::find()->orderBy('descricao')->all();
		foreach ($codarList as $codar){
			$codarJson = new CodarJson();
			$codarJson->addI18n('pt-BR', $codar->descricao, $codar->classcss);
			$codarJson->addI18n('en', \Yii::t('app', $codar->i18n, [], 'en'), $codar->classcss);
			$codarJson->addI18n('es', \Yii::t('app', $codar->i18n, [], 'es'), $codar->classcss);
			$this->addCodar($codarJson->i18n);
		}
		return $this->codarList;
	}
}
