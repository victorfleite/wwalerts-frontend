<?php

namespace app\modules\backend\models;

use Yii;


/**
 * This is the model class for table "instituicao".
 *
 * @property string $id
 * @property string $version
 * @property string $email
 * @property string $fone
 * @property string $nome
 * @property string $pais
 * @property string $sigla
 * @property string $siglacap
 * @property string $sendercap
 * @property string $contatocap
 * @property string $languagecap
 *
 * @property Cap12[] $cap12s
 * @property CapEmail[] $capEmails
 * @property CapEmailTemplate[] $capEmailTemplates
 * @property Usuario[] $usuarios
 */
class Instituicao extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'instituicao';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'version', 'email', 'fone', 'nome', 'pais', 'sigla', 'siglacap', 'sendercap', 'contatocap', 'languagecap'], 'required'],
            [['id', 'version'], 'integer'],
            [['email', 'fone', 'nome', 'pais', 'sigla'], 'string', 'max' => 255],
            [['siglacap'], 'string', 'max' => 30],
            [['sendercap'], 'string', 'max' => 50],
            [['contatocap'], 'string', 'max' => 150],
            [['languagecap'], 'string', 'max' => 15],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'version' => 'Version',
            'email' => 'Email',
            'fone' => 'Fone',
            'nome' => 'Nome',
            'pais' => 'Pais',
            'sigla' => 'Sigla',
            'siglacap' => 'Siglacap',
            'sendercap' => 'Sendercap',
            'contatocap' => 'Contatocap',
            'languagecap' => 'Languagecap',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCap12s()
    {
        return $this->hasMany(Cap12::className(), ['instituicao_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCapEmails()
    {
        return $this->hasMany(CapEmail::className(), ['instituicao_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCapEmailTemplates()
    {
        return $this->hasMany(CapEmailTemplate::className(), ['instituicao_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsuarios()
    {
        return $this->hasMany(Usuario::className(), ['instituicao_id' => 'id']);
    }
    /**
     * Retorna o nome Completo da Instituicao NOME/SIGLA
     * @return string
     */
    public function getNomeCompleto(){
    	return $this->nome . "/" . $this->sigla;	
    }
}
