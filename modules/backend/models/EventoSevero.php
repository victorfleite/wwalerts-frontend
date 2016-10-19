<?php

namespace app\modules\backend\models;

use Yii;

/**
 * This is the model class for table "evento_severo".
 *
 * @property string $id
 * @property string $version
 * @property integer $chuva_intensidade
 * @property string $data
 * @property integer $vento_intensidade
 * @property string $texto
 * @property string $owner_id
 *
 * @property CityEventos[] $cityEventos
 * @property City[] $municipios
 * @property Usuario $owner
 */
class EventoSevero extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'evento_severo';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'version', 'chuva_intensidade', 'data', 'vento_intensidade', 'texto', 'owner_id'], 'required'],
            [['id', 'version', 'chuva_intensidade', 'vento_intensidade', 'owner_id'], 'integer'],
            [['data'], 'safe'],
            [['texto'], 'string'],
            [['owner_id'], 'exist', 'skipOnError' => true, 'targetClass' => Usuario::className(), 'targetAttribute' => ['owner_id' => 'id']],
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
            'chuva_intensidade' => 'Chuva Intensidade',
            'data' => 'Data',
            'vento_intensidade' => 'Vento Intensidade',
            'texto' => 'Texto',
            'owner_id' => 'Owner ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCityEventos()
    {
        return $this->hasMany(CityEventos::className(), ['evento_severo_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMunicipios()
    {
        return $this->hasMany(City::className(), ['id' => 'municipio_id'])->viaTable('city_eventos', ['evento_severo_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOwner()
    {
        return $this->hasOne(Usuario::className(), ['id' => 'owner_id']);
    }
}
