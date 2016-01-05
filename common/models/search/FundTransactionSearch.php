<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\FundTransaction;

/**
 * FundTransactionSearch represents the model behind the search form about `common\models\FundTransaction`.
 */
class FundTransactionSearch extends FundTransaction
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'fund_id', 'member_id', 'locked', 'cleared'], 'integer'],
            [['investment', 'revenue'], 'number'],
            [['created_at', 'cleared_at', 'membername'], 'safe'],
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
        $query = FundTransaction::find()
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
                $query->andFilterWhere(['>=', $this::tableName() . '.created_at', $date[0] . ' 00:00:00']);
                $query->andFilterWhere(['<=', $this::tableName() . '.created_at', $date[1] . ' 23:59:59']);
            }
        }

        if ($this->cleared_at) {
            $date = explode(' - ', $this->cleared_at);
            if (count($date)  == 2) {
                $query->andFilterWhere(['>=', $this::tableName() . '.cleared_at', $date[0] . ' 00:00:00']);
                $query->andFilterWhere(['<=', $this::tableName() . '.cleared_at', $date[1] . ' 23:59:59']);
            }
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'fund_id' => $this->fund_id,
            'member_id' => $this->member_id,
            'investment' => $this->investment,
            'revenue' => $this->revenue,
            'fund_transaction.locked' => $this->locked,
            'cleared' => $this->cleared,
        ])->andFilterWhere(['like','member.username',$this->membername])
        ->orderBy(['created_at' => SORT_DESC]);

        return $dataProvider;
    }
}
