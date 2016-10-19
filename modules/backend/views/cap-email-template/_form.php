<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use dosamigos\ckeditor\CKEditor;
use app\modules\backend\models\Instituicao;

/* @var $this yii\web\View */
/* @var $model app\modules\backend\models\CapEmailTemplate */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="cap-email-template-form">

    <?php $form = ActiveForm::begin(); ?>
    
    <?php      
    $instituicao = ArrayHelper::map(Instituicao::find()->orderBy('nome')->all(), 'id', 'nome');
    echo $form->field($model, 'instituicao_id')->dropdownList($instituicao,['prompt'=>'']);
	?>
    
	<?= $form->field($model, 'template')->widget(CKEditor::className(), [
        'options' => ['rows' => 6],
        'preset' => 'full'
    ]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
