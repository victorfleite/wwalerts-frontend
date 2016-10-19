<?php

namespace app\modules\backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\backend\models\Emergencia;

/**
 * EmergenciaSearch represents the model behind the search form about `app\models\Emergencia`.
 */
class EmergenciaSearch extends Emergencia {
	const TIPO_ATIVOS = 'ativos';
	const TIPO_TODOS = 'todos';
	public $tipo;
	public function init() {
		parent::init ();
		$this->tipo = self::TIPO_ATIVOS;
	}
	public function emptyDataProvider() {
		$query = Emergencia::find ()->orderBy ( 'sent desc' );
		$dataProvider = new ActiveDataProvider ( [ 
				'query' => $query 
		] );
		return $dataProvider;
	}
	
	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [ 
				[ 
						[ 
								'id',
								'version',
								'codar_id',
								'owner_id',
								'risco_id' 
						],
						'integer' 
				],
				[ 
						[ 
								'tipo',
								'data_inicial',
								'data_modelo',
								'duracao_estimada',
								'location' 
						],
						'safe' 
				],
				[ 
						[ 
								'data_inicial',
								'duracao_estimada' 
						],
						'date',
						'format' => 'dd/MM/yyyy',
				] 
		];
	}
	
	/**
	 * @inheritdoc
	 */
	public function scenarios() {
		// bypass scenarios() implementation in the parent class
		return Model::scenarios ();
	}
	
	/**
	 * Creates data provider instance with search query applied
	 *
	 * @param array $params        	
	 *
	 * @return ActiveDataProvider
	 */
	public function search($get) {
		$query = Emergencia::find ()->orderBy('id desc');
		
		$dataProvider = new ActiveDataProvider ( [ 
				'query' => $query 
		] );
		
		$this->load ( $get );
		if (! $this->validate ()) {
			// uncomment the following line if you do not want to return any records when validation fails
			// $query->where('0=1');
			return $dataProvider;
		}
		if ($this->tipo == self::TIPO_ATIVOS) {
			$query->orWhere ( [
					'encerrado' => null
					] );
			$query->orWhere ( [
					'encerrado' => false
					] );
			
			$query->andWhere ( [ 
					'>=',
					'duracao_estimada',
					date('Y-m-d H:i:s')
			] );
			
		}
		
		// grid filtering conditions
		$query->andFilterWhere ( [ 
				'id' => $this->id,
				'codar_id' => $this->codar_id,
				'risco_id' => $this->risco_id,
				'encerrado' => $this->encerrado 
		] );
		if (! empty ( $this->data_inicial )) {
			$format = 'd/m/Y';
			$date = \DateTime::createFromFormat ( $format, $this->data_inicial );
			// \Yii::$app->dumper->show($this->data_inicial, true);
			// die($this->data_inicial);
			$query->andWhere ( [ 
					'>=',
					'data_inicial',
					$date->format ( 'Y-m-d 00:00:00' ) 
			] );
		}
		if (! empty ( $this->duracao_estimada )) {
			$format = 'd/m/Y';
			$date = \DateTime::createFromFormat ( $format, $this->duracao_estimada );
			$query->andWhere ( [ 
					'<=',
					'duracao_estimada',
					$date->format ( 'Y-m-d 23:59:59' ) 
			] );
		}
		
		return $dataProvider;
	}
}
