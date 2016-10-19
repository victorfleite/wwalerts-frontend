<?php

namespace app\modules\backend\models;

use Yii;
use app\components\Writer;
class RssCap12 {
	
	const LINK = "http://alert-as.inmet.gov.br";

	public $title;
	public $link;
	public $description;
	public $language;
	public $itensArray;

	public function init(){
		$this->itensArray = array();
	}
	/**
	 * Set Channel Title
	 * @param unknown $title
	 */
	public function setChannelTitle($title){
		$this->title = $title;
	}
	/**
	 * Set Channel Link
	 * @param unknown $link
	 */
	public function setChannelLink($link){
		$this->link = $link;
	}
	/**
	 * Set Channer Description
	 * @param unknown $description
	 */
	public function setChannelDescription($description){
		$this->description = $description;
	}
	/**
	 * Set Channel Language
	 * @param unknown $language
	 */
	public function setChannelLanguage($language){
		$this->language = $language;
	}
	/**
	 * Adicionar Item no RSS
	 * @param unknown $title
	 * @param unknown $link
	 * @param unknown $description
	 * @param unknown $sent
	 */
	public function addItem($title, $link, $description, $sent){

		$pathern = "D, d M Y H:i:s";
		$sentDate = \DateTime::createFromFormat('Y-m-d H:i:s', $sent);

		$writer = new Writer;
		$writer->writeln("<item>");
		$writer->writeln("	<title>".$title."</title>");
		$writer->writeln("	<link>".$link."</link>");
		$writer->writeln("	<description>".$description."</description>");
		$writer->writeln("	<pubDate>".$sentDate->format($pathern)." -0300</pubDate>");
		$writer->writeln("	<guid>".$link."</guid>");
		$writer->writeln("</item>");

		$this->itensArray[] = $writer->getString();
	}
	/**
	 * Recupera XML do RSS
	 * @return Ambigous <string, unknown>
	 */
	public function getRss(){

		$pathern = "D, d M Y H:i:s";
		$dataAtual = date($pathern);
		
		$writer = new Writer();
		$writer->writeln("<?xml version=\"1.0\" encoding=\"UTF-8\" ?>");
		$writer->writeln("<rss version=\"2.0\">");
		$writer->writeln("");
		$writer->writeln("<channel>");
		$writer->writeln(" <title>".$this->title."</title>");
		$writer->writeln(" <link>".$this->link."</link>");
		$writer->writeln(" <description>".$this->description."</description>");
		$writer->writeln(" <language>pt-BR</language>");
		$writer->writeln(" <pubDate>".$dataAtual." -0300</pubDate>");

		foreach($this->itensArray as $item){				
			$writer->writeln($item);				
		}
		
		$writer->writeln("</channel>");
		$writer->writeln("</rss>");

		return $writer->getString();
	}
	/**
	 * Salvar RSS em disco
	 * @return string
	 */
	public function saveRss(){

		$arquivo = "/var/www/html/cap_12/rss/alert-as.rss";
		$fh = fopen($arquivo, 'w') or die("can't open file");
		fwrite($fh, $this->getRss());
		fclose($fh);
	
		return $arquivo;

	}
	/**
	 * Atualizar RSS de Caps
	 */
	public function atualizarRss(){

		$this->setChannelTitle("Alert-AS - Avisos");
		$this->setChannelLink(self::LINK);
		$this->setChannelDescription("Avisos atuais na América do Sul");
		$this->setChannelLanguage("pt");

		//Consultar ultimos 48 horas de cap_12
		$query = "SELECT ".
					" id, headline, address_cap, msgType, to_char(sent, 'YYYY-MM-DD HH12:MI:SS') as sent, severity, onset, expires, description, areadesc ".		
					" FROM cap1_2 ".
					" WHERE to_char(expires, 'YYYYMMDD') >= to_char((now() - interval '2 day'), 'YYYYMMDD') AND ".
					" status NOT LIKE 'Test' ORDER BY expires DESC,emergencia_id desc, sequencecap desc;";
		$res = Yii::$app->db->createCommand ( $query )->queryAll ();
		
		foreach($res as $item){
			$title = $item["headline"];
			$link = self::LINK.DIRECTORY_SEPARATOR.$item["address_cap"];
			$htmlDescription = '<![CDATA[<table border="0" cellspacing="0" cellpadding="3"><tr><th align="left">Status</th><td>'.$item["msgType"].'</td></tr><tr><th align="left">Evento</th><td>'.$item["event"].'</td></tr><tr><th align="left">Severidade</th><td>'.$item["severity"].'</td></tr><tr><th align="left">Início</th><td>'.$item["onset"].'</td></tr><tr><th align="left">Fim</th><td>'.$item["expires"].'</td></tr><tr><th align="left">Descrição</th><td>'.$item["description"].'</td></tr><tr><th align="left">Área</th><td>'.$item["areadesc"].'</td></tr><tr><th align="left">Link Gráfico</th><td><a href="'+"http://alert-as.inmet.gov.br/cv/emergencia/cap/".$item["id"].'">'."http://alert-as.inmet.gov.br/cv/emergencia/cap/".$item["id"].'</a></td></tr></table>]]>'; 
			$this->addItem($title, $link, $htmlDescription, $item["sent"]);
		}
		return $this->saveRss();
	}

}