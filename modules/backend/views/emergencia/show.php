<?php

use yii\helpers\Html;
use app\modules\backend\models\Emergencia;
use yii\widgets\DetailView;
use yii\grid\GridView;
use webulla\extensions\geometry\WellKnownText;

$this->registerJsFile('//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js', ['position'=>yii\web\View::POS_HEAD]);
$this->registerJsFile('@web/js/OpenLayers-2.12/OpenLayers.js', ['position'=>yii\web\View::POS_HEAD]);
$this->registerCssFile('@web/js/OpenLayers-2.12/theme/default/style.css', ['position'=>yii\web\View::POS_HEAD]);

/* @var $this yii\web\View */
/* @var $model app\models\Emergencia */

$this->title = \Yii::t('app', 'emergencia.show.titulo');;
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app', 'emergencia.show.avisos'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="emergencia-view">

    <h1><?= Html::encode($this->title) ?></h1>
	<?php if(!$model->encerrado){?>
    <p>
        <?= Html::a(\Yii::t('app', 'emergencia.show.botao.estender.aviso', 'Estender Aviso'), ['update', 'id' => $model->id], ['class' => 'btn btn-warning']) ?>   
        <?= Html::a(\Yii::t('app', 'emergencia.show.botao.finalizar.aviso', 'Finalizar Aviso'), ['cancel', 'id' => $model->id], ['class' => 'btn btn-danger']) ?>      
    </p>
    <?php }?>

    <?= $this->render('_emergenciaDetalhes', [
        'model' => $model,
    ]) ?>
    
    
       <?= GridView::widget([
        'dataProvider' => $dataProviderEmergenciasLog,
        'columns' => [
			[
				'attribute' => 'data',
				'label'=>\Yii::t('app', 'emergencia_log.data', 'Data'),
				'format' => ['date', 'php:d/m/Y H:i']
    		],
			[
				'label'=>\Yii::t('app', 'emergencia_log.descricao', 'Descrição'),
				'value'=>function($data){
					return $data->descricao;
				}
			],
			'responsavel.nome'
    ]]); ?>
    <div id="wkt" value="<?= $model->location; ?>" data-color="<?= $model->risco->rgb?>"></div>
    <div id="map" style="height: 500px;">
</div>
<?php 

$this->registerJsFile('@web/js/ext-3.4.0/adapter/ext/ext-base.js', ['position'=>yii\web\View::POS_END]);
$this->registerJsFile('@web/js/ext-3.4.0/ext-all.js', ['position'=>yii\web\View::POS_END]);
$this->registerCssFile('@web/js/ext-3.4.0/resources/css/ext-all.css', ['position'=>yii\web\View::POS_END]);
$this->registerJsFile('@web/js/GeoExt/lib/GeoExt.js', ['position'=>yii\web\View::POS_END]);
$this->registerJsFile('@web/js/mapas.js', ['position'=>yii\web\View::POS_END]);

?>

<script type="text/javascript">
$(document).ready(function() {
	var fromProjection = new OpenLayers.Projection("EPSG:4326");
	var toProjection = new OpenLayers.Projection("EPSG:900913");

	var worldAlertas = new OpenLayers.Bounds(-73.839434, -33.770856,-34.858104, 5.38289).transform(fromProjection, toProjection);

	var map = new OpenLayers.Map({
		div: "map",
		projection: toProjection,
		displayProjection: fromProjection,			
		layers: [ new OpenLayers.Layer.OSM( "OSM", "", {displayInLayerSwitcher: false} ) ]
	});

	layer_vector= new OpenLayers.Layer.Vector('Aviso', { displayInLayerSwitcher: false});
	map.addLayer(layer_vector);	

	map.setCenter(new OpenLayers.LonLat(-54.404297,-14.774883).transform(fromProjection, toProjection), 4);
	var wkt = new OpenLayers.Format.WKT();
	var features = wkt.read($("#wkt").attr("value"));
	if (features) {
		if ($("#wkt").data("color")) {
			features.style = {
				fillOpacity : 0.7,
				strokeColor : $("#wkt").data("color"),
				fillColor : $("#wkt").data("color")
			}
			// features.style = { fillOpacity: 0.4, strokeColor: '0000FF',
			// fillColor: '0000FF' }
		}
		layer_vector.addFeatures(features);
	} else {
		console.log('Bad WKT');
	}
});
</script>    
    
