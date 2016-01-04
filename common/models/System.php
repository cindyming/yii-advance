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
    public $show_add_member;
    public $sell_fee_rate;
    public $annual_fee;
    public $transaction_rule;

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
            'enable_memmber_login' => Yii::t('app', 'Enable Member Login'),
            'show_add_member' => Yii::t('app', 'Show Add Member'),
            'lowest_cash_amount' => Yii::t('app', 'Lowest Cash Amount'),
            'cash_factorage' => Yii::t('app', 'Cash Factorage'),
            'sell_fee_rate' => Yii::t('app', 'Exchange Fee Rate'),
            'annual_fee' => Yii::t('app', 'Annual Fee'),
            'username' =>  Yii::t('app', 'User Name'),
            'transaction_rule' =>  Yii::t('app', 'Transaction Rule'),
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
