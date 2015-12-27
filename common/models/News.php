<?php

namespace common\models;

use Yii;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "news".
 *
 * @property integer $id
 * @property integer $be_top
 * @property string $title
 * @property string $content
 * @property string $created_at
 * @property string $updated_at
 * @property string $public_at
 */
class News extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'news';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'value' => new Expression('NOW()'),
            ],];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['be_top'], 'integer'],
            [['title', 'content'], 'required'],
            [['content'], 'string'],
            [['created_at', 'updated_at', 'public_at'], 'safe'],
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
            'be_top' => Yii::t('app', 'Be Top'),
            'title' => Yii::t('app', 'News Title'),
            'content' => Yii::t('app', 'Content'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'public_at' => Yii::t('app', 'Public At'),
        ];
    }
}
