<?php

namespace app\modules\backend\models;

use Yii;

/**
 * This is the model class for table "emergencia_cancelamento".
 *
 * @property integer $id
 * @property string $motivo
 * @property string $status
 * @property string $i18n
 */
class EmergenciaCancelamento extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'emergencia_cancelamento';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['motivo'], 'string'],
            [['status'], 'string', 'max' => 1],
            [['i18n'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'motivo' => Yii::t('app', 'Motivo'),
            'status' => Yii::t('app', 'Status'),
            'i18n' => Yii::t('app', 'I18n'),
        ];
    }
}
