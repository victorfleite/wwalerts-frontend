<?php
$params = require (__DIR__ . '/params.php');

$config = [ 
		'id' => 'basic',
		'basePath' => dirname ( __DIR__ ),
		'bootstrap' => [ 
				'log' 
		],
		'language' => 'pt-BR',
		'sourceLanguage' => 'en-US',
		'modules' => [
			'admin' => [
				'class' => 'mdm\admin\Module',
				'layout' => 'left-menu',
				'mainLayout' => '@app/views/layouts/main.php',
				'controllerMap' => [
					'assignment' => [
								'class' => 'mdm\admin\controllers\AssignmentController',
								'userClassName' => 'app\models\Usuario',
								'idField' => 'id',
								'usernameField' => 'nome',
								//'searchClass' => 'app\models\SearchUsuario'
							],
				],
			],
			'gridview' => [
				'class' => '\kartik\grid\Module',
						// enter optional module parameters below - only if you need to
				// use your own export download action or custom translation
				// message source
				'downloadAction' => 'gridview/export/download',
				// 'i18n' => []
			],
			'frontend' => [
				'class' => 'app\modules\frontend\Module',				
			],
			'backend' => [
				'class' => 'app\modules\backend\Module',
			],
			
		],
		'components' => [ 
				'authManager' => [
					'class' =>'yii\rbac\DbManager' 
				],
				'dumper' => [
						'class'=>'app\components\Dumper'
				],
				'request' => [ 
						// !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
						'cookieValidationKey' => '-2Z5hZ3p9UmLHdHLN6XEdIL82LEbOVTJ' 
				],
				'cache' => [ 
						'class' => 'yii\caching\FileCache' 
				],
				'user' => [ 
						'identityClass' => 'app\models\Usuario',
						'enableAutoLogin' => true 
				],
				'errorHandler' => [ 
						'errorAction' => 'site/error' 
				],
				'mailer' => [ 
						'class' => 'yii\swiftmailer\Mailer',
						// send all mails to a file by default. You have to set
						// 'useFileTransport' to false and configure a transport
						// for the mailer to send real emails.
						'useFileTransport' => true 
				],
				'log' => [ 
						'traceLevel' => YII_DEBUG ? 3 : 0,
						'targets' => [ 
								[ 
										'class' => 'yii\log\FileTarget',
										'levels' => [ 
												'error',
												'warning' 
										] 
								] 
						] 
				],
				'db' => require (__DIR__ . '/db.php'),	
				'mailer' => [
				         'class' => 'yii\swiftmailer\Mailer',
				         'transport' => [
				             'class' => 'Swift_SmtpTransport',
				             'host' => 'aramis.inmet.gov.br',
				             'username' => '',
				             'password' => ''
				         ],
				],
				
				/*'formatter' => [
				    'class' => 'yii\i18n\Formatter',
				    'dateFormat' => 'php:d/M/Y',
				    'datetimeFormat' => 'php:d/M/Y H:i:s',
				    'timeFormat' => 'php:H:i',
				]*/
		        /*'urlManager' => [
		            'enablePrettyUrl' => true,
		            'showScriptName' => false,
		            'enableStrictParsing' => false,
		            'rules' => [
		                // ...
		            ],
		        ],*/
    ],
    'params' => $params,
    'as access' => [
	    'class' => 'mdm\admin\components\AccessControl',
		    'allowActions' => [
			    'site/*',
			    'admin/*',
			    'frontend/front/hoje',
			   	'frontend/front/amanha',
			   	'frontend/front/futuro',
			   	'frontend/front/emergencia',
			   	'frontend/front/mapcenterconfig',
			   	'frontend/front/wmsconfig',
			   	'frontend/front/codar'
			    //'some-controller/some-action',
			    // The actions listed here will be allowed to everyone including guests.
			    // So, 'admin/*' should not appear here in the production, of course.
			    // But in the earlier stages of your development, you may probably want to
			    // add a lot of actions here until you finally completed setting up rbac,
			    // otherwise you may not even take a first step.
	    ]
    ],
];

if (YII_ENV_DEV) {
	// configuration adjustments for 'dev' environment
	$config ['bootstrap'] [] = 'debug';
	$config ['modules'] ['debug'] = [ 
			'class' => 'yii\debug\Module' 
	];
	
	$config ['bootstrap'] [] = 'gii';
	$config ['modules'] ['gii'] = [ 
			'class' => 'yii\gii\Module', 
			'allowedIPs' => ['127.0.0.1', '::1', '*'] // adjust this to your needs
	];
}

return $config;
