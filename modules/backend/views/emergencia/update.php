<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Emergencia */

$this->title = 'Update Emergencia: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Emergencias', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['show', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="emergencia-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model, 'emergencias'=>$emergencias, 'jurisdicaoUsuario'=>$jurisdicaoUsuario
    ]) ?>

</div>
