<?php

namespace app\modules\backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Instituicao;

/**
 * InstituicaoSearch represents the model behind the search form about `app\models\Instituicao`.
 */
class InstituicaoSearch extends Instituicao
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'version'], 'integer'],
            [['email', 'fone', 'nome', 'pais', 'sigla', 'siglacap', 'sendercap', 'contatocap', 'languagecap'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Instituicao::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'version' => $this->version,
        ]);

        $query->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'fone', $this->fone])
            ->andFilterWhere(['like', 'nome', $this->nome])
            ->andFilterWhere(['like', 'pais', $this->pais])
            ->andFilterWhere(['like', 'sigla', $this->sigla])
            ->andFilterWhere(['like', 'siglacap', $this->siglacap])
            ->andFilterWhere(['like', 'sendercap', $this->sendercap])
            ->andFilterWhere(['like', 'contatocap', $this->contatocap])
            ->andFilterWhere(['like', 'languagecap', $this->languagecap]);

        return $dataProvider;
    }
}
