<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\modules\backend\models\Jurisdicao;

/* @var $this yii\web\View */
/* @var $model app\modules\backend\models\Grupo */

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Grupos'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="grupo-view">

    <p>
        <?= Html::a(Yii::t('app', 'Alterar'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Apagar'), ['delete', 'id' => $model->id], [
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
            'descricao',
        ],
    ]);

    
    
    ?>
    
    
    

</div>
