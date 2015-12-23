<?php

namespace common;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Member;

/**
 * MemberSerach represents the model behind the search form about `common\models\Member`.
 */
class MemberSerach extends Member
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status', 'locked', 'role_id', 'investment', 'buy_stack', 'added_by'], 'integer'],
            [['auth_key', 'username', 'access_token', 'nickname', 'password_hash', 'password_hash2', 'identity', 'phone', 'title', 'bank', 'cardname', 'cardnumber', 'bankaddress', 'email', 'qq', 'created_at', 'updated_at', 'approved_at'], 'safe'],
            [['stack_fund', 'finance_fund'], 'number'],
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
        $query = Member::find();

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
            'status' => $this->status,
            'locked' => $this->locked,
            'role_id' => $this->role_id,
            'investment' => $this->investment,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'approved_at' => $this->approved_at,
            'buy_stack' => $this->buy_stack,
            'added_by' => $this->added_by,
            'stack_fund' => $this->stack_fund,
            'finance_fund' => $this->finance_fund,
        ]);

        $query->andFilterWhere(['like', 'auth_key', $this->auth_key])
            ->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'access_token', $this->access_token])
            ->andFilterWhere(['like', 'nickname', $this->nickname])
            ->andFilterWhere(['like', 'password_hash', $this->password_hash])
            ->andFilterWhere(['like', 'password_hash2', $this->password_hash2])
            ->andFilterWhere(['like', 'identity', $this->identity])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'bank', $this->bank])
            ->andFilterWhere(['like', 'cardname', $this->cardname])
            ->andFilterWhere(['like', 'cardnumber', $this->cardnumber])
            ->andFilterWhere(['like', 'bankaddress', $this->bankaddress])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'qq', $this->qq]);

        return $dataProvider;
    }
}
