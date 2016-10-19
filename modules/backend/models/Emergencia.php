<?php

namespace app\modules\backend\models;

use Yii;
use yii\db\Query;
use yii\db\ActiveRecord;
use yii\validators\DateValidator;
use app\models\Usuario;
use app\components\behaviors\TimestampBehavior;
use app\components\behaviors\SequenceBehavior;
use app\components\behaviors\PolygonBehavior;

/**
 * This is the model class for table "emergencia".
 *
 * @property string $id
 * @property string $version
 * @property string $codar_id
 * @property string $data_inicial
 * @property string $data_modelo
 * @property string $duracao_estimada
 * @property string $location
 * @property string $owner_id
 * @property string $risco_id
 * @property boolean $acao1
 * @property boolean $acao2
 * @property boolean $acao3
 * @property boolean $acao4
 * @property boolean $acao5
 * @property boolean $acao6
 * @property boolean $encerrado
 *
 * @property Acao[] $acaos
 * @property Cap12[] $cap12s
 * @property Codar $codar
 * @property Risco $risco
 * @property Usuario $owner
 * @property string $wkt
 * @property string $descricao
 */
class Emergencia extends ActiveRecord {
	const ENCERRADO_SIM = TRUE;
	const ENCERRADO_NAO = FALSE;
	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return 'emergencia';
	}
	public function init() {
		$this->version = 0;
		$this->data_modelo = date ( 'Y-m-d H:i' );
	}
	
	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [ 
				[ 
						[ 
								'risco_id',
								'codar_id',
								'data_inicial',
								'duracao_estimada',
								'location' 
						],
						'required' 
				],
				[ 
						'location',
						'validarJurisdicaoDoUsuario' 
				],
				[ 
						[ 
								'data_inicial',
								'duracao_estimada' 
						],
						'date',
						'format' => 'dd/MM/yyyy HH:mm' 
				],
				[ 
						[ 
								'data_inicial' 
						],
						'validarDatas' 
				],
				[ 
						[ 
								'id',
								'codar_id',
								'owner_id',
								'risco_id' 
						],
						'integer' 
				],
				[ 
						[ 
								'id',
								'codar_id',
								'risco_id',
								'data_inicial',
								'duracao_estimada',
								'location',
								'descricao' 
						],
						'safe' 
				],
				[ 
						[ 
								'codar_id' 
						],
						'exist',
						'skipOnError' => true,
						'targetClass' => Codar::className (),
						'targetAttribute' => [ 
								'codar_id' => 'id' 
						] 
				],
				[ 
						[ 
								'risco_id' 
						],
						'exist',
						'skipOnError' => true,
						'targetClass' => Risco::className (),
						'targetAttribute' => [ 
								'risco_id' => 'id' 
						] 
				],
				[ 
						[ 
								'owner_id' 
						],
						'exist',
						'skipOnError' => true,
						'targetClass' => Usuario::className (),
						'targetAttribute' => [ 
								'owner_id' => 'id' 
						] 
				] 
		];
	}
	public function validarDatas() {
		$date = new DateValidator ();
		if (! $date->validate ( $this->data_inicial ) || ! $date->validate ( $this->duracao_estimada )) {
			return false;
		}
		$data_inicial = \DateTime::createFromFormat ( 'd/m/Y H:i', $this->data_inicial );
		$duracao_estimada = \DateTime::createFromFormat ( 'd/m/Y H:i', $this->duracao_estimada );
		
		if ($data_inicial->getTimestamp () > $duracao_estimada->getTimestamp ()) {
			$this->addError ( 'data_inicial', \yii::t ( 'app', 'emergencia.message.error.validacao_datas' ) );
			return false;
		}
		return true;
	}
	
	public function validarJurisdicaoDoUsuario(){
		
		$query = "Select polygon_valido_jurisdicao_usuario(:idUsuario, ST_Transform(GeometryFromText(:wkt,3857),4326) ) as validador";
		$res = \yii::$app->db->createCommand ( $query )->bindValue ( ':idUsuario', \yii::$app->user->id )->bindValue ( ':wkt', $this->location )->queryOne ();
		if(empty($res['validador']) || $res['validador'] == False) {
			$this->addError ( 'location', \yii::t ( 'app', 'emergencia.message.error.jurisdicao_nao_permitida' ) );
		}
		
		
		
	}
	
	public function behaviors() {
		return [ 
				'sequence' => [ 
						'class' => SequenceBehavior::className (),
						'sequence' => 'emergency_sequence' 
				],
				'salvarData' => [ 
						'class' => TimestampBehavior::className (),
						'fields' => [ 
								'data_inicial',
								'duracao_estimada' 
						],
						'datetimeFormat' => 'd/m/Y H:i',
						'dateFormat' => 'd/m/Y',
						'dateTimeFormatDataBase' => 'Y-m-d H:i:s',
						'dateFormatDataBase' => 'Y-m-d' 
				],
				'geometry' => [ 
						'class' => PolygonBehavior::className (),
						'attribute' => 'location',
						'type' => PolygonBehavior::GEOMETRY_POLYGON 
				] 
		];
	}
	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [ 
				'id' => \yii::t ( 'app', 'emergencia.id' ),
				'version' => \yii::t ( 'app', 'emergencia.version' ),
				'codar_id' => \yii::t ( 'app', 'emergencia.codar_id' ),
				'data_inicial' => \yii::t ( 'app', 'emergencia.data_inicial' ),
				'data_modelo' => \yii::t ( 'app', 'emergencia.data_model' ),
				'duracao_estimada' => \yii::t ( 'app', 'emergencia.duracao_estimada' ),
				'location' => \yii::t ( 'app', 'emergencia.location' ),
				'owner_id' => \yii::t ( 'app', 'emergencia.owner_id' ),
				'risco_id' => \yii::t ( 'app', 'emergencia.risco_id' ),
				'encerrado' => \yii::t ( 'app', 'emergencia.encerrado' ),
				'descricao' => \yii::t ( 'app', 'emergencia.descricao' ) 
		];
	}
	
	/**
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getEmergenciasLog() {
		return $this->hasMany ( EmergenciaLog::className (), [ 
				'emergencia_id' => 'id' 
		] );
	}
	
	/**
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getCap12s() {
		return $this->hasMany ( Cap12::className (), [ 
				'emergencia_id' => 'id' 
		] );
	}
	
	/**
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getCodar() {
		return $this->hasOne ( Codar::className (), [ 
				'id' => 'codar_id' 
		] );
	}
	
	/**
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getRisco() {
		return $this->hasOne ( Risco::className (), [ 
				'id' => 'risco_id' 
		] );
	}
	
	/**
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getOwner() {
		return $this->hasOne ( Usuario::className (), [ 
				'id' => 'owner_id' 
		] );
	}
	/**
	 * Recuperar Array de Messoregioes a partir de uma geometria
	 *
	 * @return Ambigous <multitype:, \yii\db\mixed, mixed>
	 */
	public function getArrayMesoregioes() {
		$query = "Select " . "c.nome AS nomemesorregiao " . "FROM mesorregiao c INNER JOIN emergencia e " . "ON (st_intersects(c.the_geom, st_transform(st_setsrid(e.location, 97483), 4326))) " . "LEFT JOIN country s " . "ON s.id=c.country_id " . "WHERE e.id = :id " . "AND c.country_id=:country;";
		
		$res = Yii::$app->db->createCommand ( $query )->bindValue ( ':id', $this->id )->bindValue ( ':country', $this->owner->instituicao->pais )->queryAll ();
		
		return $res;
	}
	/**
	 * Recuperar os nomes separados por virgula de uma geometria
	 */
	public function getStringMesoregioes() {
		$res = $this->getArrayMesoregioes ();
		
		$mesorregioes = array ();
		if (! empty ( $res )) {
			foreach ( $res as $mesoregiao ) {
				$mesorregioes [] = $mesoregiao ['nomemesorregiao'];
			}
		}
		return implode ( ",", $mesorregioes );
	}
	/**
	 * Transforma Geometria (Binario) para WKT
	 *
	 * @return Ambigous <>|string
	 */
	public function geometryToWKT() {
		if (! empty ( $this->location )) {
			$q = "SELECT ST_AsText(ST_Transform(GeometryFromText('" . $this->location . "',3857),4326)) as location";
			$res = Yii::$app->db->createCommand ( $q )->queryOne ();
			return $res ['location'];
		}
		return '';
	}
	public function encerrar() {
		return \Yii::$app->db->createCommand ( "UPDATE " . Emergencia::tableName () . " SET encerrado=true WHERE id=:id" )->bindValue ( ':id', $this->id )->execute ();
	}
	static function getEmergenciasHoje($idCountry) {
		$dataHoje = date ( 'Y-m-d' );
		
		$query = " SELECT " . " DISTINCT " . " s.country_id," . " s.name AS uf_nome," . " s.abbreviation AS uf_sigla," . " e.id AS id_emergencia," . " e.descricao AS emergencia_descricao, " . " e.data_inicial, " . " e.duracao_estimada," . " co.descricao as codar," . " co.icone_path as codar_icone_path," . " co.i18n as codar_i18n," . " co.classcss as codar_classcss," . " r.descricao as risco," . " r.i18n as risco_i18n," . " r.rgb as risco_hex," . " r.classcss as risco_classcss," . " ST_asGeoJson(e.location) as geo_json," . " ST_AsText(ST_Centroid(e.location)) as centroid, " . " i.desc_ptb as recomendacoes " . " FROM " . " state s," . " city c," . " emergencia e," . " (SELECT id FROM emergencia em WHERE (em.data_inicial <= TIMESTAMP '$dataHoje 23:59:59' AND ((em.duracao_estimada > now() AND em.duracao_estimada <= TIMESTAMP '$dataHoje 23:59:59') OR em.duracao_estimada > TIMESTAMP '$dataHoje 23:59:59')) AND  (em.encerrado ISNULL OR em.encerrado = FALSE)) AS result_e, " . " codar co," . " risco r, " . " cap_1_2_instruction i" . " WHERE " . " st_intersects(c.the_geom, e.location)  AND " . " e.id = result_e.id  AND " . " e.codar_id = co.id  AND " . " e.risco_id = r.id  AND " . " c.state_id = s.id  AND " . " s.country_id = $idCountry  AND " . " i.codar_id = co.id AND " . " i.risco_id = r.id AND " . " s.abbreviation != 'NULL'" . " ORDER BY s.abbreviation ASC ";
		
		// Yii::$app->dumper->show($query, true);
		$res = \Yii::$app->db->createCommand ( $query )->queryAll ();
		
		$arrEmergencias = array ();
		// Pre-processar resultado
		foreach ( $res as $item ) {
			
			$data_inicial = \DateTime::createFromFormat ( 'Y-m-d H:i:s', $item ['data_inicial'] );
			$duracao_estimada = \DateTime::createFromFormat ( 'Y-m-d H:i:s', $item ['duracao_estimada'] );
			
			$arrEmergencias [$item ['id_emergencia']] ['ufs'] [] = array (
					'uf_nome' => $item ['uf_nome'],
					'uf_sigla' => $item ['uf_sigla'] 
			);
			$arrEmergencias [$item ['id_emergencia']] ['emergencia'] = array (
					'id_emergencia' => $item ['id_emergencia'],
					'emergencia_descricao' => $item ['emergencia_descricao'],
					'data_inicial' => $data_inicial->format ( 'd-m-Y H:i' ),
					'duracao_estimada' => $duracao_estimada->format ( 'd-m-Y H:i' ),
					'codar' => $item ['codar'],
					'codar_icone_path' => $item ['codar_icone_path'],
					'codar_i18n' => $item ['codar_i18n'],
					'codar_classcss' => $item ['codar_classcss'],
					'risco' => $item ['risco'],
					'risco_hex' => $item ['risco_hex'],
					'risco_i18n' => $item ['risco_i18n'],
					'risco_classcss' => $item ['risco_classcss'],
					'geo_json' => $item ['geo_json'],
					'centroid' => $item ['centroid'],
					'recomendacoes' => $item ['recomendacoes']
					//'documento' => $item ['documento'] 
			);
		}
		
		return $arrEmergencias;
	}
	static function getEmergenciasAmanha($idCountry) {
		$dataAmanha = Date ( 'Y-m-d', strtotime ( "1 day" ) );
		
		$query = " SELECT " . " DISTINCT " . " s.country_id," . " s.name AS uf_nome," . " s.abbreviation AS uf_sigla," . " e.id AS id_emergencia," . " e.descricao AS emergencia_descricao, " . " e.data_inicial, " . " e.duracao_estimada," . " co.descricao as codar," . " co.icone_path as codar_icone_path," . " co.i18n as codar_i18n," . " co.classcss as codar_classcss," . " r.descricao as risco," . " r.i18n as risco_i18n," . " r.rgb as risco_hex," . " r.classcss as risco_classcss," . " ST_asGeoJson(e.location) as geo_json," . " ST_AsText(ST_Centroid(st_transform(GeometryFromText(e.location, 3857),4326))) as centroid, " . " i.desc_ptb as recomendacoes " . " FROM " . " state s," . " city c," . " emergencia e," . " (SELECT id FROM emergencia em WHERE (em.data_inicial <= TIMESTAMP '$dataAmanha 23:59:59' AND ((em.duracao_estimada > now() AND (em.duracao_estimada >= TIMESTAMP '$dataAmanha 00:00:00' AND em.duracao_estimada <= TIMESTAMP '$dataAmanha 23:59:59')) OR em.duracao_estimada > TIMESTAMP '$dataAmanha 23:59:59')) AND  (em.encerrado ISNULL OR em.encerrado = FALSE)) AS result_e, " . " codar co," . " risco r, " . " cap_1_2_instruction i" . " WHERE " . " (st_intersects(c.the_geom, st_transform(st_setsrid(e.location, 97483), 4326)))  AND " . " e.id = result_e.id  AND " . " e.codar_id = co.id  AND " . " e.risco_id = r.id  AND " . " c.state_id = s.id  AND " . " i.codar_id = co.id AND " . " i.risco_id = r.id AND " . " s.country_id = $idCountry  AND " . " s.abbreviation != 'NULL'" . " ORDER BY s.abbreviation ASC ";
		
		// \Yii::$app->dumper->show($query, true);
		$res = \Yii::$app->db->createCommand ( $query )->queryAll ();
		
		$arrEmergencias = array ();
		// Pre-processar resultado
		foreach ( $res as $item ) {
			
			$data_inicial = \DateTime::createFromFormat ( 'Y-m-d H:i:s', $item ['data_inicial'] );
			$duracao_estimada = \DateTime::createFromFormat ( 'Y-m-d H:i:s', $item ['duracao_estimada'] );
			
			$arrEmergencias [$item ['id_emergencia']] ['ufs'] [] = array (
					'uf_nome' => $item ['uf_nome'],
					'uf_sigla' => $item ['uf_sigla'] 
			);
			$arrEmergencias [$item ['id_emergencia']] ['emergencia'] = array (
					'id_emergencia' => $item ['id_emergencia'],
					'emergencia_descricao' => $item ['emergencia_descricao'],
					'data_inicial' => $data_inicial->format ( 'd-m-Y H:i' ),
					'duracao_estimada' => $duracao_estimada->format ( 'd-m-Y H:i' ),
					'codar' => $item ['codar'],
					'codar_icone_path' => $item ['codar_icone_path'],
					'codar_i18n' => $item ['codar_i18n'],
					'codar_classcss' => $item ['codar_classcss'],
					'risco' => $item ['risco'],
					'risco_hex' => $item ['risco_hex'],
					'risco_i18n' => $item ['risco_i18n'],
					'risco_classcss' => $item ['risco_classcss'],
					'geo_json' => $item ['geo_json'],
					'centroid' => $item ['centroid'],
					'recomendacoes' => $item ['recomendacoes'] 
			);
		}
		
		return $arrEmergencias;
	}
	static function getEmergenciasFuturo($idCountry) {
		$dataHoje = Date ( 'Y-m-d' );
		$dataAmanha = Date ( 'Y-m-d', strtotime ( "1 day" ) );
		
		$query = " SELECT " . " DISTINCT " . " s.country_id," . " s.name AS uf_nome," . " s.abbreviation AS uf_sigla," . " e.id AS id_emergencia," . " e.descricao AS emergencia_descricao, " . " e.data_inicial, " . " e.duracao_estimada," . " co.descricao as codar," . " co.icone_path as codar_icone_path," . " co.i18n as codar_i18n," . " co.classcss as codar_classcss," . " r.descricao as risco," . " r.i18n as risco_i18n," . " r.rgb as risco_hex," . " r.classcss as risco_classcss," . " ST_asGeoJson(e.location) as geo_json," . " ST_AsText(ST_Centroid(st_transform(GeometryFromText(e.location, 3857),4326))) as centroid, " . " i.desc_ptb as recomendacoes " . " FROM " . " state s," . " city c," . " emergencia e," . " (SELECT id FROM emergencia em WHERE ((em.data_inicial <= TIMESTAMP '$dataHoje 23:59:59' OR em.data_inicial >= TIMESTAMP '$dataHoje 23:59:59') AND (em.duracao_estimada > now() AND em.duracao_estimada >= TIMESTAMP '$dataAmanha 23:59:59') AND (em.encerrado ISNULL OR em.encerrado = FALSE))) AS result_e, " . " codar co," . " risco r, " . " cap_1_2_instruction i" . " WHERE " . " (st_intersects(c.the_geom, st_transform(st_setsrid(e.location, 97483), 4326)))  AND " . " e.id = result_e.id  AND " . " e.codar_id = co.id  AND " . " e.risco_id = r.id  AND " . " c.state_id = s.id  AND " . " i.codar_id = co.id AND " . " i.risco_id = r.id AND " . " s.country_id = $idCountry  AND " . " s.abbreviation != 'NULL'" . " ORDER BY s.abbreviation ASC ";
		
		// \Yii::$app->dumper->show($query, true);
		$res = \Yii::$app->db->createCommand ( $query )->queryAll ();
		
		$arrEmergencias = array ();
		// Pre-processar resultado
		foreach ( $res as $item ) {
			
			$data_inicial = \DateTime::createFromFormat ( 'Y-m-d H:i:s', $item ['data_inicial'] );
			$duracao_estimada = \DateTime::createFromFormat ( 'Y-m-d H:i:s', $item ['duracao_estimada'] );
			
			$arrEmergencias [$item ['id_emergencia']] ['ufs'] [] = array (
					'uf_nome' => $item ['uf_nome'],
					'uf_sigla' => $item ['uf_sigla'] 
			);
			$arrEmergencias [$item ['id_emergencia']] ['emergencia'] = array (
					'id_emergencia' => $item ['id_emergencia'],
					'emergencia_descricao' => $item ['emergencia_descricao'],
					'data_inicial' => $data_inicial->format ( 'd-m-Y H:i' ),
					'duracao_estimada' => $duracao_estimada->format ( 'd-m-Y H:i' ),
					'codar' => $item ['codar'],
					'codar_icone_path' => $item ['codar_icone_path'],
					'codar_i18n' => $item ['codar_i18n'],
					'codar_classcss' => $item ['codar_classcss'],
					'risco' => $item ['risco'],
					'risco_hex' => $item ['risco_hex'],
					'risco_i18n' => $item ['risco_i18n'],
					'risco_classcss' => $item ['risco_classcss'],
					'geo_json' => $item ['geo_json'],
					'centroid' => $item ['centroid'],
					'recomendacoes' => $item ['recomendacoes'] 
			);
		}
		
		return $arrEmergencias;
	}
	static function getEmergencia($id) {
		$query = " SELECT " . " DISTINCT " . " s.country_id," . " s.name AS uf_nome," . " s.abbreviation AS uf_sigla," . " e.id AS id_emergencia," . " e.descricao AS emergencia_descricao, " . " e.data_inicial, " . " e.duracao_estimada," . " co.descricao as codar," . " co.icone_path as codar_icone_path," . " co.i18n as codar_i18n," . " co.classcss as codar_classcss," . " r.descricao as risco," . " r.i18n as risco_i18n," . " r.rgb as risco_hex," . " r.classcss as risco_classcss," . " ST_asGeoJson(e.location) as geo_json," . " ST_AsText(ST_Centroid(st_transform(GeometryFromText(e.location, 3857),4326))) as centroid, " . " i.desc_ptb as recomendacoes " . " FROM " . " state s," . " city c," . " emergencia e," . " codar co," . " risco r, " . " cap_1_2_instruction i" . " WHERE " . " (st_intersects(c.the_geom, st_transform(st_setsrid(e.location, 97483), 4326)))  AND " . " e.codar_id = co.id  AND " . " e.risco_id = r.id  AND " . " c.state_id = s.id  AND " . " i.codar_id = co.id AND " . " i.risco_id = r.id AND " . " e.id = :id AND" . " (e.encerrado ISNULL OR e.encerrado = FALSE) AND " . " s.abbreviation != 'NULL'" . " ORDER BY s.abbreviation ASC ";
		
		// \Yii::$app->dumper->show($query, true);
		$res = \Yii::$app->db->createCommand ( $query )->bindValue ( ':id', $id )->queryAll ();
		
		$arrEmergencias = array ();
		// Pre-processar resultado
		foreach ( $res as $item ) {
			
			$data_inicial = \DateTime::createFromFormat ( 'Y-m-d H:i:s', $item ['data_inicial'] );
			$duracao_estimada = \DateTime::createFromFormat ( 'Y-m-d H:i:s', $item ['duracao_estimada'] );
			
			$arrEmergencias [$item ['id_emergencia']] ['ufs'] [] = array (
					'uf_nome' => $item ['uf_nome'],
					'uf_sigla' => $item ['uf_sigla'] 
			);
			$arrEmergencias [$item ['id_emergencia']] ['emergencia'] = array (
					'id_emergencia' => $item ['id_emergencia'],
					'emergencia_descricao' => $item ['emergencia_descricao'],
					'data_inicial' => $data_inicial->format ( 'd-m-Y H:i' ),
					'duracao_estimada' => $duracao_estimada->format ( 'd-m-Y H:i' ),
					'codar' => $item ['codar'],
					'codar_icone_path' => $item ['codar_icone_path'],
					'codar_i18n' => $item ['codar_i18n'],
					'codar_classcss' => $item ['codar_classcss'],
					'risco' => $item ['risco'],
					'risco_hex' => $item ['risco_hex'],
					'risco_i18n' => $item ['risco_i18n'],
					'risco_classcss' => $item ['risco_classcss'],
					'geo_json' => $item ['geo_json'],
					'centroid' => $item ['centroid'],
					'recomendacoes' => $item ['recomendacoes'] 
			);
		}
		
		return $arrEmergencias [$id];
	}
	static function getMaxCapFromEmergencia($idEmergencia) {
		$query = " SELECT id, areadesc, identifier, max(sequencecap) m FROM cap1_2 where emergencia_id = $idEmergencia group by id, identifier, areadesc ";
		return \Yii::$app->db->createCommand ( $query )->queryOne ();
	}
	
	/**
	 * Recupera String com Nomes de Municipios a partir da Geometria
	 * 
	 * @return string
	 */
	static function getMunicipios($idEmergencia) {
		$res = self::getArrayMunicipios ( $idEmergencia );
		
		$cidades = array ();
		foreach ( $res as $cidade ) {
			if (! empty ( $cidade ["estado"] )) {
				$cidades [] = $cidade ["nome"] . " - " . $cidade ["uf"] . " (" . $cidade ["geocode"] . ")";
			} else {
				$cidades [] = $cidade ["nome"];
			}
		}
		return implode ( ',', $cidades );
	}
	static function getArrayMunicipios($idEmergencia) {
		$query = "Select " . "(c.geocode :: bigint) as geocode, " . "c.name as nome, " . "s.abbreviation as uf " . "from state s, city c " . " INNER JOIN emergencia e ON (st_intersects(c.the_geom, e.location)) " . " where e.id = :id and c.state_id = s.id and s.abbreviation != 'NULL' ORDER BY uf asc, nome ASC;";
		
		$res = Yii::$app->db->createCommand ( $query )->bindValue ( ':id', $idEmergencia )->queryAll ();
		
		return $res;
	}
	
	
}
