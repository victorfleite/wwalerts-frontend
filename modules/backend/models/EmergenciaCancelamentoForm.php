<?php

namespace app\modules\backend\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class EmergenciaCancelamentoForm extends Model
{
    public $idMotivo;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // name, email, subject and body are required
            [['idMotivo'], 'required'],
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'idMotivo' => \yii::t('app', 'emergencia.cancel.label.motivo'),
        ];
    }

}
