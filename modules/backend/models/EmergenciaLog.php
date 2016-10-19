<?php

namespace app\modules\backend\models;

use Yii;
use app\models\Usuario;

/**
 * This is the model class for table "emergencia_log".
 *
 * @property integer $id
 * @property string $data
 * @property string $descricao
 * @property string $emergencia_id
 * @property string $responsavel_id
 * @property string $i18n
 *
 * @property Emergencia $emergencia
 * @property Usuario $responsavel
 */
class EmergenciaLog extends \yii\db\ActiveRecord
{
	
	const DESCRICAO_EMERGENCIA_CRIADA = 'Aviso de Eventos Meteorológicos Severos criado';
	const DESCRICAO_EMERGENCIA_ATUALIZADA = 'Aviso de Eventos Meteorológicos Severos atualizado';
	const DESCRICAO_EMERGENCIA_CANCELADA = 'Aviso de Eventos Meteorológico Severo Cancelado';
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'emergencia_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['data', 'descricao', 'emergencia_id', 'responsavel_id', 'i18n'], 'required'],
            [['data'], 'safe'],
            [['descricao'], 'string'],
            [['emergencia_id', 'responsavel_id'], 'integer'],
            [['i18n'], 'string', 'max' => 255],
            [['emergencia_id'], 'exist', 'skipOnError' => true, 'targetClass' => Emergencia::className(), 'targetAttribute' => ['emergencia_id' => 'id']],
            [['responsavel_id'], 'exist', 'skipOnError' => true, 'targetClass' => Usuario::className(), 'targetAttribute' => ['responsavel_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'data' => Yii::t('app', 'Data'),
            'descricao' => Yii::t('app', 'Descricao'),
            'emergencia_id' => Yii::t('app', 'Emergencia ID'),
            'responsavel_id' => Yii::t('app', 'Responsavel ID'),
            'i18n' => Yii::t('app', 'I18n'),
        ];
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
    public function getResponsavel()
    {
        return $this->hasOne(Usuario::className(), ['id' => 'responsavel_id']);
    }
    
    public function salvarAlteracoes($emergencia, $attributosAlterados){

    	// TODO Descrever os campos que foram alterados.    	
    	$this->responsavel_id = $emergencia->owner_id;
    	$this->i18n = "emergencia_log.i18n.emergencia_atualizada" ;
    	$this->descricao = \yii::t('app', 'emergencia_log.descricao.emergencia.atualizada');
    	$this->data = date('Y-m-d H:i:s');
    	$this->emergencia_id = $emergencia->id;
    	$this->save();
    	
    }
    
}
