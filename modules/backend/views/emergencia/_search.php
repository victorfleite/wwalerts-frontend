<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\modules\backend\models\Emergencia;
use app\modules\backend\models\EmergenciaSearch;
use kartik\date\DatePicker;
use app\modules\backend\models\Risco;
use app\modules\backend\models\Codar;

?>
<style>
	.emergencia-search{
		background-color:#EBEBE0;
		-webkit-border-radius: 5px;
		-moz-border-radius: 5px;
		border-radius: 5px;	
		padding: 10px;
		margin-bottom: 10px;
	}
	.emergencia-search > div:first-child { 
		display:block;
		text-align: center;
		margin-bottom: 20px;
	}
	.emergencia-search #group-buttons{
		display:block;
		text-align: center;
		margin-top: 30px;
	}


</style>


<div class="emergencia-search">

	<div id="#cabecalho"><h2>Pesquisa Avançada</h2></div>

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    	'layout' => 'horizontal'  		
    ]); ?>
    
    
    <?= $form->errorSummary($model); ?>
    
    <div class="row">
  		<div class="col-md-6">
  				
  			
  		
  			<?= $form->field($model, 'tipo')->radioList([EmergenciaSearch::TIPO_TODOS => 'Todos', EmergenciaSearch::TIPO_ATIVOS => 'Ativos'], [
    					/*'onclick'=>"$('.emergencia-search form').submit()"*/]); ?>
			<?= $form->field($model, 'id')->textInput(); ?>
    		<?= $form->field($model, 'risco_id')->dropDownList(\yii\helpers\ArrayHelper::map(Risco::find()->orderBy('descricao')->asArray()->all(), 'id', 'descricao'), ['prompt'=>'']) ?>

    		<?= $form->field($model, 'codar_id')->dropDownList(\yii\helpers\ArrayHelper::map(Codar::find()->orderBy('descricao')->asArray()->all(), 'id', 'descricao'), ['prompt'=>'']) ?>
    	</div>
  		<div class="col-md-6">
  			<div class="form-group">
     		<label class="control-label col-sm-3">Data Inicial</label>
        	<div class="col-sm-6">
             	<?= DatePicker::widget([
							'model' => $model,
							'attribute' => 'data_inicial',
							'type' => DatePicker::TYPE_COMPONENT_APPEND,
							'options' => ['readOnly'=>true],
							'pluginOptions' => [
								'autoclose' => true,
								'format' => 'dd/mm/yyyy',
								'todayHighlight' => true,
								'todayBtn'=>true,
								//'minuteStep'=>1
								
						]
				]);?>
        	</div>
    		</div>
    
		     <div class="form-group">
		     	<label class="control-label col-sm-3">Duração Estimada</label>
		     	<div class="col-sm-6">
		    		<?= DatePicker::widget([
		    				'model' => $model,
							'attribute' => 'duracao_estimada',
							'type' => DatePicker::TYPE_COMPONENT_APPEND,
							'options' => ['readOnly'=>true],
							'pluginOptions' => [
								'autoclose' => true,
								'format' => 'dd/mm/yyyy',
								'todayHighlight' => true,
								'todayBtn'=>true,
								//'minuteStep'=>1
								
						]
				]);?>
		     	</div>
		    </div>		
  		
  		
  		
  		</div>
      
  
    </div>
     
    
    
    <div class="form-group" id="group-buttons">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
