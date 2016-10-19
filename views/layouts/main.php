<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use mdm\admin\components\MenuHelper;

AppAsset::register ( $this );
?>
<?php $this->beginPage()?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
<meta charset="<?= Yii::$app->charset ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags()?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head()?>
</head>
<body>
<?php $this->beginBody()?>

<div class="wrap">
    <?php
				NavBar::begin ( [ 
						'brandLabel' => 'Alert-As',
						'brandUrl' => Yii::$app->homeUrl,
						'options' => [ 
								'class' => 'navbar-inverse navbar-fixed-top' 
						] 
				] );
				$items = [ ];
				if (! Yii::$app->user->isGuest) {
					
					$items = [ 
							[ 
									'label' => 'Home',
									'url' => [ 
											'/site/index' 
									],
									'linkOptions' => [ ] 
							],
							[ 
									'label' => 'Aviso',
									'items' => [ 
											[ 
													'label' => 'Avisos de Eventos Meteorológicos Severos',
													'url' => Url::to ( [ 
															'/backend/emergencia/index' 
													] ) 
											],
											'<li class="divider"></li>',
											'<li class="dropdown-header">Dropdown Header</li>',
											[ 
													'label' => 'Level 1 - Dropdown B',
													'url' => '#' 
											] 
									] 
							],
							[ 
									'label' => 'Login',
									'url' => [ 
											'site/login' 
									],
									'visible' => Yii::$app->user->isGuest 
							] 
					];
					$items = array_merge ($items, MenuHelper::getAssignedMenu(Yii::$app->user->id));
				}
				
				// \yii::$app->dumper->show(Yii::$app->user->id, true);
				$items [] = Yii::$app->user->isGuest ? [ 
						'label' => 'Login',
						'url' => [ 
								'/site/login' 
						] 
				] : [ 
						'label' => 'Logout (' . Yii::$app->user->identity->nome . ')',
						'url' => [ 
								'/site/logout' 
						],
						'linkOptions' => [ 
								'data-method' => 'post' 
						] 
				];
				
				echo Nav::widget ( [ 
						'options' => [ 
								'class' => 'navbar-nav navbar-right' 
						],
						'items' => $items 
				] );
				NavBar::end ();
				?>

    <div class="container">
        <?=Breadcrumbs::widget ( [ 'links' => isset ( $this->params ['breadcrumbs'] ) ? $this->params ['breadcrumbs'] : [ ] ] )?>
        <?= $content?>
    </div>
	</div>

	<footer class="footer">
		<div class="container">
			<p class="pull-left">&copy; Instituto Nacional de Meteorologia - INMET <?= date('Y') ?></p>

			<p class="pull-right"><?//= Yii::powered() ?></p>
		</div>
	</footer>


<?php $this->endBody()?>
</body>
</html>
<?php $this->endPage()?>
