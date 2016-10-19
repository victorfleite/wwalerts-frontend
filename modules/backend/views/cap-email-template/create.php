<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\backend\models\CapEmailTemplate */

$this->title = Yii::t('app', 'Create Cap Email Template');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Cap Email Templates'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cap-email-template-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
