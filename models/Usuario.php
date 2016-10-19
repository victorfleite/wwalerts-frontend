<?php

namespace app\models;

use app\modules\backend\models\Instituicao;

use yii\base\NotSupportedException;

class Usuario extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
	//public $id;
	public $username;
    //public $authKey;
    //public $accessToken;
    
    const STATUS_ACTIVE = TRUE;
    
    /**
     * @inheritdoc
     */
    public static function tableName() {
    	return 'usuario';
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id'=>$id, 'ativo' => self::STATUS_ACTIVE]);
    }
    /**
     * 
     * @param unknown $username
     * @return Ambigous <\yii\db\ActiveRecord, multitype:, NULL>
     */
    public static function findByEmail($email){
    	return static::findOne(['email' => $email, 'ativo' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    /**
     * Validates password
     *
     * @param  string  $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($senha)
    {
        return $this->senha === md5($senha);
    }
    
    public function beforeSave($insert)
    {
    	/*if (parent::beforeSave($insert)) {
    		if ($this->isNewRecord) {
    			$this->auth_key = \Yii::$app->security->generateRandomString();
    		}
    		return true;
    	}
    	return false;*/
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
    	return [
    	[['id', 'version', 'email', 'fone_cel', 'fone_com', 'fone_res', 'instituicao_id', 'nome', 'senha'], 'required'],
    	[['id', 'version', 'instituicao_id'], 'integer'],
    	[['ativo'], 'boolean'],
    	[['email', 'fone_cel', 'fone_com', 'fone_res', 'nome', 'senha'], 'string', 'max' => 255],
    	[['instituicao_id'], 'exist', 'skipOnError' => true, 'targetClass' => Instituicao::className(), 'targetAttribute' => ['instituicao_id' => 'id']],
    	];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
    	return [
    	'id' => \yii::t('app', 'usuario.id'),
    	'email' => \yii::t('app', 'usuario.email'),
    	'fone_cel' => \yii::t('app', 'usuario.fone_cel'),
    	'fone_com' => \yii::t('app', 'usuario.fone_com'),
    	'fone_res' => \yii::t('app', 'usuario.fone_res'),
    	'instituicao_id' => \yii::t('app', 'usuario.instituicao_id'),
    	'nome' => \yii::t('app', 'usuario.nome'),
    	'senha' => \yii::t('app', 'usuario.senha'),
    	'ativo' => \yii::t('app', 'usuario.ativo'),
    	];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAcaos()
    {
    	return $this->hasMany(Acao::className(), ['responsavel_id' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCap12s()
    {
    	return $this->hasMany(Cap12::className(), ['usuario_id' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmergencias()
    {
    	return $this->hasMany(Emergencia::className(), ['owner_id' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEventoSeveros()
    {
    	return $this->hasMany(EventoSevero::className(), ['owner_id' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLogs()
    {
    	return $this->hasMany(Log::className(), ['usuario_id' => 'id']);
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
    public function getUsuarioFuncaos()
    {
    	return $this->hasMany(UsuarioFuncao::className(), ['usuario_id' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFuncaos()
    {
    	return $this->hasMany(Funcao::className(), ['id' => 'funcao_id'])->viaTable('usuario_funcao', ['usuario_id' => 'id']);
    }
    
    /**
     * Retorna o nome Completo do Usuário
     * @return string
     */
    public function getNomeCompleto(){
    	return $this->nome;
    }
}
