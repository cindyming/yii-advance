<?php

namespace common\models\search;

use common\models\CSVExport;
use common\models\Stack;
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
            'member_id' => $this->member_id,
            'volume' => $this->volume,
            'type' => $this->type,
            'total_price' => $this->total_price,
            'stack_transaction.status' => $this->status,
            'total' => $this->total,
        ])->andFilterWhere(['like','stack.code',$this->stackcode])
            ->andFilterWhere(['like','stack.name',$this->stackname])
            ->andFilterWhere(['like','member.username',$this->membername])
            ->orderBy(['created_at' => SORT_DESC]);

        return $dataProvider;
    }


    public function export($params)
    {
        $start = time();
        $query = StackTransaction::find()
            ->select(array(
              'username', 'member_id', 'stack_id', 'stack_id AS stack_name', 'type',
                 'volume', 'price', 'total_price', 'charge','stack_transaction.status', 'stack_transaction.created_at'
            ))
            ->joinWith(['member' => function($query) { $query->from(['member' => 'member']);}])
            ->orderBy(['created_at' => SORT_DESC]);


        $this->load($params);

        if ($this->created_at) {
            $date = explode(' - ', $this->created_at);
            if (count($date)  == 2) {
                $query->andFilterWhere(['>=', $this::tableName() . '.created_at', $date[0] . ' 00:00:00']);
                $query->andFilterWhere(['<=', $this::tableName() . '.created_at', $date[1] . ' 23:59:59']);
            }
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'type' => $this->type,
            'stack_transaction.status' => $this->status
        ])->orderBy(['created_at' => SORT_DESC]);

        $sql = ($query->createCommand()->getRawSql());

        $connection = Yii::$app->db;

        $command = $connection->createCommand($sql);

        $result = $command->queryAll();

        $header = array(
            'username' => '会员编号',
            'stack_id' => '股票代码',
            'stack_name' => '股票名称',
            'type' => '交易类型',
            'volume' => '交易量',
            'price' => '股票价格',
            'total_price' => '总股价',
            'charge' => '交易手续费',
            'status'=> '交易状态',
            'created_at' => '交易日期');

        $data = array($header);
        foreach ($result as $row) {
            unset($row['member_id']);
            $row['stack_name'] = Stack::getStackNameOptions()[$row['stack_id']];
            $row['stack_id'] = Stack::getStackCodeOptions()[$row['stack_id']];
            $row['type'] = Yii::$app->options->getOptionLabel('stack_type', $row['type']);
            $row['status'] = Yii::$app->options->getOptionLabel('transcation_status', $row['status']);
            $data[] = $row;
        }
        unlink(Yii::getAlias('@webroot') . '/assets/transactions.csv');
        CSVExport::Export([
            'dirName' => Yii::getAlias('@webroot') . '/assets/',
            'fileName' => 'transactions.csv',
            'data' => $data
        ]);

    }
}
