<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Orders;

/**
 * OrdersSearch represents the model behind the search form about `app\models\Orders`.
 */
class OrdersSearch extends Orders {

    public $userName;
    public $userPhone;
    public $vendorName;
    public $paymentStatus;
    public $paymentDate;
    public $refNumber;
    public $orderProducts;
    public $grandTotal;
    public $vendorCommission;
    public $vendorPayment;
    public $orderId;
    public $first_name;
    public $phone;

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['user_id', 'billing_country', 'shipping_country', 'updated_by'], 'integer'],
            [['id', 'order_date', 'actual_delivery_date', 'estimate_delivery_date', 'status', 'updated_date', 'userName', 'vendorName', 'paymentStatus', 'paymentDate', 'orderId', 'refNumber', 'guest_checkout', 'userPhone', 'invoice_date','first_name','phone','invoice_id'], 'safe'],
            [['amount'], 'number'],
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
    public function search($params, $vendorID = "", $orderStatus = '', $userId = '') {
        $query = Orders::find()
                ->groupBy(['orders.id']);

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
        $query->joinWith('orderProducts');
        $query->joinWith('user');
        if (!empty($vendorID)) {
            $query->andFilterWhere(['=', 'order_products.vendor_id', $vendorID]);
        }
        if (!empty($userId)) {
            $query->andFilterWhere(['=', 'orders.user_id', $userId]);
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'orders.id' => $this->id,
            'orders.user_id' => $this->user_id,
            'orders.amount' => $this->amount,
            'orders.updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'orders.status', $this->status]);
        $query->andFilterWhere(['like', 'orders.guest_checkout', $this->guest_checkout]);


        if (!empty($orderStatus)) {
            $query->andFilterWhere(['=', 'orders.status', $orderStatus]);
        }

        if (isset($_GET['OrdersSearch']['searchAll']) && $_GET['OrdersSearch']['searchAll'] != '') {
            $query->andWhere('orders.id = "' . $_GET['OrdersSearch']['searchAll'] . '"');
        }
        if (isset($_GET['OrdersSearch']['userPhone']) && $_GET['OrdersSearch']['userPhone'] != '') {
            $query->andWhere('user.phone like "%' . $_GET['OrdersSearch']['userPhone'] . '%"');
        }
        if (isset($_GET['OrdersSearch']['userName']) && $_GET['OrdersSearch']['userName'] != '') {
            $query->andWhere('(user.first_name like "%' . $_GET['OrdersSearch']['userName'] . '%" or user.last_name like "%' . $_GET['OrdersSearch']['userName'] . '%")');
        }
       

        if (isset($_GET['OrdersSearch']['order_date']) && $_GET['OrdersSearch']['order_date'] != '') {
            $query->andWhere('DATE_FORMAT(DATE(orders.order_date), "%d/%m/%Y") = "' . $_GET['OrdersSearch']['order_date'] . '"');
        }

        if (isset($_GET['OrdersSearch']['estimate_delivery_date']) && $_GET['OrdersSearch']['estimate_delivery_date'] != '') {
            $query->andWhere('DATE_FORMAT(DATE(orders.estimate_delivery_date), "%d/%m/%Y") = "' . $_GET['OrdersSearch']['estimate_delivery_date'] . '"');
        }

        if (isset($_GET['OrdersSearch']['updated_date']) && $_GET['OrdersSearch']['updated_date'] != '') {
            $query->andWhere('DATE_FORMAT(DATE(orders.updated_date), "%d/%m/%Y") = "' . $_GET['OrdersSearch']['updated_date'] . '"');
        }

        return $dataProvider;
    }

    public function paymentSearch($params, $orderStatus = '') {
        $query = (new \yii\db\Query())
                ->select("orders.id as order_id,orders.order_date,orders.actual_delivery_date,vendors.name as vendor_name,vendors.shop_name as shop_name,vendors.vendor_code as vendor_code,vendors.commission_rate,order_products.price as total,vendor_payment.payment_date as payment_date,vendor_payment.payment_ref_number as payment_ref_number,sum(order_products.price) as order_sum,sum(order_products.vendor_commission) as comission,sum(order_products.vendor_payment) as vendor_payment, order_products.vendor_id as payment_vendor,vendors.commission_type ")
                ->from('orders ')
                ->join('LEFT JOIN', 'order_products', 'order_products.order_id = orders.id')
                ->join('LEFT JOIN', 'vendors', ' vendors.id = order_products.vendor_id')
                ->join('LEFT JOIN', 'vendor_payment', 'vendor_payment.order_id = orders.id AND vendor_payment.vendor_id = vendors.id')
                ->where(['order_products.shipment_status'=>'1'])
                ->groupBy(['orders.id', 'order_products.vendor_id']);

        // add conditions that should always apply here

        $pageSize = Yii::$app->params['list-pagination'];
        if (isset($_GET['per-page']) && $_GET['per-page'] != '') {
            $pageSize = $_GET['per-page'];
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => array('pageSize' => $pageSize),
//            'sort' => ['defaultOrder'=>['id'=>SORT_DESC],'attributes' => ['id', 'first_name', 'phone', 'gender', 'order_date','estimate_delivery_date','updated_date','guest_checkout','status']]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if (isset($_GET['OrdersSearch']['searchOrder']) && trim($_GET['OrdersSearch']['searchOrder']) != '') {
            $query->andWhere('(orders.id= "' . trim($_GET['OrdersSearch']['searchOrder']) . '" OR vendor_payment.payment_ref_number= "' . trim($_GET['OrdersSearch']['searchOrder']) . '")');
        }
        if (isset($_GET['OrdersSearch']['order_date']) && trim($_GET['OrdersSearch']['order_date']) != '') {
            $query->andWhere('DATE_FORMAT(DATE(orders.order_date), "%d/%m/%Y") = "' . trim($_GET['OrdersSearch']['order_date']) . '"');
        }
        if (isset($_GET['OrdersSearch']['actual_delivery_date']) && trim($_GET['OrdersSearch']['actual_delivery_date']) != '') {
            $query->andWhere('DATE_FORMAT(DATE(orders.actual_delivery_date), "%d/%m/%Y") = "' . trim($_GET['OrdersSearch']['actual_delivery_date']) . '"');
        }
        if (isset($_GET['OrdersSearch']['orderId']) && trim($_GET['OrdersSearch']['orderId']) != '') {
            $query->andWhere('orders.id = "' . trim($_GET['OrdersSearch']['orderId']) . '"');
        }
        if (isset($_GET['OrdersSearch']['vendorName']) && trim($_GET['OrdersSearch']['vendorName']) != '') {
            $query->andWhere('(vendors.shop_name LIKE "%' . trim($_GET['OrdersSearch']['vendorName']) . '%" OR vendors.vendor_code LIKE "%' . trim($_GET['OrdersSearch']['vendorName']) . '%")');
        }
        if (isset($_GET['OrdersSearch']['vendorCommission']) && trim($_GET['OrdersSearch']['vendorCommission']) != '') {
            $query->andWhere('vendors.commission_rate = "' . trim($_GET['OrdersSearch']['vendorCommission']) . '"');
        }
        if (isset($_GET['OrdersSearch']['paymentStatus']) && trim($_GET['OrdersSearch']['paymentStatus']) != '') {
            if (trim($_GET['OrdersSearch']['paymentStatus']) == 'paid') {
                $query->andWhere('vendor_payment.payment_ref_number != ""');
            }
        }
        if (isset($_GET['OrdersSearch']['paymentDate']) && trim($_GET['OrdersSearch']['paymentDate']) != '') {
            $query->andWhere('DATE_FORMAT(DATE(vendor_payment.payment_date), "%d/%m/%Y") = "' . trim($_GET['OrdersSearch']['paymentDate']) . '"');
        }

        if (!empty($orderStatus)) {
            $query->andFilterWhere(['=', 'orders.status', $orderStatus]);
        }

        return $dataProvider;
    }

    public function orderSearch($params) {
//        $query = Orders::find()
//                ->groupBy(['orders.id']);

        $query1 = $query = (new \yii\db\Query())
                ->select("orders.*,user.first_name,user.phone")
                ->from('orders')
                ->join('LEFT JOIN', 'user', ' user.id = orders.user_id')
                ->where(['user.user_role' => '2']);

        $query2 = $query = (new \yii\db\Query())
                ->select("orders.*,guest_user.first_name,guest_user.phone")
                 ->join('LEFT JOIN', 'guest_user', ' guest_user.id = orders.user_id')
                ->from('orders');

        $query = (new \yii\db\Query())
                ->from(['customer' => $query1->union($query2)])
                ->groupBy(['id']);
        // add conditions that should always apply here

        $pageSize = Yii::$app->params['list-pagination'];
        if (isset($_GET['per-page']) && $_GET['per-page'] != '') {
            $pageSize = $_GET['per-page'];
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => array('pageSize' => $pageSize),
            'sort' => ['defaultOrder'=>['id'=>SORT_DESC],'attributes' => ['id', 'first_name', 'phone', 'gender', 'order_date','amount','estimate_delivery_date','updated_date','guest_checkout','status','invoice_id','invoice_date']]
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
            'user_id' => $this->user_id,
            'amount' => $this->amount,
            'invoice_id' => $this->invoice_id,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'status', $this->status]);
        $query->andFilterWhere(['like', 'guest_checkout', $this->guest_checkout]);
        if (isset($_GET['OrdersSearch']['userName']) && $_GET['OrdersSearch']['userName'] != '') {
            $query->andWhere('(first_name like "%' . $_GET['OrdersSearch']['userName'] . '%" or last_name like "%' . $_GET['OrdersSearch']['userName'] . '%")');
        }
         if (isset($_GET['OrdersSearch']['first_name']) && $_GET['OrdersSearch']['first_name'] != '') {
            $query->andWhere('first_name like "%' . $_GET['OrdersSearch']['first_name'] . '%"');
        }
        if (isset($_GET['OrdersSearch']['userPhone']) && $_GET['OrdersSearch']['userPhone'] != '') {
            $query->andWhere('phone like "%' . $_GET['OrdersSearch']['userPhone'] . '" ');
        }
        if (isset($_GET['OrdersSearch']['phone']) && $_GET['OrdersSearch']['phone'] != '') {
            $query->andWhere('phone like "%' . $_GET['OrdersSearch']['phone'] . '" ');
        }

        if (isset($_GET['OrdersSearch']['invoice_date']) && $_GET['OrdersSearch']['invoice_date'] != '') {
            $query->andWhere('DATE_FORMAT(DATE(invoice_date), "%d/%m/%Y") = "' . $_GET['OrdersSearch']['invoice_date'] . '"');
        }
        if (isset($_GET['OrdersSearch']['order_date']) && $_GET['OrdersSearch']['order_date'] != '') {
            $query->andWhere('DATE_FORMAT(DATE(order_date), "%d/%m/%Y") = "' . $_GET['OrdersSearch']['order_date'] . '"');
        }

        if (isset($_GET['OrdersSearch']['estimate_delivery_date']) && $_GET['OrdersSearch']['estimate_delivery_date'] != '') {
            $query->andWhere('DATE_FORMAT(DATE(estimate_delivery_date), "%d/%m/%Y") = "' . $_GET['OrdersSearch']['estimate_delivery_date'] . '"');
        }

        if (isset($_GET['OrdersSearch']['updated_date']) && $_GET['OrdersSearch']['updated_date'] != '') {
            $query->andWhere('DATE_FORMAT(DATE(updated_date), "%d/%m/%Y") = "' . $_GET['OrdersSearch']['updated_date'] . '"');
        }
         $query->orderBy(['id' => SORT_DESC]);
        if (isset($_GET['sort']) && $_GET['sort'] != '') {
            if ($_GET['sort'] == 'id') {
                $query->orderBy(['id' => SORT_ASC]);
            } else if ($_GET['sort'] == '-id') {
                $query->orderBy(['id' => SORT_DESC]);
            } else if ($_GET['sort'] == 'first_name') {
                $query->orderBy(['first_name' => SORT_ASC]);
            } else if ($_GET['sort'] == '-first_name') {
                $query->orderBy(['first_name' => SORT_DESC]);
            } else if ($_GET['sort'] == 'order_date') {
                $query->orderBy(['order_date' => SORT_ASC]);
            } else if ($_GET['sort'] == '-order_date') {
                $query->orderBy(['order_date' => SORT_DESC]);
            } else if ($_GET['sort'] == 'order_date') {
                $query->orderBy(['phone' => SORT_ASC]);
            } else if ($_GET['sort'] == '-phone') {
                $query->orderBy(['phone' => SORT_DESC]);
            } else if ($_GET['sort'] == 'gender') {
                $query->orderBy(['gender' => SORT_ASC]);
            } else if ($_GET['sort'] == '-gender') {
                $query->orderBy(['gender' => SORT_DESC]);
            } else if ($_GET['sort'] == 'amount') {
                $query->orderBy(['amount' => SORT_ASC]);
            } else if ($_GET['sort'] == '-amount') {
                $query->orderBy(['amount' => SORT_DESC]);
            }else if ($_GET['sort'] == 'estimate_delivery_date') {
                $query->orderBy(['estimate_delivery_date' => SORT_ASC]);
            } else if ($_GET['sort'] == '-estimate_delivery_date') {
                $query->orderBy(['estimate_delivery_date' => SORT_DESC]);
            }else if ($_GET['sort'] == 'updated_date') {
                $query->orderBy(['updated_date' => SORT_ASC]);
            } else if ($_GET['sort'] == '-updated_date') {
                $query->orderBy(['updated_date' => SORT_DESC]);
            }else if ($_GET['sort'] == 'guest_checkout') {
                $query->orderBy(['guest_checkout' => SORT_ASC]);
            } else if ($_GET['sort'] == '-guest_checkout') {
                $query->orderBy(['guest_checkout' => SORT_DESC]);
            }else if ($_GET['sort'] == 'status') {
                $query->orderBy(['status' => SORT_ASC]);
            } else if ($_GET['sort'] == '-status') {
                $query->orderBy(['i' => SORT_DESC]);
            }else if ($_GET['sort'] == 'status') {
                $query->orderBy(['status' => SORT_ASC]);
            } else if ($_GET['sort'] == '-invoice_id') {
                $query->orderBy(['invoice_id' => SORT_DESC]);
            }else if ($_GET['sort'] == 'invoice_id') {
                $query->orderBy(['invoice_id' => SORT_ASC]);
            } else if ($_GET['sort'] == '-invoice_date') {
                $query->orderBy(['invoice_date' => SORT_DESC]);
            }else if ($_GET['sort'] == '-invoice_date') {
                $query->orderBy(['invoice_date' => SORT_DESC]);
            }
        }
        return $dataProvider;
    }

    public function invoiceSearch($params) {


        $query1 = $query = (new \yii\db\Query())
                ->select("orders.*,user.first_name,user.phone")
                ->from('orders')
                ->join('LEFT JOIN', 'user', ' user.id = orders.user_id')
                ->where(['user.user_role' => '2']);

        $query2 = $query = (new \yii\db\Query())
                ->select("orders.*,guest_user.first_name,guest_user.phone")
                 ->join('LEFT JOIN', 'guest_user', ' guest_user.id = orders.user_id')
                ->from('orders');

        $query = (new \yii\db\Query())
                ->from(['customer' => $query1->union($query2)])
                ->where(' invoice_id != "" and invoice_id IS NOT NULL')
                ->groupBy(['id']);
        // add conditions that should always apply here

        $pageSize = Yii::$app->params['list-pagination'];
        if (isset($_GET['per-page']) && $_GET['per-page'] != '') {
            $pageSize = $_GET['per-page'];
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => array('pageSize' => $pageSize),
            'sort' => ['defaultOrder'=>['invoice_id'=>SORT_DESC],'attributes' => ['id', 'first_name', 'phone', 'gender', 'order_date','amount','estimate_delivery_date','updated_date','guest_checkout','status','invoice_id','invoice_date']]
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
            'user_id' => $this->user_id,
            'amount' => $this->amount,
            'invoice_id' => $this->invoice_id,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'status', $this->status]);
        $query->andFilterWhere(['like', 'guest_checkout', $this->guest_checkout]);
        if (isset($_GET['OrdersSearch']['userName']) && $_GET['OrdersSearch']['userName'] != '') {
            $query->andWhere('(first_name like "%' . $_GET['OrdersSearch']['userName'] . '%" or last_name like "%' . $_GET['OrdersSearch']['userName'] . '%")');
        }
         if (isset($_GET['OrdersSearch']['first_name']) && $_GET['OrdersSearch']['first_name'] != '') {
            $query->andWhere('first_name like "%' . $_GET['OrdersSearch']['first_name'] . '%"');
        }
        if (isset($_GET['OrdersSearch']['userPhone']) && $_GET['OrdersSearch']['userPhone'] != '') {
            $query->andWhere('phone like "%' . $_GET['OrdersSearch']['userPhone'] . '" ');
        }
        if (isset($_GET['OrdersSearch']['phone']) && $_GET['OrdersSearch']['phone'] != '') {
            $query->andWhere('phone like "%' . $_GET['OrdersSearch']['phone'] . '" ');
        }

        if (isset($_GET['OrdersSearch']['invoice_date']) && $_GET['OrdersSearch']['invoice_date'] != '') {
            $query->andWhere('DATE_FORMAT(DATE(invoice_date), "%d/%m/%Y") = "' . $_GET['OrdersSearch']['invoice_date'] . '"');
        }
        if (isset($_GET['OrdersSearch']['order_date']) && $_GET['OrdersSearch']['order_date'] != '') {
            $query->andWhere('DATE_FORMAT(DATE(order_date), "%d/%m/%Y") = "' . $_GET['OrdersSearch']['order_date'] . '"');
        }

        if (isset($_GET['OrdersSearch']['estimate_delivery_date']) && $_GET['OrdersSearch']['estimate_delivery_date'] != '') {
            $query->andWhere('DATE_FORMAT(DATE(estimate_delivery_date), "%d/%m/%Y") = "' . $_GET['OrdersSearch']['estimate_delivery_date'] . '"');
        }

        if (isset($_GET['OrdersSearch']['updated_date']) && $_GET['OrdersSearch']['updated_date'] != '') {
            $query->andWhere('DATE_FORMAT(DATE(updated_date), "%d/%m/%Y") = "' . $_GET['OrdersSearch']['updated_date'] . '"');
        }
         $query->orderBy(['id' => SORT_DESC]);
        if (isset($_GET['sort']) && $_GET['sort'] != '') {
            if ($_GET['sort'] == 'id') {
                $query->orderBy(['id' => SORT_ASC]);
            } else if ($_GET['sort'] == '-id') {
                $query->orderBy(['id' => SORT_DESC]);
            } else if ($_GET['sort'] == 'first_name') {
                $query->orderBy(['first_name' => SORT_ASC]);
            } else if ($_GET['sort'] == '-first_name') {
                $query->orderBy(['first_name' => SORT_DESC]);
            } else if ($_GET['sort'] == 'order_date') {
                $query->orderBy(['order_date' => SORT_ASC]);
            } else if ($_GET['sort'] == '-order_date') {
                $query->orderBy(['order_date' => SORT_DESC]);
            } else if ($_GET['sort'] == 'order_date') {
                $query->orderBy(['phone' => SORT_ASC]);
            } else if ($_GET['sort'] == '-phone') {
                $query->orderBy(['phone' => SORT_DESC]);
            } else if ($_GET['sort'] == 'gender') {
                $query->orderBy(['gender' => SORT_ASC]);
            } else if ($_GET['sort'] == '-gender') {
                $query->orderBy(['gender' => SORT_DESC]);
            } else if ($_GET['sort'] == 'amount') {
                $query->orderBy(['amount' => SORT_ASC]);
            } else if ($_GET['sort'] == '-amount') {
                $query->orderBy(['amount' => SORT_DESC]);
            }else if ($_GET['sort'] == 'estimate_delivery_date') {
                $query->orderBy(['estimate_delivery_date' => SORT_ASC]);
            } else if ($_GET['sort'] == '-estimate_delivery_date') {
                $query->orderBy(['estimate_delivery_date' => SORT_DESC]);
            }else if ($_GET['sort'] == 'updated_date') {
                $query->orderBy(['updated_date' => SORT_ASC]);
            } else if ($_GET['sort'] == '-updated_date') {
                $query->orderBy(['updated_date' => SORT_DESC]);
            }else if ($_GET['sort'] == 'guest_checkout') {
                $query->orderBy(['guest_checkout' => SORT_ASC]);
            } else if ($_GET['sort'] == '-guest_checkout') {
                $query->orderBy(['guest_checkout' => SORT_DESC]);
            }else if ($_GET['sort'] == 'status') {
                $query->orderBy(['status' => SORT_ASC]);
            } else if ($_GET['sort'] == '-status') {
                $query->orderBy(['i' => SORT_DESC]);
            }else if ($_GET['sort'] == 'status') {
                $query->orderBy(['status' => SORT_ASC]);
            } else if ($_GET['sort'] == '-invoice_id') {
                $query->orderBy(['invoice_id' => SORT_DESC]);
            }else if ($_GET['sort'] == 'invoice_id') {
                $query->orderBy(['invoice_id' => SORT_ASC]);
            } else if ($_GET['sort'] == '-invoice_date') {
                $query->orderBy(['invoice_date' => SORT_DESC]);
            }else if ($_GET['sort'] == '-invoice_date') {
                $query->orderBy(['invoice_date' => SORT_DESC]);
            }
        }
        return $dataProvider;
    }
}
