<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class SiteController extends Controller
{	
	
	public function actions()
	{
		return [
		'error' => [
		'class' => 'yii\web\ErrorAction',
		],
		];
	}
	
	public function actionIndex(){
		return $this->render ( 'index' );
	}	
    
    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        
        return Yii::$app->getResponse()->redirect(['site/login']);
    }

  
}
