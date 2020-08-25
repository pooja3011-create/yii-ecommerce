<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\User;

/**
 * UserSearch represents the model behind the search form about `app\models\User`.
 */
class UserSearch extends User {

    public $user_type;

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'billing_country', 'updated_by', 'user_role'], 'integer'],
            [['user_code', 'first_name', 'email', 'phone', 'gender', 'billing_city', 'billing_state', 'billing_date', 'created_at', 'status'], 'safe'],
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
        $query = User::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => array('pageSize' => 10),
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC
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
            'user_role' => $this->user_role,
        ]);

        $query->andFilterWhere(['like', 'user_code', $this->user_code])
                ->andFilterWhere(['=', 'status', $this->status])
                ->andFilterWhere(['like', 'first_name', $this->first_name]);

        $query->andFilterWhere(['=', 'DATE_FORMAT(DATE(created_at), "%d/%m/%Y")', $this->created_at]);

        $query->andFilterWhere(['!=', 'user_role', '1']);
        $query->andFilterWhere(['!=', 'user_role', '2']);


        return $dataProvider;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function userSearch($params) {

        $query1 = $query = (new \yii\db\Query())
                ->select("user.id, user.user_code as user_code, user.first_name as first_name, user.email as email, user.phone as phone, user.gender as gender, user.billing_address1 as billing_address1, user.billing_city as billing_city, user.billing_country as billing_country, user.created_at as created_at,'`registered`' as user_type")
                ->from('user')
                ->where(['user.user_role' => '2']);

        $query2 = $query = (new \yii\db\Query())
                ->select("guest_user.id, guest_user.user_code as user_code, guest_user.first_name as first_name, guest_user.email as email, guest_user.phone as phone, guest_user.gender as gender, guest_user.billing_address1 as billing_address1, guest_user.billing_city as billing_city, guest_user.billing_country as billing_country, guest_user.created_at as created_at,'`guest_user`' as user_type")
                ->from('guest_user');

        $query = (new \yii\db\Query())
                ->from(['customer' => $query1->union($query2)]);


        $pageSize = Yii::$app->params['list-pagination'];
        if (isset($_GET['per-page']) && $_GET['per-page'] != '') {
            $pageSize = $_GET['per-page'];
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => array('pageSize' => $pageSize),
            'sort' => ['defaultOrder'=>['user_code'=>SORT_DESC],'attributes' => ['user_code', 'first_name', 'email', 'phone', 'gender', 'created_at']]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere(['like', 'user_code', $this->user_code])
                ->andFilterWhere(['=', 'billing_country', $this->billing_country])
                ->andFilterWhere(['=', 'gender', $this->gender])
                ->andFilterWhere(['like', 'email', $this->email])
                ->andFilterWhere(['like', 'phone', $this->phone])
                ->andFilterWhere(['like', 'first_name', $this->first_name])
                ->andFilterWhere(['like', 'billing_city', $this->billing_city]);

//        $query->andFilterWhere(['=', 'DATE_FORMAT(DATE(created_at), "%d/%m/%Y")', $this->created_at]);
        if (isset($_GET['UserSearch']['created_at']) && $_GET['UserSearch']['created_at'] != '') {
            $date = date('Y-m-d', strtotime(str_replace('/', '-', $_GET['UserSearch']['created_at'])));
            $query->andWhere('DATEDIFF( DATE_FORMAT( created_at, "%Y-%m-%d" ) , "' . $date . '" ) >= 0 ');
        }
        if (isset($_GET['UserSearch']['user_type']) && $_GET['UserSearch']['user_type'] != '') {
            $query->andFilterWhere(['=', 'user_type', '`' . $_GET['UserSearch']['user_type'] . '`']);
        }

        $query->orderBy(['user_code' => SORT_ASC]);
        if (isset($_GET['sort']) && $_GET['sort'] != '') {
            if ($_GET['sort'] == 'user_code') {
                $query->orderBy(['user_code' => SORT_ASC]);
            } else if ($_GET['sort'] == '-user_code') {
                $query->orderBy(['user_code' => SORT_DESC]);
            } else if ($_GET['sort'] == 'first_name') {
                $query->orderBy(['first_name' => SORT_ASC]);
            } else if ($_GET['sort'] == '-first_name') {
                $query->orderBy(['first_name' => SORT_DESC]);
            } else if ($_GET['sort'] == 'email') {
                $query->orderBy(['email' => SORT_ASC]);
            } else if ($_GET['sort'] == '-email') {
                $query->orderBy(['email' => SORT_DESC]);
            } else if ($_GET['sort'] == 'phone') {
                $query->orderBy(['phone' => SORT_ASC]);
            } else if ($_GET['sort'] == '-phone') {
                $query->orderBy(['phone' => SORT_DESC]);
            } else if ($_GET['sort'] == 'gender') {
                $query->orderBy(['gender' => SORT_ASC]);
            } else if ($_GET['sort'] == '-gender') {
                $query->orderBy(['gender' => SORT_DESC]);
            } else if ($_GET['sort'] == 'created_at') {
                $query->orderBy(['created_at' => SORT_ASC]);
            } else if ($_GET['sort'] == '-created_at') {
                $query->orderBy(['created_at' => SORT_DESC]);
            }
        }


        return $dataProvider;
    }

}
