<?php
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\ActiveForm;
use app\models\Usuario;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\modules\backend\models\Grupo */

$this->params ['breadcrumbs'] [] = [ 
		'label' => Yii::t ( 'app', 'Grupos' ),
		'url' => [ 
				'index' 
		] 
];
$this->params ['breadcrumbs'] [] = $this->title;
?>
<div class="usuario-view">

	<h1>Associar Usuários</h1>
    <?=DetailView::widget ( [ 'model' => $grupo,'attributes' => [ 'nome','descricao' ] ] );$form = ActiveForm::begin ();?>
    <div>
    
   	<?php
   					
				echo maksyutin\duallistbox\Widget::widget ( [ 
						'model' => $model,
						'attribute' => 'usuarios',
						'title' => 'Associar Usuários',
						'data' => Usuario::find (),
						'data_id' => 'id',
						'data_value' => 'nome',
						'lngOptions' => [ 
								'search_placeholder' => 'Procurar',
								'showing' => ' - Usuários',
								'available' => 'Não contidos',
								'selected' => 'Contidos' 
						] 
				] );
				
				?> 
    
    </div>
	<p>&nbsp;</p>
	<div class="form-group">
        <?= Html::submitButton("Salvar", ['class' => 'btn btn-success'])?>&nbsp;<?= Html::a("Associar Jurisdições", Url::to(['associar-jurisdicoes', 'id'=>$grupo->id]), ['class' => 'btn btn-primary'])?>
    </div>

    <?php ActiveForm::end(); ?>
    

</div>
