<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ProductVariation;

/**
 * ProductVariationSearch represents the model behind the search form about `app\models\ProductVariation`.
 */
class ProductVariationSearch extends ProductVariation
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'product_id', 'qty', 'updated_by'], 'integer'],
            [['color', 'size', 'display_currency', 'created_date', 'updated_date', 'status'], 'safe'],
            [['display_price'], 'number'],
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
        $query = ProductVariation::find();

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
            'product_id' => $this->product_id,
            'display_price' => $this->display_price,
            'qty' => $this->qty,
            'created_date' => $this->created_date,
            'updated_date' => $this->updated_date,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'color', $this->color])
            ->andFilterWhere(['like', 'size', $this->size])
            ->andFilterWhere(['like', 'display_currency', $this->display_currency])
            ->andFilterWhere(['like', 'status', $this->status]);

        return $dataProvider;
    }
}
