<?php

namespace app\modules\backend\models;

use Yii;
use yii\db\Query;
use yii\helpers\FileHelper;
use app\components\Writer;
use app\components\behaviors\SequenceBehavior;
use app\components\behaviors\PolygonBehavior;
use app\modules\backend\models\behaviors\FeedBehavior;
use app\modules\backend\models\behaviors\EmailCapBehavior;
use app\models\Usuario;

/**
 * This is the model class for table "cap1_2".
 *
 * @property string $id
 * @property string $emergencia_id
 * @property string $instituicao_id
 * @property string $risco_id
 * @property string $codar_id
 * @property string $identifier
 * @property string $sent
 * @property string $sender
 * @property string $status
 * @property string $msgtype
 * @property string $scope
 * @property string $category
 * @property string $event
 * @property string $responsetype
 * @property string $urgency
 * @property string $severity
 * @property string $certainty
 * @property string $onset
 * @property string $expires
 * @property string $sendername
 * @property string $headline
 * @property string $instruction
 * @property string $description
 * @property string $contact
 * @property string $areadesc
 * @property string $polygon
 * @property string $usuario_id
 * @property string $version
 * @property string $sequencecap
 * @property string $cap1_2_id_pai
 * @property string $language
 * @property string $polygontext
 * @property string $address_cap
 *
 * @property Codar $codar
 * @property Emergencia $emergencia
 * @property Instituicao $instituicao
 * @property Risco $risco
 * @property Usuario $usuario
 */
class Cap12 extends \yii\db\ActiveRecord {
	const SCOPE_PUBLIC = "Public";
	const STATUS_ACTUAL = "Actual";
	const STATUS_EXERCISE = "Exercise";
	const STATUS_SYSTEM = "System";
	const STATUS_TEST = "Test";
	const STATUS_DRAFT = "Draft";
	const MESSAGE_TYPE_ALERT = "Alert";
	const MESSAGE_TYPE_UPDATE = "Update";
	const MESSAGE_TYPE_CANCEL = "Cancel";
	const CATEGORY_GEO = "Geo"; // Geológico
	const CATEGORY_MET = "Met"; // Meteorológico
	const CATEGORY_SAFETY = "Safety"; // Emergencia Publica
	const CATEGORY_SECURITY = "Security"; // Emergencia Militar Segurança Nacional
	const CATEGORY_RESCUE = "Rescue"; // Salvamento e Resgate
	const CATEGORY_FIRE = "Fire"; // Combate ao Fogo
	const CATEGORY_HEALTH = "Health"; // Saude Publica
	const CATEGORY_ENV = "Env"; // Poluição no Ambiente
	const CATEGORY_TRANSPORT = "Transport"; // Transporte Publico
	const CATEGORY_INFRA = "Infra"; // Infraestrutura
	const CATEGORY_CBRNE = "CBRNE"; // Risco de Explosão, Quimico, Biológico e Nuclear
	const RESPONSETYPE_SHELTER = "Shelter"; // Abrigar-se
	const RESPONSETYPE_EVACUATE = "Evacuate"; // Realocação
	const RESPONSETYPE_PREPARE = "Prepare"; // Prepara-se
	const RESPONSETYPE_EXECUTE = "Execute"; // Executar
	const RESPONSETYPE_AVOID = "Avoid"; // Evitar
	const RESPONSETYPE_MONITOR = "Monitor"; // Monitorar
	const RESPONSETYPE_ASSESS = "Assess"; // Avaliar Informações
	const RESPONSETYPE_ALLCLEAR = "AllClear"; // Evento cessou
	const RESPONSETYPE_NONE = "None"; // Nenhum tipo de Resposta
	const URGENCY_IMMEDIATE = "Immediate"; // Evento Imadiato
	const URGENCY_EXPECTED = "Expected"; // Evento que pode acontecer na próxima hora
	const URGENCY_FUTURE = "Future"; // Evento deve acontecer no futuro próximo
	const URGENCY_PAST = "Past"; // Evento Passado
	const SEVERITY_EXTREME = "Extreme"; // Grande ameaça a vida
	const SEVERITY_SEVERE = "Severe"; // Significante ameaça a vida
	const SEVERITY_MODERATE = "Moderate"; // Possivel ameaça a vida
	const SEVERITY_MINOR = "Minor"; // Mínima ameaça a vida
	const SEVERITY_EXTREME_COLOR_RGB = "#FF0000";
	const SEVERITY_SEVERE_COLOR_RGB = "#FF9933";
	const SEVERITY_MODERATE_COLOR_RGB = "#FFFF00";
	const SEVERITY_MINOR_COLOR_RGB = "#00FF00";
	const CERTAINTY_OBSERVED = "Observed"; // Com certeza irá acontecer
	const CERTAINTY_LIKELY = "Likely"; // Probabilidade acima de 50% de acontecer
	const CERTAINTY_POSSIBLE = "Possible"; // Possibilidade com menos de 50% de acontecer
	const CERTAINTY_UNLIKELY = "Unlikely"; // Improvavel de acontecer, ~0% de chance
	const INSTRUCTION = "Contate a Defesa Civil.";
	const XML_DECLARATION = "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"no\"?>";
	const CAP12_XMLNS = "urn:oasis:names:tc:emergency:cap:1.2";
	
