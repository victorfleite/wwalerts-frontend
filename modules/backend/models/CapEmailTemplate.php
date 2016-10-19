<?php

namespace app\modules\backend\models;

use Yii;

/**
 * This is the model class for table "cap_email_template".
 *
 * @property integer $id
 * @property integer $instituicao_id
 * @property string $template
 *
 * @property Instituicao $instituicao
 */
class CapEmailTemplate extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cap_email_template';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['instituicao_id'], 'integer'],
            [['template'], 'string'],
            [['instituicao_id'], 'exist', 'skipOnError' => true, 'targetClass' => Instituicao::className(), 'targetAttribute' => ['instituicao_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'instituicao_id' => Yii::t('app', 'Instituicao ID'),
            'template' => Yii::t('app', 'Template'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstituicao()
    {
        return $this->hasOne(Instituicao::className(), ['id' => 'instituicao_id']);
    }
}
