<?php

namespace app\modules\backend\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class AssociarUsuarioGrupoForm extends Model
{
    public $usuarios;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
        	['usuarios', 'safe']
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
