<?php

/** PRODUCAO **/
/*
return [
    'class' => 'yii\db\Connection',
 	'dsn' => 'pgsql:host=ximej;port=5432;dbname=centrovirtual',
	'username' => 'desenv',
    'password' => 'desenvolve',
    'charset' => 'utf8',
];*/
/** DESENVOLVIMENTO **/

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'pgsql:host=192.168.242.31;port=5432;dbname=centrovirtual',
	'username' => 'desenv',
    'password' => 'desenvolve',
    'charset' => 'utf8',
];