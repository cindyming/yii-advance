<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\StackTransaction;

/**
 * StackTransactionSearch represents the model behind the search form about `common\models\StackTransaction`.
 */
class StackTransactionSearch extends StackTransaction
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'stack_id', 'member_id', 'volume', 'type'], 'integer'],
            [['price', 'total_price', 'charge'], 'number'],
            [['created_at', 'updated_at', 'stackname', 'stackcode', 'membername', 'status'], 'safe'],
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
        $query = StackTransaction::find()
                 ->joinWith(['stack' => function($query) { $query->from(['stack' => 'stack']);}])
                 ->joinWith(['member' => function($query) { $query->from(['member' => 'member']);}])
                 ->orderBy(['created_at' => SORT_DESC]);

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
        if ($this->created_at) {
            $date = explode(' - ', $this->created_at);
            if (count($date)  == 2) {
                $query->andFilterWhere(['>=', $this::tableName() . '.created_at', $date[0]]);
                $query->andFilterWhere(['<=', $this::tableName() . '.created_at', $date[1]]);
            }
        }
        if ($this->updated_at) {
            $date = explode(' - ', $this->updated_at);
            if (count($date)  == 2) {
                $query->andFilterWhere(['>=', $this::tableName() . '.updated_at', $date[0]]);
                $query->andFilterWhere(['<=', $this::tableName() . '.updated_at', $date[1]]);
            }
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'stack_id' => $this->stack_id,
            'member_id' => $this->member_id,
            'volume' => $this->volume,
            'total_price' => $this->total_price,
            'status' => $this->status,
            'total' => $this->total,
        ])->andFilterWhere(['like','stack.code',$this->stackcode])
            ->andFilterWhere(['like','stack.name',$this->stackname])
            ->andFilterWhere(['like','member.username',$this->membername])
            ->orderBy(['created_at' => SORT_DESC]);

        return $dataProvider;
    }
}
