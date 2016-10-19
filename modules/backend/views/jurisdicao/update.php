<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\backend\models\Jurisdicao */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Jurisdição',
]) . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Jurisdição'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="jurisdicao-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
