<?php
namespace app\components;
/**
 * Classe Helper para manipulação de String (Concatenar)
 * @author root
 *
 */
class Writer{
	
	const DELIMITER  = "\n";
	public $word;
	
	public function __construct($word = ""){
		$this->word = $word;
	}
	
	public function writeln($str){
		$this->word .= $str . self::DELIMITER;
		return $this;
	}
	public function write($str){
		$this->word .= $str;
		return $this;
	}
	public function replace($key, $value){		
		$this->word = str_replace($key, $value, $this->word);
		return $this;
	}	
	
	public function getString(){
		return $this->word;
	}
	
	
	
	
}