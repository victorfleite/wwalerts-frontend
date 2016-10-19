<?php

namespace app\modules\backend\models;

use Yii;

/**
 * This is the model class for table "funcao".
 *
 * @property string $id
 * @property string $version
 * @property string $nome
 *
 * @property UsuarioFuncao[] $usuarioFuncaos
 * @property Usuario[] $usuarios
 */
class Funcao extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'funcao';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'version', 'nome'], 'required'],
            [['id', 'version'], 'integer'],
            [['nome'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'version' => 'Version',
            'nome' => 'Nome',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsuarioFuncaos()
    {
        return $this->hasMany(UsuarioFuncao::className(), ['funcao_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsuarios()
    {
        return $this->hasMany(Usuario::className(), ['id' => 'usuario_id'])->viaTable('usuario_funcao', ['funcao_id' => 'id']);
    }
}
