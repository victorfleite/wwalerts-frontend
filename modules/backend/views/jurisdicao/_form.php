<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\color\ColorInput;

use app\modules\backend\models\Instituicao;

/* @var $this yii\web\View */
/* @var $model app\modules\backend\models\Jurisdicao */
/* @var $form yii\widgets\ActiveForm */

$this->registerJsFile('//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js', ['position'=>yii\web\View::POS_HEAD]);
$this->registerJsFile('@web/js/OpenLayers-2.12/OpenLayers.js', ['position'=>yii\web\View::POS_HEAD]);
$this->registerCssFile('@web/js/OpenLayers-2.12/theme/default/style.css', ['position'=>yii\web\View::POS_HEAD]);
$this->registerJsFile('@web/js/ext-3.4.0/adapter/ext/ext-base.js', ['position'=>yii\web\View::POS_END]);
$this->registerJsFile('@web/js/ext-3.4.0/ext-all.js', ['position'=>yii\web\View::POS_END]);
$this->registerCssFile('@web/js/ext-3.4.0/resources/css/ext-all.css', ['position'=>yii\web\View::POS_END]);
$this->registerJsFile('@web/js/GeoExt/lib/GeoExt.js', ['position'=>yii\web\View::POS_END]);
$this->registerJsFile('@web/js/html2canvas.js', ['position'=>yii\web\View::POS_HEAD]);

?>

<div class="jurisdicao-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'nome')->textInput() ?>    
	<?php      
		$instituicoes = ArrayHelper::map(Instituicao::find()->orderBy('nome')->all(), 'id', 'nome');
		echo $form->field($model, 'instituicao_id')->dropdownList($instituicoes,['prompt'=>'']);
	?>
	
	<?php 
		
		// Usage with ActiveForm and model
		echo $form->field($model, 'cor')->widget(ColorInput::classname(), [
			'options' => ['placeholder' => 'Selecione a Cor'],
		]);
	
	
	?>

    <?= $form->field($model, 'geometria')->textArea(['rows' => '6', 'id'=>'wkt']) ?>

    <div class="form-group">
        <?= Html::Button("Preview", ['class' =>'btn btn-warning', 'id'=>'btnPreview']) ?>&nbsp;<?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>


 <div id="map" style="height: 440px"></div>
 
     
<script type="text/javascript">
	$(document).ready(function() {

		var layer_vector_polygon = new OpenLayers.Layer.Vector("Vector Criando",{styleMap: styleMap});	
		
		$('#btnPreview').click(function(){

			var styleMap = new OpenLayers.StyleMap({
			    "default": OpenLayers.Feature.Vector.style['default'],
			    "vertex": vertexStyle
			}, {extendDefault: false});

				
			var wkt = new OpenLayers.Format.WKT();
			var features = wkt.read($("#wkt").val());

			if (features) {			
				
				
				layer_vector_polygon.destroyFeatures();
				layer_vector_polygon.displayInLayerSwitcher=false;			
				layer_vector_polygon.addFeatures(features);
				map.addLayer(layer_vector_polygon);
				//console.log('WKT adicionado com sucesso!');
			} else {
				//console.log('Bad WKT');
			}
			
		});
		
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
		layer_vector_polygon.displayInLayerSwitcher=false;
      
		map.addLayer(layer_vector);
		map.addLayer(layer_vector_polygon);
		map.setCenter(new OpenLayers.LonLat(-54.404297,-14.774883).transform(fromProjection, toProjection), 4);
		
		<?php if(!empty($model->geometria)){?>
		var wkt = new OpenLayers.Format.WKT();
		var features = wkt.read($("#wkt").val());
		layer_vector_polygon.destroyFeatures();
		if (features) {			
			layer_vector_polygon.addFeatures(features);
			map.addLayer(layer_vector_polygon);
		} else {
			console.log('Bad WKT');
		}
		<?php } ?>	

		
			
	});
</script>
