<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Vendors;

/**
 * VendorsSearch represents the model behind the search form about `app\models\Vendors`.
 */
class VendorsSearch extends Vendors {

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'country_id', 'updated_by'], 'integer'],
            [['address1', 'country_id', 'status', 'created_date','email','phone','vendor_code','name'], 'safe'],
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
        $query = Vendors::find();

        // add conditions that should always apply here
        $pageSize = Yii::$app->params['list-pagination'];
        if (isset($_GET['per-page']) && $_GET['per-page'] != '') {
            $pageSize = $_GET['per-page'];
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => array('pageSize' => $pageSize),
            'sort' => [
                'defaultOrder' => [
                    'vendor_code' => SORT_DESC
                ]
            ],
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
            'country_id' => $this->country_id,
            'updated_date' => $this->updated_date,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['=', 'vendor_code', $this->vendor_code])
                ->andFilterWhere(['like', 'name', $this->name])
                ->andFilterWhere(['like', 'email', $this->email])
                ->andFilterWhere(['like', 'phone', $this->phone])
                ->andFilterWhere(['like', 'status', $this->status]);

        
        if (isset($_GET['VendorsSearch']['searchAll']) && $_GET['VendorsSearch']['searchAll'] !='') {
            $query->andWhere('(vendor_code = "' . $_GET['VendorsSearch']['searchAll'] . '" OR name LIKE "%' . $_GET['VendorsSearch']['searchAll'] . '%" OR email LIKE "%' . $_GET['VendorsSearch']['searchAll'] . '%" OR phone LIKE "%' . $_GET['VendorsSearch']['searchAll'] . '%")');
        }
        if (isset($_GET['VendorsSearch']['address1']) && $_GET['VendorsSearch']['address1'] !='') {
            $_GET['VendorsSearch']['address1'] = str_replace(',', '', $_GET['VendorsSearch']['address1']);
            $query->andWhere('(address1 LIKE "%' . $_GET['VendorsSearch']['address1'] . '%" OR address2 LIKE "%' . $_GET['VendorsSearch']['address1'] . '%" OR city LIKE "%' . $_GET['VendorsSearch']['address1'] . '%" OR status LIKE "%' . $_GET['VendorsSearch']['address1'] . '%")');
        }
        
        if (isset($_GET['VendorsSearch']['created_date']) && $_GET['VendorsSearch']['created_date'] != '') {
            $date = date('Y-m-d',  strtotime(str_replace('/', '-', $_GET['VendorsSearch']['created_date'])));
            $query->andWhere('DATEDIFF( DATE_FORMAT( created_date, "%Y-%m-%d" ) , "' . $date . '" ) >= 0 ');
        }
        return $dataProvider;
    }
}
