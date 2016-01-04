<?php

namespace common\models;

use Yii;
use yii\db\Expression;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "message".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $title
 * @property string $content
 * @property string $replied_content
 * @property integer $type
 * @property string $created_at
 * @property string $updated_at
 *
 * @property User $user
 */
class Message extends ActiveRecord
{
    public $membername;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'message';
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
                    ActiveRecord::EVENT_BEFORE_INSERT => 'member_id',
                ],
                'value' => function ($event) {
                    return Yii::$app->user->identity->id;
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
            [[ 'title', 'content', 'type'], 'required'],
            [['member_id', 'type'], 'integer'],
            [['content', 'replied_content'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['title'], 'string', 'max' => 250]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', 'Messaged by'),
            'title' => Yii::t('app', 'Title'),
            'content' => Yii::t('app', 'Content'),
            'replied_content' => Yii::t('app', 'Replied Content'),
            'type' => Yii::t('app', 'Type'),
            'created_at' => Yii::t('app', 'Leave At'),
            'updated_at' => Yii::t('app', 'Reply At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMember()
    {
        return $this->hasOne(Member::className(), ['id' => 'member_id']);
    }

    public function isReplied()
    {
        return ($this->replied_content) ? '已回复' : '未回复';
    }
}
