<?php

namespace app\modules\backend\models;

use Yii;

/**
 * This is the model class for table "risco".
 *
 * @property string $id
 * @property string $version
 * @property string $descricao
 * @property string $i18n
 * @property string $rgb
 * @property string $severitycap
 *
 * @property Cap12[] $cap12s
 * @property Cap12Description[] $cap12Descriptions
 * @property Emergencia[] $emergencias
 */
class Risco extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'risco';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'version', 'descricao', 'i18n'], 'required'],
            [['id', 'version'], 'integer'],
            [['rgb'], 'string'],
            [['descricao', 'i18n'], 'string', 'max' => 255],
            [['severitycap'], 'string', 'max' => 30],
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
            'descricao' => 'Descricao',
            'i18n' => 'I18n',
            'rgb' => 'Rgb',
            'severitycap' => 'Severitycap',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCap12s()
    {
        return $this->hasMany(Cap12::className(), ['risco_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCap12Descriptions()
    {
        return $this->hasMany(Cap12Description::className(), ['risco_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmergencias()
    {
        return $this->hasMany(Emergencia::className(), ['risco_id' => 'id']);
    }
}
