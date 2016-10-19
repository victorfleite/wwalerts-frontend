<?php

namespace app\modules\backend\models;

use Yii;

/**
 * This is the model class for table "vw_emergencia".
 *
 * @property string $id
 * @property string $data_inicial
 * @property string $data_modelo
 * @property string $duracao_estimada
 * @property boolean $acao1
 * @property boolean $acao2
 * @property boolean $acao3
 * @property boolean $acao4
 * @property boolean $acao5
 * @property boolean $acao6
 * @property boolean $encerrado
 * @property string $risco_id
 * @property string $risco_descricao
 * @property string $risco_i18n
 * @property string $risco_rgb
 * @property string $risco_severitycap
 * @property string $codar_id
 * @property string $codar_descricao
 * @property string $codar_i18n
 * @property string $codar_categoriacap
 * @property string $codar_icone_path
 * @property string $usuario_nome
 * @property string $instituicao_nome
 * @property string $instituicao_sigla
 */
class VwEmergencia extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'vw_emergencia';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'risco_id', 'codar_id'], 'integer'],
            [['data_inicial', 'data_modelo', 'duracao_estimada'], 'safe'],
            [['acao1', 'acao2', 'acao3', 'acao4', 'acao5', 'acao6', 'encerrado'], 'boolean'],
            [['risco_rgb'], 'string'],
            [['risco_descricao', 'risco_i18n', 'codar_descricao', 'codar_i18n', 'usuario_nome', 'instituicao_nome', 'instituicao_sigla'], 'string', 'max' => 255],
            [['risco_severitycap'], 'string', 'max' => 30],
            [['codar_categoriacap'], 'string', 'max' => 50],
            [['codar_icone_path'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'data_inicial' => Yii::t('app', 'Data Inicial'),
            'data_modelo' => Yii::t('app', 'Data Modelo'),
            'duracao_estimada' => Yii::t('app', 'Duracao Estimada'),
            'acao1' => Yii::t('app', 'Acao1'),
            'acao2' => Yii::t('app', 'Acao2'),
            'acao3' => Yii::t('app', 'Acao3'),
            'acao4' => Yii::t('app', 'Acao4'),
            'acao5' => Yii::t('app', 'Acao5'),
            'acao6' => Yii::t('app', 'Acao6'),
            'encerrado' => Yii::t('app', 'Encerrado'),
            'risco_id' => Yii::t('app', 'Risco ID'),
            'risco_descricao' => Yii::t('app', 'Risco Descricao'),
            'risco_i18n' => Yii::t('app', 'Risco I18n'),
            'risco_rgb' => Yii::t('app', 'Risco Rgb'),
            'risco_severitycap' => Yii::t('app', 'Risco Severitycap'),
            'codar_id' => Yii::t('app', 'Codar ID'),
            'codar_descricao' => Yii::t('app', 'Codar Descricao'),
            'codar_i18n' => Yii::t('app', 'Codar I18n'),
            'codar_categoriacap' => Yii::t('app', 'Codar Categoriacap'),
            'codar_icone_path' => Yii::t('app', 'Codar Icone Path'),
            'usuario_nome' => Yii::t('app', 'Usuario Nome'),
            'instituicao_nome' => Yii::t('app', 'Instituicao Nome'),
            'instituicao_sigla' => Yii::t('app', 'Instituicao Sigla'),
        ];
    }
}