	const EXTENSAO_DOCUMENTO_PDF = '.pdf';
	
	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return 'cap1_2';
	}
	public function init() {
		$this->version = 0;
	}
	
	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [ 
				[ 
						[ 
								'emergencia_id',
								'instituicao_id',
								'risco_id',
								'codar_id',
								'identifier',
								'sender',
								'status',
								'msgtype',
								'scope',
								'category',
								'event',
								'urgency',
								'severity',
								'certainty',
								'contact',
								'areadesc',
								'usuario_id',
								'version',
								'language' 
						],
						'required' 
				],
				[ 
						[ 
								'id',
								'emergencia_id',
								'instituicao_id',
								'risco_id',
								'codar_id',
								'usuario_id',
								'version',
								'sequencecap',
								'cap1_2_id_pai' 
						],
						'integer' 
				],
				[ 
						[ 
								'id',
								'sent',
								'onset',
								'expires' 
						],
						'safe' 
				],
				[ 
						[ 
								'instruction',
								'description',
								'areadesc',
								'polygon',
								'polygontext' 
						],
						'string' 
				],
				[ 
						[ 
								'identifier',
								'sender',
								'status',
								'scope',
								'category',
								'responsetype',
								'urgency',
								'severity',
								'certainty' 
						],
						'string',
						'max' => 50 
				],
				[ 
						[ 
								'msgtype' 
						],
						'string',
						'max' => 20 
				],
				[ 
						[ 
								'event',
								'headline' 
						],
						'string',
						'max' => 100 
				],
				[ 
						[ 
								'sendername',
								'contact' 
						],
						'string',
						'max' => 150 
				],
				[ 
						[ 
								'language' 
						],
						'string',
						'max' => 15 
				],
				[ 
						[ 
								'address_cap' 
						],
						'string',
						'max' => 300 
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
								'emergencia_id' 
						],
						'exist',
						'skipOnError' => true,
						'targetClass' => Emergencia::className (),
						'targetAttribute' => [ 
								'emergencia_id' => 'id' 
						] 
				],
				[ 
						[ 
								'instituicao_id' 
						],
						'exist',
						'skipOnError' => true,
						'targetClass' => Instituicao::className (),
						'targetAttribute' => [ 
								'instituicao_id' => 'id' 
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
								'usuario_id' 
						],
						'exist',
						'skipOnError' => true,
						'targetClass' => Usuario::className (),
						'targetAttribute' => [ 
								'usuario_id' => 'id' 
						] 
				] 
		];
	}
	public function behaviors() {
		return [ 
				'sequence' => [ 
						'class' => SequenceBehavior::className (),
						'sequence' => 'cap1_2_id_seq' 
				],
				'geometry' => [ 
						'class' => PolygonBehavior::className (),
						'attribute' => 'polygon',
						'type' => PolygonBehavior::GEOMETRY_POLYGON 
				],
				'sendEmail' => [
						'class' => EmailCapBehavior::className(),				
				],
				'feed' => [
						'class' => FeedBehavior::className()
				],
		];
	}
	
	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [ 
				'id' => 'ID',
				'emergencia_id' => 'Emergencia ID',
				'instituicao_id' => 'Instituicao ID',
				'risco_id' => 'Risco ID',
				'codar_id' => 'Codar ID',
				'identifier' => 'Identifier',
				'sent' => 'Sent',
				'sender' => 'Sender',
				'status' => 'Status',
				'msgtype' => 'Msgtype',
				'scope' => 'Scope',
				'category' => 'Category',
				'event' => 'Event',
				'responsetype' => 'Responsetype',
				'urgency' => 'Urgency',
				'severity' => 'Severity',
				'certainty' => 'Certainty',
				'onset' => 'Onset',
				'expires' => 'Expires',
				'sendername' => 'Sendername',
				'headline' => 'Headline',
				'instruction' => 'Instruction',
				'description' => 'Description',
				'contact' => 'Contact',
				'areadesc' => 'Areadesc',
				'polygon' => 'Polygon',
				'usuario_id' => 'Usuario ID',
				'version' => 'Version',
				'sequencecap' => 'Sequencecap',
				'cap1_2_id_pai' => 'Cap1 2 Id Pai',
				'language' => 'Language',
				'polygontext' => 'Polygontext',
				'address_cap' => 'Address Cap' 
		];
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
	public function getEmergencia() {
		return $this->hasOne ( Emergencia::className (), [ 
				'id' => 'emergencia_id' 
		] );
	}
	
	/**
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getInstituicao() {
		return $this->hasOne ( Instituicao::className (), [ 
				'id' => 'instituicao_id' 
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
	public function getUsuario() {
		return $this->hasOne ( Usuario::className (), [ 
				'id' => 'usuario_id' 
		] );
	}
	/**
	 * Montar o Indetifier
	 * 
	 * @param unknown $siglaInstituicao        	
	 * @param unknown $idEmergencia        	
	 * @param unknown $type        	
	 * @param unknown $oldsequencecap        	
	 * @return number
	 */
	public function getIdentifier($siglaInstituicao, $idEmergencia, $sequencecap) {
		return $siglaInstituicao . '.' . date ( 'Y' ) . '.' . $idEmergencia . '.' . $sequencecap;
	}
	/**
	 * Montar Headline
	 * 
	 * @param unknown $codarDescricao        	
	 * @param unknown $riscoDescricao        	
	 * @return string
	 */
	public function getHeadline($codarDescricao, $riscoDescricao) {
		return 'Aviso de ' . $codarDescricao . '. Severidade Grau: ' . $riscoDescricao;
	}
	/**
	 * Montar Area Desc
	 * 
	 * @param unknown $mesoregioes        	
	 * @return string
	 */
	public function getAreaDesc($mesoregioes) {
		return 'Aviso para as áreas: ' . $mesoregioes;
	}
	/**
	 * Recupera a Quantidades de Caps existentes para uma emergencia
	 * 
	 * @param unknown $emergencia_id        	
	 */
	public function getQuantidadeCaps($emergencia_id) {
		$query = new Query ();
		$query->from ( self::tableName () )->where ( [ 
				'emergencia_id' => $emergencia_id 
		] );
		return $query->count ();
	}
	
	/**
	 * Retorna Ultimo Cap Gerado
	 */
	public function getUltimoCap($emergencia_id) {
		$max = self::find ()->where ( [ 
				'emergencia_id' => $emergencia_id 
		] )->orderBy ( 'sequencecap desc' )->one ();
		return $max;
	}
	
	/**
	 * Retorna Next Cap Sequence
	 */
	public function getNextSequence($emergencia_id) {
		$max = self::find ()->where ( [ 
				'emergencia_id' => $emergencia_id 
		] )->orderBy ( 'sequencecap desc' )->one ();
		return ($max ['sequencecap'] + 1);
	}
	
	/**
	 * Recupera Cor RGB do Risco
	 * @throws \Exception
	 * @return string
	 */
	public function getColorRisk(){	
		switch ($this->severity) {
			case self::SEVERITY_EXTREME:
				return self::SEVERITY_EXTREME_COLOR_RGB;
				break;
			case self::SEVERITY_SEVERE:
				return self::SEVERITY_SEVERE_COLOR_RGB;
				break;
			case self::SEVERITY_MODERATE:
				return self::SEVERITY_MODERATE_COLOR_RGB;
				break;
			case self::SEVERITY_MINOR:
				return self::SEVERITY_MINOR_COLOR_RGB;
				break;
			default:
				throw new \Exception('Color Risk doesnt exist');
		}
	}
	
	/**
	 * Gerar Xml do CAP
	 * @return string
	 */
	public function gerarXMLCAP12() {
		$writer = new Writer ();
		
		$polygonXY; // Tem os pares divididos (Y,X)
		$polygonPair; // Tem os pares juntos (Y+X)
		$polygonReady; // Monta o polygono em lon/lat X+Y
		
		$newPolygon = str_replace ( "POLYGON((", "", $this->polygontext );
		$newPolygon = str_replace ( "))", "", $newPolygon );
		
		$polygonReady = "";
		$polygonPair = explode ( ",", $newPolygon );
		for($i = 0; $i < count ( $polygonPair ); $i ++) {
			$polygonXY = explode ( " ", $polygonPair [$i] );
			$polygonReady .= $polygonXY [1] . "," . $polygonXY [0];
		}
		$polygonReady = substr ( $polygonReady, 0, strlen ( $polygonReady ) - 1 );
		
		$sentDate = \DateTime::createFromFormat('Y-m-d H:i', $this->sent);
		$onSetDate = \DateTime::createFromFormat('Y-m-d H:i:s', $this->onset);
		$onExpiresDate = \DateTime::createFromFormat('Y-m-d H:i:s', $this->expires);
		
    	$writer->writeln(self::XML_DECLARATION)
    	->writeln("<alert xmlns=\"" . self::CAP12_XMLNS . "\">")
    	->writeln("  <identifier>".$this->identifier."</identifier>")
    	->writeln("  <sender>".$this->sender."</sender>")
    	->writeln("  <sent>".$sentDate->format("Y-m-d")."T".$sentDate->format("H:i:s")."-03:00</sent>")
    	->writeln("  <status>".$this->status."</status>")
		->writeln("  <msgType>".$this->msgtype."</msgType>")
    	->writeln("  <scope>".$this->scope."</scope>")
		->writeln("  <info>")
    	->writeln("    <language>".$this->language."</language>")
    	->writeln("    <category>".$this->category."</category>")
    	->writeln("    <event>".$this->event."</event>")
    	->writeln("    <responseType>".$this->responsetype."</responseType>")
    	->writeln("    <urgency>".$this->urgency."</urgency>")
    	->writeln("    <severity>".$this->severity."</severity>")
    	->writeln("    <certainty>".$this->certainty."</certainty>")
    	->writeln("    <onset>".$onSetDate->format("Y-m-d")."T".$onSetDate->format("H:i:s")."-03:00</onset>")
		->writeln("    <expires>".$onExpiresDate->format("Y-m-d")."T".$onExpiresDate->format("H:i:s")."-03:00</expires>")
		->writeln("    <senderName>".$this->sendername."</senderName>")
		->writeln("    <headline>".$this->headline."</headline>")
    	->writeln("    <description>".$this->description."</description>")
    	->writeln("    <instruction>".$this->instruction."</instruction>")
    	->writeln("    <web>http://alert-as.inmet.gov.br/cv/emergencia/cap/".$this->id."</web>")
    	->writeln("    <contact>".$this->contact."</contact>")
    	->writeln("    <parameter><valueName>ColorRisk</valueName><value>".$this->getColorRisk()."</value></parameter>")
		->writeln("    <parameter><valueName>TimeStampDateOnSet</valueName><value>".$onSetDate->getTimestamp()."</value></parameter>")
		->writeln("    <parameter><valueName>TimeStampDateExpires</valueName><value>".$onExpiresDate->getTimestamp()."</value></parameter>")
		->writeln("    <parameter><valueName>Municipios</valueName><value>".Emergencia::getMunicipios($this->emergencia_id)."</value></parameter>")
		->writeln("    <area>")
    	->writeln("      <areaDesc>".$this->areadesc."</areaDesc>")
    	->writeln("      <polygon>".$polygonReady."</polygon>")
    	->writeln("    </area>")
    	->writeln("  </info>")
    	->writeln("</alert>");
		return $writer->getString();
	}
	
	
	/**
	 * Salvar Xml do CAP em arquivo
	 * @throws \Exception
	 * @return string
	 */
	public function saveXML(){
		$path = "/var/www/html/cap_12";
		$xml = $this->gerarXMLCAP12();
	
		$siglacap = $this->instituicao->siglacap;
		$anoFolder = date('Y');
		$mesFolder = date('m');
		$diaFolder = date('d');
	
		$writer = new Writer();
		$writer->write($path . DIRECTORY_SEPARATOR)
				  ->write($anoFolder . DIRECTORY_SEPARATOR)
				  ->write($mesFolder . DIRECTORY_SEPARATOR)
				  ->write($diaFolder . DIRECTORY_SEPARATOR)
				  ->write($siglacap . DIRECTORY_SEPARATOR);
				
		$nomePasta = $writer->getString();
		$fileHelper = new FileHelper();
		$fileHelper->createDirectory($nomePasta, 755, true);	
		
		$nomeArquivo = $nomePasta. $this->identifier.".xml";
		try {
			$this->salvarArquivo($nomeArquivo, $xml);
			
		} catch (Exception $e) {
			throw new \Exception("Erro ao tentar salvar o arquivo xml do cap");
		}
		return str_replace("/var/www/html/", "", $nomeArquivo);	
	}

	/**
	 * Salva String em Arquivo no Disco
	 * @param unknown $nomeArquivo
	 * @param unknown $valor
	 */
	public function salvarArquivo($nomeArquivo, $valor) {
		$fh = fopen($nomeArquivo, 'w') or die("can't open file");
		fwrite($fh, $valor);
		fclose($fh);
	}
	
	static function getCapsHoje(){
		
		$dataHoje = date('Y-m-d');
		
		$query = " SELECT ".
				 //" ca.id, ".
				 " ca.address_cap, ".
				 " ca.event, ".
				 " d.marcador ".
				 " FROM ".
				 " cap1_2 as ca, ".
				 " cap_1_2_description as d, ".
				 " (SELECT emergencia_id, max(sequencecap) m FROM cap1_2 ".
				 "  	WHERE (onset<= TIMESTAMP '".$dataHoje." 23:59:59' AND ".
				 " 		((expires > now() AND expires <= TIMESTAMP '".$dataHoje." 23:59:59') ".
				 " 		OR expires > TIMESTAMP '".$dataHoje." 23:59:59')) AND status NOT LIKE 'Test' group by emergencia_id) as ul ".
				 " WHERE ".
				 " ca.risco_id = d.risco_id and ".
				 " ca.codar_id = d.codar_id and ".
				 " ca.emergencia_id = ul.emergencia_id and ".
				 " ca.sequencecap = ul.m and ".
				 " ca.msgtype NOT LIKE 'Cancel' ".
				 " ORDER BY ".
				 " ca.risco_id ASC";
		
		
		$res = \Yii::$app->db->createCommand ( $query )->queryAll();
		
		$url = (((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS']!=='off') || $_SERVER['SERVER_PORT']==443) ? 'https://':'http://' ).$_SERVER['HTTP_HOST'];
		
		$aux = array();
		foreach ($res as $item){
			$aux[] = array(
						'address_cap' => $url.Yii::getAlias('@web').DIRECTORY_SEPARATOR.$item['address_cap'],
						'event'=> $item['event'],
						'marcador' => $item['marcador']
					);
		}		
		return $res;
	}
	
	static function getCapsFuturo(){
		
		$dataHoje = date('Y-m-d');
		
		$query = "select ".
				 //" ca.id, ".
				 " ca.address_cap, ".
				 " ca.event, ".
				 " d.marcador ".
				 " from ".
				 " cap1_2 as ca, ".
				 " cap_1_2_description as d, ".
				 " (SELECT emergencia_id, max(sequencecap) m FROM cap1_2  ".
				 " WHERE (onset > TIMESTAMP '".$dataHoje." 23:59:59' OR expires >= TIMESTAMP '".$dataHoje." 23:59:59') AND ".
				 " status NOT LIKE 'Test' group by emergencia_id) as ul ".
				 " WHERE ".
				 " ca.risco_id = d.risco_id and ".
				 " ca.codar_id = d.codar_id and ".
				 " ca.emergencia_id = ul.emergencia_id and ".
				 " ca.sequencecap = ul.m AND ".
				 " ca.msgtype NOT LIKE 'Cancel' ".
				 " ORDER BY ".
				 " ca.risco_id ASC";
		
		$res = \Yii::$app->db->createCommand ( $query )->queryAll();
		
		$url = (((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS']!=='off') || $_SERVER['SERVER_PORT']==443) ? 'https://':'http://' ).$_SERVER['HTTP_HOST'];
		
		$aux = array();
		foreach ($res as $item){
			$aux[] = array(
					'address_cap' => $url.Yii::getAlias('@web').DIRECTORY_SEPARATOR.$item['address_cap'],
					'event'=> $item['event'],
					'marcador' => $item['marcador']
			);
		}
		return $res;
		
		
	}
	
	
}
