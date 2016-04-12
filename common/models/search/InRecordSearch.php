<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\InRecord;

/**
 * InRecordSearch represents the model behind the search form about `common\models\InRecord`.
 */
class InRecordSearch extends InRecord
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'member_id', 'account_type'], 'integer'],
            [['amount', 'fee', 'total'], 'number'],
            [['note', 'created_at', 'updated_at', 'membername', 'type'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
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
    public function search($params)
    {
        $query = InRecord::find()
            ->joinWith(['member' => function($query) { $query->from(['member' => 'member']);}])
            ->orderBy(['id' => SORT_DESC]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);
        if (!Yii::$app->user->identity->isAdmin()) {
            $this->member_id = Yii::$app->user->identity->id;
        }

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'member_id' => $this->member_id,
            'account_type' => $this->account_type,
            'type' => $this->type,
            'amount' => $this->amount,
            'fee' => $this->fee,
            'total' => $this->total,
        ]);

        if ($this->created_at) {
            $date = explode(' - ', $this->created_at);
            if (count($date)  == 2) {
                $query->andFilterWhere(['>=', $this::tableName() . '.created_at', $date[0] . ' 00:00:00']);
                $query->andFilterWhere(['<=', $this::tableName() . '.created_at', $date[1] . ' 23:59:59']);
            }
        }

        $query->andFilterWhere(['like', 'note', $this->note])
            ->andFilterWhere(['like','member.username',$this->membername])
            ->orderBy(['id' => SORT_DESC]);

        return $dataProvider;
    }
}
