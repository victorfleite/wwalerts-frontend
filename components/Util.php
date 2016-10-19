<?php

namespace app\components;

/**
 * Classe Helper para manipulação de String (Concatenar)
 * 
 * @author root
 *        
 */
class Util {
	const LINGUA_PORTUGUES = 'pt_BR';
	const LINGUA_INGLES = 'en_US';
	const LINGUA_ESPANHOL = 'es';
	const LINGUA_FRANCES = 'fr';
	static function gerarToken() {
		return sprintf ( '%08x%08x%08x%08x', mt_rand (), mt_rand (), mt_rand (), mt_rand () ) . date ( 'YmdHi' );
	}
	static function contemString($findme, $mystring) {
		$pos = strpos ( $mystring, $findme );
		if ($pos === false) {
			return false;
		} else {
			return true;
		}
	}
	static function getDataPorExtenso($data = null) {
		if (! $data) {
			$data = new \DateTime ();
		}
		return $data->format ( 'l, d' ) . " de " . $data->format ( 'F' ) . " de " . $data->format ( 'Y' );
	}
	static function convertHexToRgb($hex, $alfa = null) {
		list ( $r, $g, $b ) = sscanf ( $hex, "#%02x%02x%02x" );
		$str = '';
		if ($alfa) {
			$str = "rgba($r, $g, $b, $alfa)";
		} else {
			$str = "rgba($r, $g, $b)";
		}
		return $str;
	}
}