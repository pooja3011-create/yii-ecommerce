<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Collection;

/**
 * CollectionSearch represents the model behind the search form about `app\models\Collection`.
 */
class CollectionSearch extends Collection {

    public $product_count;

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id'], 'integer'],
            [['title', 'image', 'created_date', 'updated_date', 'updated_by', 'status'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios() {
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
    public function search($params) {
        $query = Collection::find();
        $query = (new \yii\db\Query())
                ->select("collection.*, count(collection_product.product_id) as product_count")
                ->from('collection ')
                ->join('LEFT JOIN', 'collection_product', 'collection_product.collection_id = collection.id')
                ->groupBy(['collection.id']);

        // add conditions that should always apply here
        $pageSize = Yii::$app->params['list-pagination'];
        if (isset($_GET['per-page']) && $_GET['per-page'] != '') {
            $pageSize = $_GET['per-page'];
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => array('pageSize' => $pageSize),
            'sort' => ['attributes' => ['title','product_count','status']]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'collection.id' => $this->id,
        ]);

        $query->andFilterWhere(['like', 'collection.title', $this->title])
                ->andFilterWhere(['like', 'collection.status', $this->status]);


        return $dataProvider;
    }

}
