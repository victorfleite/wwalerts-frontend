<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\jui\DatePicker;
use kartik\select2\Select2;
use app\modules\backend\models\Risco;
use app\modules\backend\models\Codar;
use kartik\datetime\DateTimePicker;

/* @var $this yii\web\View */
/* @var $model app\models\Emergencia */
/* @var $form yii\widgets\ActiveForm */

$this->registerJsFile('//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js', ['position'=>yii\web\View::POS_HEAD]);
$this->registerJsFile('@web/js/OpenLayers-2.12/OpenLayers.js', ['position'=>yii\web\View::POS_HEAD]);
$this->registerCssFile('@web/js/OpenLayers-2.12/theme/default/style.css', ['position'=>yii\web\View::POS_HEAD]);
$this->registerJsFile('@web/js/ext-3.4.0/adapter/ext/ext-base.js', ['position'=>yii\web\View::POS_END]);
$this->registerJsFile('@web/js/ext-3.4.0/ext-all.js', ['position'=>yii\web\View::POS_END]);
$this->registerCssFile('@web/js/ext-3.4.0/resources/css/ext-all.css', ['position'=>yii\web\View::POS_END]);
$this->registerJsFile('@web/js/GeoExt/lib/GeoExt.js', ['position'=>yii\web\View::POS_END]);
$this->registerJsFile('@web/js/mapas.js', ['position'=>yii\web\View::POS_END]);
$this->registerJsFile('@web/js/html2canvas.js', ['position'=>yii\web\View::POS_HEAD]);

?>

<div class="emergencia-form">

<?php $form = ActiveForm::begin(['options'=>['class'=>'form-horizontal']]); ?> 
    
    <?= $form->errorSummary($model); ?>
    
    
    
    
     <div class="row">
  			<div class="col-md-3">
  		
				<?php      
			    $riscos = ArrayHelper::map(Risco::find()->orderBy('descricao')->all(), 'id', 'descricao');
			    echo $form->field($model, 'risco_id')->dropdownList($riscos,['prompt'=>'']);
				?>
				
				<?php      
			    $codares = ArrayHelper::map(Codar::find()->orderBy('descricao')->all(), 'id', 'descricao');
			    echo $form->field($model, 'codar_id')->dropdownList($codares,['prompt'=>'']);
				?>
			  
  		</div>
  		<div class="col-md-2">
  		</div>
  		<div class="col-md-3">
  			  			
  			<div class="form-group">
		    <label class="control-label">Data Inicial</label><br>
		    <?= DateTimePicker::widget([
		    				'model' => $model,
							'attribute' => 'data_inicial',
		    				'type' => DateTimePicker::TYPE_COMPONENT_APPEND,
							'options' => ['readOnly'=>true],
							'pluginOptions' => [
								'autoclose' => true,
								'format' => 'dd/mm/yyyy hh:ii',
								'todayHighlight' => true,
								'todayBtn'=>true,
								'minuteStep'=>1
						]
			]);?>
		    </div>
		    
		    <div class="form-group">
		    <label class="control-label">Duração Estimada</label><br>
		    <?= DateTimePicker::widget([
		    				'model' => $model,
							'attribute' => 'duracao_estimada',
		    				'type' => DateTimePicker::TYPE_COMPONENT_APPEND,
							'options' => ['readOnly'=>true],
							'pluginOptions' => [
								'autoclose' => true,
								'format' => 'dd/mm/yyyy hh:ii',
								'todayHighlight' => true,
								'todayBtn'=>true,
								'minuteStep'=>1
								
						]
			]);?>
		    </div>
		   
  			
  			
  		</div>
      
  
    </div>
    <?php if(!$model->isNewRecord) {?>
       <div class="row">
  			<div class="col-md-12">
	  			<div class="form-group">
			    <?= $form->field($model, 'descricao')->textArea(['rows' => '6']) ?>
			    </div>
  			</div>
  		</div>	
	<?php } ?>	     
    
    <?= Html::activeHiddenInput($model, 'location', ['id'=>'wkt'])?>
    

    <div class="form-group">
        <?= Html::submitButton(($model->isNewRecord)?\Yii::t('app', 'emergencia.create.label.botao.salvar_aviso', 'Salvar Aviso'):\Yii::t('app', 'emergencia.create.label.botao.atualizar_aviso', 'Atualizar Aviso'), ['class' => 'btn btn-success']) ?>
    </div>

