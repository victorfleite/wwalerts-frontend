<?php

namespace app\modules\backend\models;

use Yii;

/**
 * This is the model class for table "rl_grupo_jurisdicao".
 *
 * @property integer $jurisdicao_id
 * @property integer $grupo_id
 *
 * @property Grupo $grupo
 * @property Jurisdicao $jurisdicao
 */
class RlGrupoJurisdicao extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rl_grupo_jurisdicao';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['jurisdicao_id', 'grupo_id'], 'required'],
            [['jurisdicao_id', 'grupo_id'], 'integer'],
            [['grupo_id'], 'exist', 'skipOnError' => true, 'targetClass' => Grupo::className(), 'targetAttribute' => ['grupo_id' => 'id']],
            [['jurisdicao_id'], 'exist', 'skipOnError' => true, 'targetClass' => Jurisdicao::className(), 'targetAttribute' => ['jurisdicao_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'jurisdicao_id' => Yii::t('app', 'Jurisdicao ID'),
            'grupo_id' => Yii::t('app', 'Grupo ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGrupo()
    {
        return $this->hasOne(Grupo::className(), ['id' => 'grupo_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJurisdicao()
    {
        return $this->hasOne(Jurisdicao::className(), ['id' => 'jurisdicao_id']);
    }
}
