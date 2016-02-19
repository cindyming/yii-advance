<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\StackAuthorize;

/**
 * StackAuthorizeSearch represents the model behind the search form about `common\models\StackAuthorize`.
 */
class StackAuthorizeSearch extends StackAuthorize
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'stack_id', 'status', 'member_id'], 'integer'],
            [['price', 'real_price'], 'number'],
            [['created_at', 'updated_at', 'stackcode', 'membername', 'type', 'note'], 'safe'],
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
        $query = StackAuthorize::find()
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
                $query->andFilterWhere(['>=', $this::tableName() . '.created_at', $date[0] . ' 00:00:00']);
                $query->andFilterWhere(['<=', $this::tableName() . '.created_at', $date[1] . ' 23:59:59']);
            }
        }
        if ($this->updated_at) {
            $date = explode(' - ', $this->updated_at);
            if (count($date)  == 2) {
                $query->andFilterWhere(['>=', $this::tableName() . '.updated_at', $date[0] . ' 00:00:00']);
                $query->andFilterWhere(['<=', $this::tableName() . '.updated_at', $date[1] . ' 23:59:59']);
            }
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'stack_id' => $this->stack_id,
            'price' => $this->price,
            'real_price' => $this->real_price,
            $this::tableName() . '.status' => $this->status,
            'type' => $this->type,
            'member_id' => $this->member_id,
        ])->andFilterWhere(['like',$this::tableName() . '.note',$this->note])
            ->andFilterWhere(['like','stack.code',$this->stackcode])
            ->andFilterWhere(['like','member.username',$this->membername])
            ->orderBy(['created_at' => SORT_DESC]);

        return $dataProvider;
    }
}
