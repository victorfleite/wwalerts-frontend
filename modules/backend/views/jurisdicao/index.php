<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\backend\models\JurisdicaoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->registerJsFile('//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js', ['position'=>yii\web\View::POS_HEAD]);
$this->registerJsFile('@web/js/OpenLayers-2.12/OpenLayers.js', ['position'=>yii\web\View::POS_HEAD]);
$this->registerCssFile('@web/js/OpenLayers-2.12/theme/default/style.css', ['position'=>yii\web\View::POS_HEAD]);
$this->registerJsFile('@web/js/ext-3.4.0/adapter/ext/ext-base.js', ['position'=>yii\web\View::POS_END]);
$this->registerJsFile('@web/js/ext-3.4.0/ext-all.js', ['position'=>yii\web\View::POS_END]);
$this->registerCssFile('@web/js/ext-3.4.0/resources/css/ext-all.css', ['position'=>yii\web\View::POS_END]);
$this->registerJsFile('@web/js/GeoExt/lib/GeoExt.js', ['position'=>yii\web\View::POS_END]);
$this->registerJsFile('@web/js/html2canvas.js', ['position'=>yii\web\View::POS_HEAD]);

$this->title = Yii::t('app', 'Jurisdições');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="jurisdicao-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Nova Jurisdição'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    
	<div id="map" style="height: 440px"></div>
    
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
			'instituicao.nome',
            'nome',
			[
				'attribute'=>'cor',
				'format' => 'raw',
				'value'=>function($data){
								return "<div style='background-color:".$data->cor."'>&nbsp;</div>";
				},
			],
            ['class' => 'yii\grid\ActionColumn']
		]]); 
	?>
</div>

<script type="text/javascript">
	$(document).ready(function() {

			
	
				
		var vertexStyle = {
		    strokeColor: "#FF0000",
		    fillColor: "#FF0000",
		    strokeOpacity: 1,
		    strokeWidth: 2,
		    pointRadius: 6,
		    graphicName: "circle"
		};

		var virtual = {
		    strokeColor: "#EE9900",
		    fillColor: "#EE9900",
		    strokeOpacity: 1,
		    strokeWidth: 2,
		    pointRadius: 4,
		    graphicName: "circle"
		};
		

		var fromProjection = new OpenLayers.Projection("EPSG:4326");
		var toProjection = new OpenLayers.Projection("EPSG:900913");

		var worldAlertas = new OpenLayers.Bounds(-73.839434, -33.770856,-34.858104, 5.38289).transform(fromProjection, toProjection);

		var toolbar;
		var panel_controls;
		var controle_vector = true;
		

		var map = new OpenLayers.Map({
			div: "map",
			projection: toProjection,
			displayProjection: fromProjection,			
			layers: [ new OpenLayers.Layer.OSM( "OSM", "", {displayInLayerSwitcher: false} ) ]
		});

		layer_vector = new OpenLayers.Layer.Vector({
			layers : "Poligonos"
		}, {
			displayInLayerSwitcher : false
		});
		layer_vector.displayInLayerSwitcher=false;
		
      
		map.addLayer(layer_vector);
		map.setCenter(new OpenLayers.LonLat(-54.404297,-14.774883).transform(fromProjection, toProjection), 4);

		<?php foreach($jurisdicoes as $jurisdicao){?>
			<?php $cor = (($jurisdicao->cor)? $jurisdicao->cor :'#00ff00');?>
			var style = new OpenLayers.Style({ fillColor: '<?php echo $cor?>', 'strokeWidth': 1,'strokeColor': '#000', fillOpacity:0.6});
			var layer_vector_polygon = new OpenLayers.Layer.Vector("Vector Criando", { styleMap: style });	
			layer_vector_polygon.displayInLayerSwitcher=false;
			var wkt = new OpenLayers.Format.WKT();
			var features = wkt.read("<?php echo $jurisdicao->geometria?>");
			if (features) {			
				layer_vector_polygon.addFeatures(features);
				map.addLayer(layer_vector_polygon);
			} else {
				console.log('Bad WKT');
			}

		<?php }?>

		
			
	});
</script>
