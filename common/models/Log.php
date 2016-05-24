<?php

namespace common\models;

use Yii;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "log".
 *
 * @property integer $id
 * @property string $role
 * @property string $action
 * @property string $result
 * @property string $note
 * @property string $created_at
 */
class Log extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'log';
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

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['role', 'action', 'result'], 'required'],
            [['created_at'], 'safe'],
            [['role', 'action', 'result'], 'string', 'max' => 100],
            [['note'], 'string', 'max' => 250]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'role' => Yii::t('app', 'Role'),
            'action' => Yii::t('app', 'Action'),
            'result' => Yii::t('app', 'Result'),
            'note' => Yii::t('app', 'Note'),
            'created_at' => Yii::t('app', 'Log Date'),
        ];
    }

    static function add($role, $action, $result= false, $note = '', $member_id = '')
    {
        $log = new Log();
        $log->role =  $role  . ($member_id ? $member_id : ( Yii::$app->user->identity ?  ' -'. Yii::$app->user->identity->username : ''));
        $log->action = $action;
        $log->result = ($result) ? $result : '成功';
        $log->note = ($note) ? $note : '';
        $log->save();
    }
}
