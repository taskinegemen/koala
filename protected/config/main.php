<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.

$db_config=array(
			"piranha"=>array(
                        		'connectionString' => 'mysql:host=pufferfish;dbname=koala',
                        		'emulatePrepare' => true,
                        		'username' => 'piranha',
                        		'password' => 'rS59UMbUmyM7PawN',
                        		'charset' => 'utf8',
                			),
			"lindneo"=>array(
                        		'connectionString' => 'mysql:host=lindneo.com;dbname=koala',
                        		'emulatePrepare' => true,
                        		'username' => 'db_koala',
                        		'password' => 'sTYpCXQ7vpTPc2xe',
                        		'charset' => 'utf8',
                			),
			"ulgen"=>array(
                        		'connectionString' => 'mysql:host=datamaster.private.services.lindneo.com;port=3306;dbname=koala',
                        		'emulatePrepare' => true,
                        		'username' => 'db_koala',
                        		'password' => 'sTYpCXQ7vpTPc2xe',
                        		'charset' => 'utf8',
                			)
		
		);

return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'My Web Application',

	// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>array(
		'application.utilities.*',
		'application.models.*',
		'application.components.*',
	),

	'modules'=>array(
		// uncomment the following to enable the Gii tool
		
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'kl14@LnDnkl',
			// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('127.0.0.1','::1','*.*.*.*'),
		),
		
	),

	// application components
	'components'=>array(
		'user'=>array(
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
		),
		// uncomment the following to enable URLs in path-format
		
		'urlManager'=>array(
			'urlFormat'=>'path',
			'showScriptName'=>false,
			'rules'=>array(
				array('KerberizedService/authenticate','pattern'=>'kerberizedservice/authenticate/','verb'=>'POST'),
				// array('api/addUserBook', 'pattern'=>'api/addUserBook/<user_id:\d+>/<type:\w+>/<type_id:\w+>'),
				// array('api/addUserNote', 'pattern'=>'api/addUserNote/<user_id:\d+>/<book_id:\w+>/<page_id:\w+>/<note:\w+>'),
				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
			),
		),
		/*
		'db'=>array(
			'connectionString' => 'sqlite:'.dirname(__FILE__).'/../data/testdrive.db',
		),*/
		// uncomment the following to use a MySQL database
		
		'db'=>$db_config[gethostname()],
		
		'errorHandler'=>array(
			// use 'site/error' action to display errors
			'errorAction'=>'site/error',
		),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
				),
				// uncomment the following to show log messages on web pages
				/*
				array(
					'class'=>'CWebLogRoute',
				),
				*/
			),
		),
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
		'adminEmail'=>'webmaster@example.com',
	),
);
