<?php

namespace app\modules\backend\models;

use Yii;

/**
 * This is the model class for table "cap_1_2_description".
 *
 * @property integer $id
 * @property integer $codar_id
 * @property integer $risco_id
 * @property string $desc_ptb
 * @property string $marcador
 *
 * @property Codar $codar
 * @property Risco $risco
 */
class Cap12Description extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cap_1_2_description';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['codar_id', 'risco_id', 'desc_ptb'], 'required'],
            [['codar_id', 'risco_id'], 'integer'],
            [['desc_ptb'], 'string', 'max' => 500],
            [['marcador'], 'string', 'max' => 200],
            [['codar_id'], 'exist', 'skipOnError' => true, 'targetClass' => Codar::className(), 'targetAttribute' => ['codar_id' => 'id']],
            [['risco_id'], 'exist', 'skipOnError' => true, 'targetClass' => Risco::className(), 'targetAttribute' => ['risco_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'codar_id' => Yii::t('app', 'Codar ID'),
            'risco_id' => Yii::t('app', 'Risco ID'),
            'desc_ptb' => Yii::t('app', 'Desc Ptb'),
            'marcador' => Yii::t('app', 'Marcador'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCodar()
    {
        return $this->hasOne(Codar::className(), ['id' => 'codar_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRisco()
    {
        return $this->hasOne(Risco::className(), ['id' => 'risco_id']);
    }
}
