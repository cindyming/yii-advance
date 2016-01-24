<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Cash;

/**
 * CashSearch represents the model behind the search form about `common\models\Cash`.
 */
class CashSearch extends Cash
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'member_id'], 'integer'],
            [['bank', 'cardname', 'backaddress', 'cardnumber', 'created_at', 'updated_at', 'membername', 'status'], 'safe'],
            [['amount', 'fee', 'real_amount'], 'number'],
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
        $query = Cash::find()
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

        $query->andFilterWhere([
            'id' => $this->id,
            'member_id' => $this->member_id,
            'amount' => $this->amount,
            'fee' => $this->fee,
            'real_amount' => $this->real_amount,
            'cash.status' => $this->status,
        ]);

        if ($this->created_at) {
            $date = explode(' - ', $this->created_at);
            if (count($date)  == 2) {
                $query->andFilterWhere(['>=', $this::tableName() . '.created_at', $date[0] . ' 00:00:00']);
                $query->andFilterWhere(['<=', $this::tableName() . '.created_at', $date[1] . ' 23:59:59']);
            }
        }

        $query->andFilterWhere(['like', 'cash.bank', $this->bank])
            ->andFilterWhere(['like', 'cash.cardname', $this->cardname])
            ->andFilterWhere(['like', 'cash.backaddress', $this->backaddress])
            ->andFilterWhere(['like', 'cash.cardnumber', $this->cardnumbert])
            ->andFilterWhere(['like','member.username',$this->membername])
            ->orderBy(['created_at' => SORT_DESC]);

        return $dataProvider;
    }
}
