<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\OrderProducts;

/**
 * OrderProductsSearch represents the model behind the search form about `app\models\OrderProducts`.
 */
class OrderProductsSearch extends OrderProducts {

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'order_id', 'vendor_id', 'product_id', 'updated_by'], 'integer'],
            [['price'], 'number'],
            [['qty', 'shipment_status', 'carrier', 'traking_number', 'shipped_date', 'shipment_from', 'shipment_to', 'shipment_note', 'created_date', 'updated_date'], 'safe'],
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
    public function search($params, $orderId = '') {
        $query = OrderProducts::find()
                ->where(['!=', 'traking_number', ''])
                ->groupBy(['traking_number']);

        $query1 = (new \yii\db\Query())
                ->select("id,shipment_from,shipment_to,carrier,traking_number,shipped_date,status as shipment_status,delivered_date,id as order_id")
                ->from('orders')
                ->where(['!=', 'traking_number', ''])
                ->andWhere(['id' => $orderId]);

        $query2 = $query = (new \yii\db\Query())
                ->select("id,shipment_from,shipment_to,carrier,traking_number,shipped_date,shipment_status,delivered_date,order_id")
                ->from('order_products')
                ->where(['!=', 'traking_number', ''])
                ->andWhere(['order_id' => $orderId])
                ->groupBy(['traking_number']);

        $query = (new \yii\db\Query())
                ->from(['shipment' => $query1->union($query2)]);
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
            'order_id' => $this->order_id,
            'vendor_id' => $this->vendor_id,
            'product_id' => $this->product_id,
            'price' => $this->price,
            'shipped_date' => $this->shipped_date,
            'created_date' => $this->created_date,
            'updated_date' => $this->updated_date,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'qty', $this->qty])
                ->andFilterWhere(['like', 'shipment_status', $this->shipment_status])
                ->andFilterWhere(['like', 'carrier', $this->carrier])
                ->andFilterWhere(['like', 'traking_number', $this->traking_number])
                ->andFilterWhere(['like', 'shipment_from', $this->shipment_from])
                ->andFilterWhere(['like', 'shipment_to', $this->shipment_to])
                ->andFilterWhere(['like', 'shipment_note', $this->shipment_note]);

//        if ($orderId != '') {
//            $query->andFilterWhere(['=', 'order_id', $orderId]);
//        }

        return $dataProvider;
    }

}
