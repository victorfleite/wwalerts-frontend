<?php

namespace app\modules\backend\models;

use Yii;

/**
 * This is the model class for table "codar".
 *
 * @property string $id
 * @property string $version
 * @property string $descricao
 * @property string $i18n
 * @property string $categoriacap
 * @property string $icone_path
 *
 * @property Cap12[] $cap12s
 * @property Cap12Description[] $cap12Descriptions
 * @property Emergencia[] $emergencias
 */
class Codar extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'codar';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'version', 'descricao', 'i18n'], 'required'],
            [['id', 'version'], 'integer'],
            [['descricao', 'i18n'], 'string', 'max' => 255],
            [['categoriacap'], 'string', 'max' => 50],
            [['icone_path'], 'string', 'max' => 200],
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
            'categoriacap' => 'Categoriacap',
            'icone_path' => 'Icone Path',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCap12s()
    {
        return $this->hasMany(Cap12::className(), ['codar_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCap12Descriptions()
    {
        return $this->hasMany(Cap12Description::className(), ['codar_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmergencias()
    {
        return $this->hasMany(Emergencia::className(), ['codar_id' => 'id']);
    }
}
