<?php
namespace app\components;

use yii\base\Component;
use \yii\helpers\VarDumper;

class Dumper extends Component{
		
	public function show($obj, $stop = true){
		echo "<pre>";
		VarDumper::dump($obj);
		echo "</pre>";
		if($stop){
			die();
		}
	}
	
}