<?php

namespace app\modules\backend\models;

use Yii;
use app\components\behaviors\PolygonBehavior;

/**
 * This is the model class for table "jurisdicao".
 *
 * @property integer $id
 * @property string $nome
 * @property string $geometria
 * @property integer $instituicao_id
 *
 * @property Acao $instituicao
 * @property RlGrupoJurisdicao[] $rlGrupoJurisdicaos
 * @property Grupo[] $grupos
 */
class Jurisdicao extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'jurisdicao';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nome','cor','instituicao_id', 'geometria'], 'required'],
            [['nome', 'geometria'], 'string'],
            [['instituicao_id'], 'integer'],
            [['instituicao_id'], 'exist', 'skipOnError' => true, 'targetClass' => Acao::className(), 'targetAttribute' => ['instituicao_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'nome' => Yii::t('app', 'Nome'),
            'cor' => Yii::t('app', 'Cor'),
            'geometria' => Yii::t('app', 'Geometria (WKT - Polygon ou Multipolygon [Projeção 3857 - Google])'),
            'instituicao_id' => Yii::t('app', 'Instituição'),
        ];
    }
    
	public function behaviors() {
		return [ 
				'geometry' => [ 
						'class' => PolygonBehavior::className (),
						'attribute' => 'geometria',
						'type' => PolygonBehavior::GEOMETRY_POLYGON,
				] 
		];
	}

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstituicao()
    {
        return $this->hasOne(Instituicao::className(), ['id' => 'instituicao_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRlGrupoJurisdicaos()
    {
        return $this->hasMany(RlGrupoJurisdicao::className(), ['jurisdicao_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGrupos()
    {
        return $this->hasMany(Grupo::className(), ['id' => 'grupo_id'])->viaTable('rl_grupo_jurisdicao', ['jurisdicao_id' => 'id']);
    }
    
    static function getTodasJurisdicoes($condicoes){
    	return self::find()->where($condicoes)->all();
    }
	/**
	 * Recupera a Jurisdição dos grupos que o usuário está associado
	 */    
    static function getJurisdicaoDoUsuario($idUsuario){
    	$query = "Select ST_AsText(ST_Transform(jurisdicao_usuario(:idUsuario ),3857)) as jurisdicao";
    	$res = \yii::$app->db->createCommand ( $query )->bindValue ( ':idUsuario', $idUsuario)->queryOne ();
    	return $res['jurisdicao'];
    }
    
    
    public function beforeDelete(){
    	// Deletar Relacionamentos
    	RlGrupoJurisdicao::deleteAll('jurisdicao_id = :jurisdicao_id', [':jurisdicao_id'=>$this->id]);
    	return true;
    }
    
}
