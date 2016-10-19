<?php

namespace app\modules\backend\models;

use Yii;
use app\models\Usuario;

/**
 * This is the model class for table "grupo".
 *
 * @property integer $id
 * @property string $nome
 * @property string $descricao
 *
 * @property RlGrupoJurisdicao[] $rlGrupoJurisdicaos
 * @property Jurisdicao[] $jurisdicaos
 * @property RlGrupoUsuario[] $rlGrupoUsuarios
 * @property Usuario[] $usuarios
 */
class Grupo extends \yii\db\ActiveRecord
{
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'grupo';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nome'], 'required'],
            [['nome'], 'string', 'max' => 150],
            [['descricao'], 'string', 'max' => 500],
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
            'descricao' => Yii::t('app', 'Descricao'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRlGrupoJurisdicaos()
    {
        return $this->hasMany(RlGrupoJurisdicao::className(), ['grupo_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJurisdicaos()
    {
        return $this->hasMany(Jurisdicao::className(), ['id' => 'jurisdicao_id'])->viaTable('rl_grupo_jurisdicao', ['grupo_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRlGrupoUsuarios()
    {
        return $this->hasMany(RlGrupoUsuario::className(), ['grupo_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsuarios()
    {
        return $this->hasMany(Usuario::className(), ['id' => 'usuario_id'])->viaTable('rl_grupo_usuario', ['grupo_id' => 'id']);
    }
    
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdsJurisdicaosAssociadosArray()
    {	
    	$jurisdicoes = $this->hasMany(Jurisdicao::className(), ['id' => 'jurisdicao_id'])->viaTable('rl_grupo_jurisdicao', ['grupo_id' => 'id'])->select(['id','nome'])->all();
    	$arr = array();
    	foreach($jurisdicoes as $jurisdicao){
    		$arr[]= $jurisdicao->id;
    	}
    	return $arr;
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdsUsuariosAssociadosArray()
    {
    	$usuarios = $this->hasMany(Usuario::className(), ['id' => 'usuario_id'])->viaTable('rl_grupo_usuario', ['grupo_id' => 'id'])->select(['id','nome'])->all();
    	$arr = array();
    	foreach($usuarios as $usuario){
    		$arr[]= $usuario->id;
    	}
    	return $arr;
    }
    
    
    public function beforeDelete(){
    	// Deletar Relacionamentos
    	RlGrupoUsuario::deleteAll('grupo_id = :grupo_id', [':grupo_id'=>$this->id]);
    	RlGrupoJurisdicao::deleteAll('grupo_id = :grupo_id', [':grupo_id'=>$this->id]);
    	return true;
    }
    
    
}
