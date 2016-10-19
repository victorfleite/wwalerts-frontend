<?php

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\grid\GridView;
use app\modules\backend\models\Emergencia;
use yii\bootstrap\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = \Yii::t('app', 'emergencia.index.title');
$this->params['breadcrumbs'][] = $this->title;

$this->registerJsFile('//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js', ['position'=>yii\web\View::POS_HEAD]);
$this->registerJsFile('@web/js/OpenLayers-2.12/OpenLayers.js', ['position'=>yii\web\View::POS_HEAD]);
$this->registerCssFile('@web/js/OpenLayers-2.12/theme/default/style.css', ['position'=>yii\web\View::POS_HEAD]);

$models =  $dataProvider->getModels();

?>
<div class="emergencia-index">



    <h1><?= \Yii::t('app', 'emergencia.index.title') ?></h1>
	<?php  //echo $this->render('_search', ['model' => $searchModel]); ?>
  	<?php 
		Pjax::begin();
	?>

	<?php  echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
    	//'filterModel' => $searchModel,    	
        'columns' => [
    		/*[
    			'class' => 'yii\grid\CheckboxColumn',
				'checkboxOptions' => function ($model, $key, $index, $column) {
    				return ['data-wkt' => $model->location];
				}
			],*/
    		[
    		'class' => 'yii\grid\ActionColumn',
				'template' => '{enter}',
    						'buttons' => [
    						'enter' => function ($url, $model) {
	    						return Html::a('<span class="glyphicon glyphicon-check"></span>', $url,
	    						[
	                            'title' => Yii::t('app', 'emergencia.index.label.entrar'),
	    		                            ]);
	    						}
	    						],
	    					'urlCreator' => function ($action, $model, $key, $index) {
		    						if ($action === 'enter') {
		    						return Url::to(['emergencia/show', 'id'=>$model->id]);
		    						}
    						}
    		],
			[
				'attribute' => 'id',
				'label'=>\Yii::t('app', 'emergencia.index.label.id', 'ID'),
			],
    		[
    			'label'=>\Yii::t('app', 'emergencia.index.label.risco', 'Risco'),
    			'value'=>function($data){
		                return \Yii::t('app', $data->risco->i18n, $data->risco->descricao);
    			},
    			//'filter' => Html::activeDropDownList($searchModel, 'risco_id', \yii\helpers\ArrayHelper::map(Risco::find()->orderBy('descricao')->asArray()->all(), 'id', 'descricao'), ['prompt'=>'']),
    	    ],
            [
            	'label'=>\Yii::t('app', 'emergencia.index.label.codar', 'Codar'),
            	'value'=>function($data){
            		return \Yii::t('app', $data->codar->i18n, $data->codar->descricao);
            	},
            	//'filter' => Html::activeDropDownList($searchModel, 'codar_id', \yii\helpers\ArrayHelper::map(Codar::find()->orderBy('descricao')->asArray()->all(), 'id', 'descricao'), ['prompt'=>'']),
    	   ],    		
            [
    			'attribute' => 'data_inicial',
    			'label'=>\Yii::t('app', 'emergencia.index.label.data', 'Data Inicial'),
    			
    			/*'filter' => DateTimePicker::widget([
    					'model' => $searchModel,
    					'attribute' => 'data_inicial',
    					'clientOptions'=>['timeFormat'=> "hh:mm"]
    			])*/
    		],
    		[
    			'attribute' => 'duracao_estimada',
    			'label'=>\Yii::t('app', 'emergencia.index.label.duracao', 'Data Estimada'),
    			/*'filter' => DateTimePicker::widget([
				    		'model' => $searchModel,
				    		'attribute' => 'duracao_estimada',
							'clientOptions'=>['timeFormat'=> "hh:mm"]
    			])*/
    		],
            [
    			'attribute' => 'owner.instituicao.sigla',
    			'label'=>\Yii::t('app', 'emergencia.index.label.instituicao', 'Instituição'),
    		],
    		[
    			'label'=>\Yii::t('app', 'emergencia.index.label.protocolo.status', 'Status'),
    			'content'=>function($data){
					$cor = $data->risco->rgb;
					return "<div style='background-color:".$cor."'>&nbsp;</div>";
    			}
    		],    		
    		[
    			'label'=>\Yii::t('app', 'emergencia.index.label.encerrado', 'Encerrado'),
    			'value'=>function($data){
					if($data->encerrado == Emergencia::ENCERRADO_SIM){
						return \Yii::t('app', 'emergencia.index.encerrado.sim', 'Sim');
					}else{
						return \Yii::t('app', 'emergencia.index.encerrado.nao', 'Não');
					}
    				
    			}
    		],
    		[
    			'attribute' => 'owner.nome',
    			'label'=>\Yii::t('app', 'emergencia.index.label.autor', 'Responsável'),
    		],            
        ],       
        'toolbar' =>  [
	        ['content'=>
	           Html::a('<i class="glyphicon glyphicon-plus"></i> '. \Yii::t('app', 'emergencia.index.label.novo.aviso'), ['create'], ['class'=>'btn btn-success'])
	        ],
        	'{export}',
        	'{toggleData}'
    		],
	    'pjax' => true,
	    'bordered' => true,
	    'striped' => false,
	    'condensed' => false,
	    'responsive' => true,
	    'hover' => true,
	    'floatHeader' => true,
//	    'floatHeaderOptions' => ['scrollingTop' => $scrollingTop],
	    'panel' => [
	        'type' => GridView::TYPE_PRIMARY
	    ],
    ]); ?>
    
    
    <?php 
		Pjax::end();
	?>

</div>
 <h1><?= \Yii::t('app', 'emergencia.index.title_mapa') ?></h1>
 <h4><?= \Yii::t('app', 'emergencia.index.total_avisos') ?> : <?php echo $dataProvider->getTotalCount();?></h4>
<div>
   	<div id="map" style="height: 500px;">
</div>


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
	map.setCenter(new OpenLayers.LonLat(-54.404297,-14.774883).transform(fromProjection, toProjection), 4);
		
	<?php foreach($models as $emergencia){ ?>
	var wkt = new OpenLayers.Format.WKT();
	layer_vector= new OpenLayers.Layer.Vector('Aviso', { displayInLayerSwitcher: false});
		
	var features = wkt.read('<?php echo $emergencia->location;?>');
	if (features) {
		features.style = {
				fillOpacity : 0.7,
				strokeColor : '#000',
				strokeWidth : 1,
				fillColor : '<?php echo $emergencia->risco->rgb;?>'
		}
		layer_vector.addFeatures(features);
		
	} else {
		console.log('Bad WKT');
	}
	map.addLayer(layer_vector);	
	<?php }?>
});
</script>    
    


