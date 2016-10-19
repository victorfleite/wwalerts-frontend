<?php

namespace app\modules\backend\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class AssociarJurisdicaoGrupoForm extends Model
{
    public $jurisdicoes;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
        	['jurisdicoes', 'safe']
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
        ];
    }

  
}