<?php ActiveForm::end(); ?> 

</div>
<?php if(empty($jurisdicaoUsuario)){?> 
<div class="alert alert-danger">
  	<?php echo \Yii::t('app', 'emergencia.jurisdicao.sem_jurisdicao');?>
</div>
<?php }?>

 <div id="map" style="height: 440px"></div>
 
 <?php 
 //Criação dos objetos AVISO, para instaurálos nas camadas de visualização de deesenho do mapa.
	if(!empty($emergencias)){
 		foreach ($emergencias as $e){
				// Formatando data para formato Internacional
				$dataInicial = \DateTime::createFromFormat('d/m/Y H:i', $e->data_inicial);
				$dataEstimada = \DateTime::createFromFormat('d/m/Y H:i', $e->duracao_estimada);
		?>    	
			<input class="avisos_correntes" data-wkt="<?= $e->location ?>" data-cor="<?= $e->risco->rgb ?>" data-inicio="<?=  $dataInicial->format("Y-m-d H:i:s");?>" data-fim="<?= $dataEstimada->format("Y-m-d H:i:s"); ?>" style="display:none"/>
		<?php 
		}
	}
 ?>
     
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

		var drawPolygon_avisos = function(element, color, inicio, fim, fim_dia_hoje) {
			var wkt = new OpenLayers.Format.WKT();
			var features = wkt.read(element);
			if (features) {
				features.geometry.transform(map.displayProjection, map.getProjectionObject());
				if (color) {
					var features_futuro = wkt.read(element);
					features_futuro.geometry.transform(map.displayProjection, map.getProjectionObject());
					features_futuro.style = {
						fillOpacity : 1,
						strokeColor : color,
						fillColor : color
					}
					features.style = {
						fillOpacity : 1,
						strokeColor : color,
						fillColor : color
					}
				}
				//console.log("anoia");
				if ((inicio <= fim_dia_hoje) && (fim <= fim_dia_hoje || fim > fim_dia_hoje)){
					avisos_hoje.addFeatures(features);
				}
				if (inicio > fim_dia_hoje || fim >= fim_dia_hoje){
					avisos_futuros.addFeatures(features_futuro);
				}
			} else {
				console.log('Bad WKT');
			}
		};

		var map = new OpenLayers.Map({
			div: "map",
			projection: toProjection,
			displayProjection: fromProjection,			
			layers: [ new OpenLayers.Layer.OSM( "OSM", "", {displayInLayerSwitcher: false} ) ]
		});


		<?php if(!empty($jurisdicaoUsuario)){ ?>		
			
			var jurisdicaoStyle = new OpenLayers.Style({ fillColor: "#7EE57A", 'strokeWidth': 1,'strokeColor': "#000", fillOpacity:0.5});
			layer_vector_jurisdicao = new OpenLayers.Layer.Vector({layers : "Jurisdicao"}, {displayInLayerSwitcher : false, styleMap: jurisdicaoStyle});
			var wkt = new OpenLayers.Format.WKT();
			var features = wkt.read('<?php echo $jurisdicaoUsuario?>');
			if (features) {				
				layer_vector_jurisdicao.addFeatures(features);
				map.addLayer(layer_vector_jurisdicao);
			} else {
				console.log('Bad WKT');
			}	

		<?php }	?>
		

		layer_vector = new OpenLayers.Layer.Vector({
			layers : "Poligonos"
		}, {
			displayInLayerSwitcher : false
		});
		layer_vector.displayInLayerSwitcher=false;
		var layer_vector_polygon = new OpenLayers.Layer.Vector("Vector Criando",{styleMap: styleMap});
		layer_vector_polygon.displayInLayerSwitcher=false;

		panelControls = [
				new OpenLayers.Control.Navigation({title:'Navegar'}),
				// new OpenLayers.Control.DrawFeature(layer_vector,
				// OpenLayers.Handler.Polygon, {'displayClass':
				// 'olControlDrawFeaturePath'}),
				//editaPoligono
				new OpenLayers.Control.ModifyFeature(layer_vector_polygon, {'displayClass' : 'olControlDrawFeaturePoint', vertexRenderIntent: "vertex", virtualStyle: virtual, title:'Editar Polígono'}),
				new OpenLayers.Control.DrawFeature(layer_vector_polygon,
						OpenLayers.Handler.Polygon, {
							'displayClass' : 'olControlDrawFeaturePath', title:'Criar Polígono'
						}) ];
		toolbar = new OpenLayers.Control.Panel({
			displayClass : 'olControlEditingToolbar',
			defaultControl : panelControls[0]
		});
		
		toolbar.addControls(panelControls);
		map.addLayer(layer_vector);
		map.addLayer(layer_vector_polygon);
		map.addControl(toolbar);

		//Avisos hoje/features_futuro
		avisos_hoje = new OpenLayers.Layer.Vector("Hoje",{layers : "Hoje"}, {displayInLayerSwitcher : false});
		map.addLayer(avisos_hoje);
		avisos_futuros = new OpenLayers.Layer.Vector("Futuros",{layers : "Futuros"}, {displayInLayerSwitcher : false});
		map.addLayer(avisos_futuros);
		var ano=new Date();
		ano=ano.getFullYear();
		var mes=new Date();
		mes=mes.getMonth();
		var dia=new Date();
		dia=dia.getDate();
		var fim_dia_hoje=new Date(ano,mes,dia,23,59,59);
		fim_dia_hoje=fim_dia_hoje.format('Y-m-d H:i:s');
		var temAviso=0;
		$('.avisos_correntes').each(function(i, obj) {
			/*console.log($(obj).data('wkt'));
		    /console.log($(obj).data('cor'));
			console.log($(obj).data('inicio'));
			console.log($(obj).data('fim'));*/
		    drawPolygon_avisos($(obj).data('wkt'), $(obj).data('cor'), $(obj).data('inicio'), $(obj).data('fim'), fim_dia_hoje);
		    $(obj).remove();
		    temAviso++;
		});
		avisos_hoje.setVisibility(false);
		avisos_futuros.setVisibility(false);
		if (temAviso > 0){
			var lSwitcher=new OpenLayers.Control.LayerSwitcher({'position':"right-corner",'ascending':true});
			map.addControl(lSwitcher);
			lSwitcher.maximizeControl();
			lSwitcher.dataLbl.innerText = "Avisos Correntes"
		}
		//avisos hoje/futuro

		panelControls[1].featureAdded = function() {
			// layer_vector_polygon.destroyFeatures(layer_vector_polygon.features);
		}
		
		function featureModified() {
			var campo = $("#wkt");
			$('#ufsTxt').html('');
			
			campo.val(layer_vector_polygon.features[0].geometry);

		}
		
		layer_vector_polygon.events.on({
			featuremodified: featureModified
		});

		layer_vector_polygon.preFeatureInsert = function() {
			idAction.control.deactivate();
			// layer_vector.destroyFeatures(layer_vector.features);
			$("#table-geos").find('tbody').empty();
		};

		layer_vector_polygon.onFeatureInsert = function() {
			// controle_vector = false;
			// $.getJSON('/cv/mapa/cidades_by_area?geo=' +
			// layer_vector_polygon.features[0].geometry + '&callback=?',
			// function(data) {
			// // $.each(data.resultado, function(index, element) {

			// // $("#table-geos").find('tbody').append($('<tr value='
			// +element.id+'>').append($('<td>').html("<input type='hidden'
			// value='"+element.geo+"' name='municipios' /> ")));
			// // drawPolygon(element.geo, '#FF9400');
			// // });
			// drawPolygon(data.resultado, '#FF9400');
			var campo = $("#wkt");
			campo.val(layer_vector_polygon.features[0].geometry);
			// });
			//console.log(campo);
		};

		layer_vector_polygon.preFeatureInsert = function() {
			// idAction.control.deactivate();
			layer_vector_polygon.destroyFeatures(layer_vector_polygon.features);
			// $("#table-geos").find('tbody').empty();
		};

		
		map.setCenter(new OpenLayers.LonLat(-54.404297,-14.774883).transform(fromProjection, toProjection), 4);
		<?php if(!empty($model->location)){?>
		var wkt = new OpenLayers.Format.WKT();
		var features = wkt.read($("#wkt").attr("value"));
		if (features) {
			if ($("#wkt").data("color")) {
				features.style = {
					fillOpacity : 0.7
				}
				// features.style = { fillOpacity: 0.4, strokeColor: '0000FF',
				// fillColor: '0000FF' }
			}
			layer_vector_polygon.addFeatures(features);
			map.addLayer(layer_vector_polygon);
		} else {
			console.log('Bad WKT');
		}
		<?php } ?>	

		
			
	});
</script>

