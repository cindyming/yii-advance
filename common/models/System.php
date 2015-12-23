<?php

namespace common\models;

use yii;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;

class System extends ActiveRecord
{
    public $enable_memmber_login;
    public $lowest_cash_amount;
    public $cash_factorage;
    public $stop_banus_times;
    public $open_baodan_tree;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'system';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'enable_memmber_login' => '会员登录功能',
            'stop_banus_times' => '分红封顶倍数',
            'lowest_cash_amount' => '最低提现额',
            'cash_factorage' => '绩效提现手续费',
            'open_baodan_tree' => '报单员网络图'
            ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'value'], 'required'],
        ];
    }

    static function loadConfig($name = '')
    {

        $configs = unserialize(Yii::$app->cache->get('SYSTEM_CONFIG'));
        if (!count($configs) || true) {
            $configs = array();
            $values = System::find()->all();
            foreach ($values as $val) {
                $configs[$val['name']] = $val['value'];
            }
            Yii::$app->cache->set('SYSTEM_CONFIG', serialize($configs));
        }
        return ($name) ? (isset($configs[$name]) ? $configs[$name] : '') : $configs;
    }
}
