<?php

namespace app\modules\backend\models;

use Yii;

/**
 * This is the model class for table "log".
 *
 * @property string $id
 * @property integer $usuario_id
 * @property string $acao_realizada
 * @property string $data_log
 * @property string $detalhes
 * @property string $version
 *
 * @property Usuario $usuario
 */
class Log extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['usuario_id', 'version'], 'integer'],
            [['data_log'], 'safe'],
            [['detalhes'], 'string'],
            [['version'], 'required'],
            [['acao_realizada'], 'string', 'max' => 100],
            [['usuario_id'], 'exist', 'skipOnError' => true, 'targetClass' => Usuario::className(), 'targetAttribute' => ['usuario_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'usuario_id' => 'Usuario ID',
            'acao_realizada' => 'Acao Realizada',
            'data_log' => 'Data Log',
            'detalhes' => 'Detalhes',
            'version' => 'Version',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsuario()
    {
        return $this->hasOne(Usuario::className(), ['id' => 'usuario_id']);
    }
}
