<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\StackTrends;

/**
 * StackTrendsSearch represents the model behind the search form about `common\models\StackTrends`.
 */
class StackTrendsSearch extends StackTrends
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'stack_id'], 'integer'],
            [['price'], 'number'],
            [['created_at', 'updated_at', 'code', 'name'], 'safe'],
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
        $query = StackTrends::find()
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
            $this::tableName() . '.price' => $this->price,
        ]) ->andFilterWhere(['like','stack.code',$this->code])
            ->andFilterWhere(['like','stack.name',$this->name])
            ->orderBy(['created_at' => SORT_DESC]);

        return $dataProvider;
    }
}
