<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->registerJsFile('//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js', ['position'=>yii\web\View::POS_HEAD]);
$this->registerJsFile('@web/js/OpenLayers-2.12/OpenLayers.js', ['position'=>yii\web\View::POS_HEAD]);
$this->registerCssFile('@web/js/OpenLayers-2.12/theme/default/style.css', ['position'=>yii\web\View::POS_HEAD]);
$this->registerJsFile('@web/js/ext-3.4.0/adapter/ext/ext-base.js', ['position'=>yii\web\View::POS_END]);
$this->registerJsFile('@web/js/ext-3.4.0/ext-all.js', ['position'=>yii\web\View::POS_END]);
$this->registerCssFile('@web/js/ext-3.4.0/resources/css/ext-all.css', ['position'=>yii\web\View::POS_END]);
$this->registerJsFile('@web/js/GeoExt/lib/GeoExt.js', ['position'=>yii\web\View::POS_END]);
$this->registerJsFile('@web/js/html2canvas.js', ['position'=>yii\web\View::POS_HEAD]);

/* @var $this yii\web\View */
/* @var $model app\modules\backend\models\Jurisdicao */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Jurisdições'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="jurisdicao-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'nome',
            'instituicao.nome',
            
        ],
    ]) ?>
	
</div>
<input type="hidden" id="wkt" value="<?php echo $model->geometria?>">
<div id="map" style="height: 440px"></div>
 
     
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
		
		var styleMap = new OpenLayers.StyleMap({
		    "default": OpenLayers.Feature.Vector.style['default'],
		    "vertex": vertexStyle
		}, {extendDefault: false});

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

		var wkt = new OpenLayers.Format.WKT();
		var features = wkt.read($("#wkt").val());

		<?php $cor = (($model->cor)? $model->cor :'#00ff00');?>
		var style = new OpenLayers.Style({ fillColor: '<?php echo $cor?>', 'strokeWidth': 1,'strokeColor': '#000', fillOpacity:0.6});
		var layer_vector_polygon = new OpenLayers.Layer.Vector("Vector Criando", { styleMap: style });	
		layer_vector_polygon.destroyFeatures();
		if (features) {			
			layer_vector_polygon.addFeatures(features);
			map.addLayer(layer_vector_polygon);
		} else {
			console.log('Bad WKT');
		}

		
			
	});
</script>

