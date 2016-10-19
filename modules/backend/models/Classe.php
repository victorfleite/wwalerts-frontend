<?php

namespace app\modules\backend\models;

use Yii;

/**
 * This is the model class for table "classe".
 *
 * @property string $id
 * @property string $version
 * @property string $nome
 *
 * @property Acao[] $acaos
 */
class Classe extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'classe';
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
            'id' => Yii::t('app', 'ID'),
            'version' => Yii::t('app', 'Version'),
            'nome' => Yii::t('app', 'Nome'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAcaos()
    {
        return $this->hasMany(Acao::className(), ['classe_id' => 'id']);
    }
}
