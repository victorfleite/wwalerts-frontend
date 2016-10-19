<?php

namespace app\modules\backend\models\behaviors;

use yii\db\ActiveRecord;
use yii\base\Behavior;
use app\modules\backend\models\Emergencia;
use app\modules\backend\models\RssCap12;


class FeedBehavior extends Behavior {
	
	public function events() {
		return [ 
				ActiveRecord::EVENT_AFTER_INSERT => 'atualizarFeed',
		];
	}
	public function atualizarFeed($event) {
						
		$rss = new RssCap12;
		$rss->atualizarRss();		
		
	}
}