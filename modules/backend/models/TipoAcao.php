<?php

namespace app\modules\backend\models;

use Yii;

/**
 * This is the model class for table "tipo_acao".
 *
 * @property string $id
 * @property string $version
 * @property string $i18n
 * @property string $nome
 *
 * @property Acao[] $acaos
 */
class TipoAcao extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tipo_acao';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'version', 'i18n', 'nome'], 'required'],
            [['id', 'version'], 'integer'],
            [['i18n', 'nome'], 'string', 'max' => 255],
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
            'i18n' => 'I18n',
            'nome' => 'Nome',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAcaos()
    {
        return $this->hasMany(Acao::className(), ['tipo_acao_id' => 'id']);
    }
}
