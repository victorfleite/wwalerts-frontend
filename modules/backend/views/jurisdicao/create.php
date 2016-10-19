<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\backend\models\Jurisdicao */

$this->title = Yii::t('app', 'Nova Jurisdição');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Jurisdição'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="jurisdicao-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
