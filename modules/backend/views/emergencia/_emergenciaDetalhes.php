<?php 

use yii\widgets\DetailView;

?>


<?= DetailView::widget([
		'model' => $model,
		'attributes' => [
		[
		'label'=>\Yii::t('app', 'emergencia.show.label.codar', 'Codar'),
		'value'=>\Yii::t('app', $model->codar->i18n, $model->codar->descricao)
		],
		[
		'label'=>\Yii::t('app', 'emergencia.show.label.risco', 'Risco'),
		'value'=>\Yii::t('app', $model->risco->i18n, $model->risco->descricao)
		],
		[
		'label'=>\Yii::t('app', 'emergencia.show.label.protocolo.status', 'Status'),
		'format'=>['html'],
		'value'=>"<div style='background-color:".$model->risco->rgb."'>&nbsp;</div>"
		],
		[
		'attribute' => 'data_inicial',
		'label'=>\Yii::t('app', 'emergencia.show.label.data', 'Data Inicial'),
		],
		[
		'attribute' => 'duracao_estimada',
		'label'=>\Yii::t('app', 'emergencia.show.label.duracao', 'Data Estimada'),
		],
		[
		'attribute' => 'owner.instituicao.sigla',
		'label'=>\Yii::t('app', 'emergencia.show.label.instituicao', 'Instituição'),
		],

		/*[
		 'label'=>\Yii::t('app', 'mapa.emergencia.encerrado', 'Encerrado'),
'value'=>(($model->encerrado == Emergencia::ENCERRADO_SIM)? \Yii::t('app', 'mapa.emergencia.encerrado.sim', 'Sim'):\Yii::t('app', 'mapa.emergencia.encerrado.nao', 'Não'))
],*/
		[
		'attribute' => 'owner.nome',
		'label'=>\Yii::t('app', 'emergencia.show.label.autor', 'Responsável'),
		],
		],
    ]);?>

    
    
    