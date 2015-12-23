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
            [['id', 'member_id', 'status'], 'integer'],
            [['bank', 'cardname', 'backaddress', 'cardnumber', 'created_at', 'updated_at', 'membername'], 'safe'],
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
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'cash.bank', $this->bank])
            ->andFilterWhere(['like', 'cash.cardname', $this->cardname])
            ->andFilterWhere(['like', 'cash.backaddress', $this->backaddress])
            ->andFilterWhere(['like','member.username',$this->membername])
            ->orderBy(['created_at' => SORT_DESC]);

        return $dataProvider;
    }
}
