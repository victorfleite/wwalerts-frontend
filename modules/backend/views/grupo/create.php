<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\backend\models\Grupo */

$this->title = Yii::t('app', 'Novo Grupo de Trabalho');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Grupos de Trabalho'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="grupo-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
