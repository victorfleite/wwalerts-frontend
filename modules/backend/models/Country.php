<?php

namespace app\modules\backend\models;

use Yii;

/**
 * This is the model class for table "country".
 *
 * @property integer $id
 * @property string $fips
 * @property string $iso2
 * @property string $iso3
 * @property integer $un
 * @property string $name
 * @property integer $area
 * @property integer $pop2005
 * @property integer $region
 * @property integer $subregion
 * @property double $lon
 * @property double $lat
 * @property string $the_geom
 * @property double $center_latitude
 * @property double $center_longitude
 * @property string $acronym
 * @property boolean $parceiro_alertas
 *
 * @property Institution[] $institutions
 * @property Mesorregiao[] $mesorregiaos
 * @property State[] $states
 */
class Country extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'country';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['un', 'area', 'pop2005', 'region', 'subregion'], 'integer'],
            [['lon', 'lat', 'center_latitude', 'center_longitude'], 'number'],
            [['the_geom', 'acronym'], 'string'],
            [['parceiro_alertas'], 'boolean'],
            [['fips', 'iso2'], 'string', 'max' => 2],
            [['iso3'], 'string', 'max' => 3],
            [['name'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'fips' => Yii::t('app', 'Fips'),
            'iso2' => Yii::t('app', 'Iso2'),
            'iso3' => Yii::t('app', 'Iso3'),
            'un' => Yii::t('app', 'Un'),
            'name' => Yii::t('app', 'Name'),
            'area' => Yii::t('app', 'Area'),
            'pop2005' => Yii::t('app', 'Pop2005'),
            'region' => Yii::t('app', 'Region'),
            'subregion' => Yii::t('app', 'Subregion'),
            'lon' => Yii::t('app', 'Lon'),
            'lat' => Yii::t('app', 'Lat'),
            'the_geom' => Yii::t('app', 'The Geom'),
            'center_latitude' => Yii::t('app', 'Center Latitude'),
            'center_longitude' => Yii::t('app', 'Center Longitude'),
            'acronym' => Yii::t('app', 'Acronym'),
            'parceiro_alertas' => Yii::t('app', 'Se o país é parceiro da plataforma Alert-as'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstitutions()
    {
        return $this->hasMany(Institution::className(), ['country_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMesorregiaos()
    {
        return $this->hasMany(Mesorregiao::className(), ['country_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStates()
    {
        return $this->hasMany(State::className(), ['country_id' => 'id']);
    }
    
    
    static function getUfs($idCountry){
    	
    	$query = " SELECT ".
    			 " state.name as nome, ".
    			 " abbreviation as uf ".
    			 //" icon_path, ".
    			 //" country.name as pais ".
    			 " FROM state, country ".
    			 " WHERE ". 
    			 " state.country_id = country.id and ".
    			 " country_id = $idCountry order by nome";
       	
    	$res = \Yii::$app->db->createCommand ( $query )->queryAll();
    	
    	return $res;
    	
    }
    
    static function getCenterMapConfigurations($idCountry){
    	
    	$query = " SELECT ".
    			" center_latitude, center_longitude, zoom ".
    	" FROM country ".
    	" WHERE ".
    	" id = $idCountry";    	
    	
    	$res = \Yii::$app->db->createCommand ( $query )->queryOne();
    	
    	return $res;
    	
    }
    
    
    
}
