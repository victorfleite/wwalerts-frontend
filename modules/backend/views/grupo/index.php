<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\backend\models\GrupoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Grupos');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="grupo-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create Grupo'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'nome',
            'descricao',

            [
				'class' => 'yii\grid\ActionColumn',
				'template' => '{associar-usuarios}{associar-jurisdicoes}{update}{delete}',
				'buttons' => [
					'associar-jurisdicoes' => function ($url, $model) {
						return Html::a('<span class="glyphicon glyphicon-check"></span>', $url,
								[
								'title' => Yii::t('app', 'Associar Jurisdições ao Grupo'),
								]);
					},
					'associar-usuarios' => function ($url, $model) {
						return Html::a('<span class="glyphicon glyphicon-user"></span>', $url,
								[
								'title' => Yii::t('app', 'Associar Usuários ao Grupo'),
								]);
					}
				],
				'urlCreator' => function ($action, $model, $key, $index) {
					if ($action === 'delete') {
						return Url::to(['grupo/delete', 'id'=>$model->id]);
					}
					if ($action === 'update') {
						return Url::to(['grupo/update', 'id'=>$model->id]);
					}
					if ($action === 'associar-jurisdicoes') {
						return Url::to(['grupo/associar-jurisdicoes', 'id'=>$model->id]);
					}
					if ($action === 'associar-usuarios') {
						return Url::to(['grupo/associar-usuarios', 'id'=>$model->id]);
					}
				}

			],
        ],
    ]); ?>
</div>
