<?php

namespace app\modules\backend\models;

use Yii;

/**
 * This is the model class for table "cap_email".
 *
 * @property integer $id
 * @property string $email
 * @property integer $instituicao_id
 * @property string $status
 * @property string $nome
 *
 * @property Instituicao $instituicao
 */
class CapEmail extends \yii\db\ActiveRecord
{
	const STATUS_ATIVO = 'A';
	const STATUS_INATIVO = 'I';
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cap_email';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email', 'instituicao_id'], 'required'],
            [['instituicao_id'], 'integer'],
            [['email'], 'string', 'max' => 200],
            [['status'], 'string', 'max' => 1],
            [['nome'], 'string', 'max' => 150],
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
            'email' => Yii::t('app', 'Email'),
            'instituicao_id' => Yii::t('app', 'Instituicao ID'),
            'status' => Yii::t('app', 'Status'),
            'nome' => Yii::t('app', 'Nome'),
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
