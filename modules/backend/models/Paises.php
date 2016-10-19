<?php

namespace app\modules\backend\models;

use Yii;

/**
 * This is the model class for table "paises".
 *
 * @property integer $gid
 * @property double $id
 * @property string $name
 * @property string $center_lat
 * @property string $center_lon
 * @property string $sigla
 * @property string $the_geom
 * @property string $the_geomsimples
 */
class Paises extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'paises';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'center_lat', 'center_lon'], 'number'],
            [['the_geom', 'the_geomsimples'], 'string'],
            [['name'], 'string', 'max' => 50],
            [['sigla'], 'string', 'max' => 254],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'gid' => Yii::t('app', 'Gid'),
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'center_lat' => Yii::t('app', 'Center Lat'),
            'center_lon' => Yii::t('app', 'Center Lon'),
            'sigla' => Yii::t('app', 'Sigla'),
            'the_geom' => Yii::t('app', 'The Geom'),
            'the_geomsimples' => Yii::t('app', 'The Geomsimples'),
        ];
    }
    
    static public function getWKT($idPais){
    		$q = "SELECT (g.gdump).path, ST_Astext((g.gdump).geom) as wkt FROM (SELECT ST_Dump( GeometryFromText(the_geomsimples, 3857) ) AS gdump FROM paises WHERE id=:id) AS g LIMIT 1";
    		$res = Yii::$app->db->createCommand ( $q )->bindValue(':id', $idPais)->queryOne ();
    		return $res ['wkt'];
    }
}
