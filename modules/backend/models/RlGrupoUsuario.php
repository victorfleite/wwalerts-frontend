<?php

namespace app\modules\backend\models;

use Yii;
use app\models\Usuario;

/**
 * This is the model class for table "rl_grupo_usuario".
 *
 * @property integer $grupo_id
 * @property integer $usuario_id
 *
 * @property Grupo $grupo
 * @property Usuario $usuario
 */
class RlGrupoUsuario extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rl_grupo_usuario';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['grupo_id', 'usuario_id'], 'required'],
            [['grupo_id', 'usuario_id'], 'integer'],
            [['grupo_id'], 'exist', 'skipOnError' => true, 'targetClass' => Grupo::className(), 'targetAttribute' => ['grupo_id' => 'id']],
            [['usuario_id'], 'exist', 'skipOnError' => true, 'targetClass' => Usuario::className(), 'targetAttribute' => ['usuario_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'grupo_id' => Yii::t('app', 'Grupo ID'),
            'usuario_id' => Yii::t('app', 'Usuario ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGrupo()
    {
        return $this->hasOne(Grupo::className(), ['id' => 'grupo_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsuario()
    {
        return $this->hasOne(Usuario::className(), ['id' => 'usuario_id']);
    }
}
