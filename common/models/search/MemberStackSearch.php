<?php

namespace common\models\search;

use common\models\CSVExport;
use common\models\Stack;
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
            [['id', 'member_id', 'stack_id', 'stack_id'], 'integer'],
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
            'stack_id' => $this->stack_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ])->andFilterWhere(['like','member.username',$this->membername])
            ->andFilterWhere(['like','stack.code',$this->stackcode])
            ->andFilterWhere(['like','stack.name',$this->stackname])
            ->orderBy(['created_at' => SORT_DESC]);

        return $dataProvider;
    }

    public function export($params)
    {
        $start = time();
        $query = MemberStack::find()
            ->select(array(
                'username', 'member_id', 'stack_id', 'stack_id AS stack_name', 'sell_volume',
                'lock_volume','stack.price AS price'
            ))
            ->joinWith(['member' => function($query) { $query->from(['member' => 'member']);}])
            ->joinWith(['stack' => function($query) { $query->from(['stack' => 'stack']);}])
            ->orderBy(['created_at' => SORT_DESC]);

        $this->load($params);

        $query->andFilterWhere([
            'id' => $this->id,
            'member_id' => $this->member_id,
            'stack_id' => $this->stack_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ])->andFilterWhere(['like','member.username',$this->membername])
            ->andFilterWhere(['like','stack.code',$this->stackcode])
            ->andFilterWhere(['like','stack.name',$this->stackname])
            ->orderBy(['member_stack.updated_at' => SORT_DESC]);

        $sql = ($query->createCommand()->getRawSql());

        $connection = Yii::$app->db;

        $command = $connection->createCommand($sql);

        $result = $command->queryAll();

        $header = array(
            'username' => '会员编号',
            'stack_id' => '股票代码',
            'stack_name' => '股票名称',
            'sell_volume' => '可交易量',
            'lock_volume' => '待解锁数量',
            'price' => '当前股价',
            'total_price' => '总股价'
        );

        $data = array($header);
        foreach ($result as $row) {
            unset($row['member_id']);
            $row['stack_name'] = Stack::getStackNameOptions()[$row['stack_id']];
            $row['stack_id'] = Stack::getStackCodeOptions()[$row['stack_id']];
            $row['total_price'] = ($row['sell_volume'] + $row['lock_volume']) * $row['price'];
            $data[] = $row;
        }

        CSVExport::Export([
            'dirName' => Yii::getAlias('@webroot') . '/assets/',
            'fileName' => 'fund.csv',
            'data' => $data
        ]);

    }
}
