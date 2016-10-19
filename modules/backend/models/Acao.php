<?php

namespace app\modules\backend\models;

use Yii;

/**
 * This is the model class for table "acao".
 *
 * @property string $id
 * @property string $version
 * @property string $classe_id
 * @property string $data
 * @property string $descricao
 * @property string $emergencia_id
 * @property string $responsavel_id
 * @property string $tipo_acao_id
 *
 * @property Classe $classe
 * @property Emergencia $emergencia
 * @property TipoAcao $tipoAcao
 * @property Usuario $responsavel
 */
class Acao extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'acao';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'version', 'classe_id', 'data', 'descricao', 'emergencia_id', 'responsavel_id', 'tipo_acao_id'], 'required'],
            [['id', 'version', 'classe_id', 'emergencia_id', 'responsavel_id', 'tipo_acao_id'], 'integer'],
            [['data'], 'safe'],
            [['descricao'], 'string'],
            [['classe_id'], 'exist', 'skipOnError' => true, 'targetClass' => Classe::className(), 'targetAttribute' => ['classe_id' => 'id']],
            [['emergencia_id'], 'exist', 'skipOnError' => true, 'targetClass' => Emergencia::className(), 'targetAttribute' => ['emergencia_id' => 'id']],
            [['tipo_acao_id'], 'exist', 'skipOnError' => true, 'targetClass' => TipoAcao::className(), 'targetAttribute' => ['tipo_acao_id' => 'id']],
            [['responsavel_id'], 'exist', 'skipOnError' => true, 'targetClass' => Usuario::className(), 'targetAttribute' => ['responsavel_id' => 'id']],
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
            'classe_id' => 'Classe ID',
            'data' => 'Data',
            'descricao' => 'Descricao',
            'emergencia_id' => 'Emergencia ID',
            'responsavel_id' => 'Responsavel ID',
            'tipo_acao_id' => 'Tipo Acao ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClasse()
    {
        return $this->hasOne(Classe::className(), ['id' => 'classe_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmergencia()
    {
        return $this->hasOne(Emergencia::className(), ['id' => 'emergencia_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipoAcao()
    {
        return $this->hasOne(TipoAcao::className(), ['id' => 'tipo_acao_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getResponsavel()
    {
        return $this->hasOne(Usuario::className(), ['id' => 'responsavel_id']);
    }
}
