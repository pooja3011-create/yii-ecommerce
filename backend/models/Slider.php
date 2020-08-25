<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "slider".
 *
 * @property integer $id
 * @property string $title
 * @property string $image
 * @property integer $sort_order
 * @property string $link
 * @property string $status
 * @property string $created_date
 * @property string $updated_date
 */
class Slider extends \yii\db\ActiveRecord {

    public $imageFiles;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'slider';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['title'], 'required'],
            [['sort_order'], 'integer'],
            [['status'], 'string'],
            [['title', 'image', 'link'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'image' => 'Image',
            'sort_order' => 'Sort Order',
            'link' => 'Link',
            'status' => 'Status',
            'created_date' => 'Created Date',
            'updated_date' => 'Updated Date',
        ];
    }

    /**
     * save slider 
     * @return integer slider id 
     * * */
    function saveSlider() {
        $saveArr['title'] = isset($_POST['Slider']['title']) ? $_POST['Slider']['title'] : '';
        $saveArr['link'] = isset($_POST['Slider']['link']) ? $_POST['Slider']['link'] : '';

        $saveArr['status'] = isset($_POST['Slider']['status']) ? $_POST['Slider']['status'] : '';
        $saveArr['created_date'] = date('Y:m:d H:i:s');
        $saveArr['updated_date'] = date('Y:m:d H:i:s');
        $data = (new \yii\db\Query())
                ->select(['sort_order'])
                ->from('slider')
                ->orderBy(['sort_order' => SORT_DESC])
                ->one();
        $saveArr['sort_order'] = 0;
        if(isset($data['sort_order']) && $data['sort_order'] != ''){
            $saveArr['sort_order'] = $data['sort_order'] + 1;
        }
        Yii::$app->db->createCommand()->insert('slider', $saveArr)->execute();
        $sliderID = Yii::$app->db->getLastInsertID();
        Yii::$app->session->setFlash('success', Yii::$app->params['saveSlide']);
        return $sliderID;
    }

    /**
     * get slider detail
     * @id slider id
     * @return mixed  slider detail
     * * */
    function sliderDetail($id) {
        $data = (new \yii\db\Query())
                ->select(['id', 'title', 'image', 'link', 'sort_order', 'status'])
                ->from('slider')
                ->where(['id' => $id])
                ->one();
        return $data;
    }

    /** update slider detail
     * @id int slider id
     *      * * */
    function editSlider($id) {

        $saveArr['title'] = isset($_POST['Slider']['title']) ? $_POST['Slider']['title'] : '';
        $saveArr['link'] = isset($_POST['Slider']['link']) ? $_POST['Slider']['link'] : '';
        $saveArr['status'] = isset($_POST['Slider']['status']) ? $_POST['Slider']['status'] : '';
        $saveArr['updated_date'] = date('Y:m:d H:i:s');

        Yii::$app->db->createCommand()->update('slider', $saveArr, ['id' => $id])->execute();

        Yii::$app->session->setFlash('success', Yii::$app->params['editSlide']);
        return TRUE;
    }

    /**
     * get list of slides
     * @return  mixed values 
     * * */
    function sliderList() {
        $data = (new \yii\db\Query())
                ->select(['id', 'title', 'image', 'link', 'sort_order', 'status'])
                ->from('slider')
                ->orderBy(['sort_order' => SORT_ASC])
                ->all();
        return $data;
    }

    /** set slides order */
    function sliderSort($json) {
        $list = json_decode($json);

        foreach ($list as $key => $val) {
            Yii::$app->db->createCommand()->update('slider', ['sort_order' => $key], ['id' => $val])->execute();
        }
        return true;
    }

}
