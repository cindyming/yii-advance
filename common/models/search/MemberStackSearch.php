<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\MemberStack;

/**
 * MemberStackSearch represents the model behind the search form about `common\models\MemberStack`.
 */
class MemberStackSearch extends MemberStack
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'member_id', 'stack_id', 'sell_volume', 'lock_volume'], 'integer'],
            [['created_at', 'updated_at', 'membername', 'stackname', 'stackcode'], 'safe'],
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
        $query = MemberStack::find()
            ->joinWith(['member' => function($query) { $query->from(['member' => 'member']);}])
            ->joinWith(['stack' => function($query) { $query->from(['stack' => 'stack']);}])
            ->orderBy(['created_at' => SORT_DESC]);


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'member_id' => $this->member_id,
            'stack_id' => $this->stack_id,
            'sell_volume' => $this->sell_volume,
            'lock_volume' => $this->lock_volume,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ])->andFilterWhere(['=','member.usernmae',$this->membername])
            ->andFilterWhere(['=','stack.code',$this->stackcode])
            ->andFilterWhere(['=','stack.name',$this->stackname])
            ->orderBy(['created_at' => SORT_DESC]);

        return $dataProvider;
    }
}
