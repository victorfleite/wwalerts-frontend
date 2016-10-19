<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Emergencia */

$this->title = 'Create Emergencia';
$this->params['breadcrumbs'][] = ['label' => 'Emergencias', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="emergencia-create">

    <h1><?= Html::encode($this->title) ?></h1>
    

    <?= $this->render('_form', [
        'model' => $model, 'emergencias'=>$emergencias, 'jurisdicaoUsuario'=>$jurisdicaoUsuario
    ]) ?>


</div>

