<?php

namespace app\modules\frontend\models;

use Yii;
use yii\base\Model;
use app\modules\backend\models\Codar;
use yii\helpers\Json;
use app\components\Util;

class CodarJson extends Model{
		
	public $i18n;
	
	public function addI18n($language, $cobrade, $class){
		$this->i18n[$language] =  array('codar'=>$cobrade, 'class'=>$class);
	}
	
		
}

