<?php

namespace common\models;

use Yii;
use yii\db\Expression;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;


/**
 * This is the model class for table "date".
 *
 * @property integer $id
 * @property string $date
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 */
class Date extends \yii\db\ActiveRecord
{
    public $start_date;
    public $end_date;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'date';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'value' => new Expression('NOW()'),
            ],
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'status',
                ],
                'value' => function ($event) {
                    $dw = date( "w", strtotime($this->date));
                    if (in_array($dw, array(0,6))){
                        return 1;
                    } else {
                        return 0;
                    }

                },
            ],
        ];
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['date'], 'required'],
            [['date', 'status', 'created_at', 'updated_at', 'start_date', 'end_date'], 'safe'],
            [['status'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'date' => Yii::t('app', 'Date'),
            'status' => Yii::t('app', 'Date Status'),
            'start_date' => Yii::t('app', 'Start Date'),
            'end_start' => Yii::t('app', 'End Date'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    public static function isWorkingDay()
    {
        $data = Date::find()->where(['=', 'date', new Expression('curdate()')])->one();
        return ($data && $data->status == 0) ? true : false;
    }

    public static function isWorkingTime()
    {
        $hours = date('H');
        if (((10 < $hours) && ($hours < 12)) || ((14 < $hours) && ($hours < 16))) {
            return true;
        } else {
            return false;
        }
    }
}
