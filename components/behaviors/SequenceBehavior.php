<?php

namespace app\components\behaviors;

use yii\db\ActiveRecord;
use yii\base\Behavior;
use yii\db\pgsql\Schema;

class SequenceBehavior extends Behavior {
	public $sequence = '';
	

	public function events() {
		return [ 
				ActiveRecord::EVENT_BEFORE_INSERT => 'manageSequence',
		];
	}
	public function manageSequence($event) {
				
		$model = $event->sender;
		$pk = $model->getTableSchema()->primaryKey;
		
		if(is_array($pk)){
			$pk = $pk[0];
		}
		try
		{			
			$query = "SELECT NEXTVAL('{$this->sequence}') AS CURVAL";						
			$result = \Yii::$app->db->createCommand ( $query )->queryOne();
			$model->$pk = $result['curval'];
		}
		catch (Exception $e){
			echo("Error: $e\n");
		}				
	}
}