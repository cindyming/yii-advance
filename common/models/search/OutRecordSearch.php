<?php

namespace common\models\search;

use common\models\CSVExport;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\OutRecord;

/**
 * OutRecordSearch represents the model behind the search form about `common\models\OutRecord`.
 */
class OutRecordSearch extends OutRecord
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'member_id', 'account_type'], 'integer'],
            [['amount', 'fee', 'total'], 'number'],
            [['note', 'created_at', 'updated_at', 'membername', 'type'], 'safe'],
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
        $query = OutRecord::find()
            ->joinWith(['member' => function($query) { $query->from(['member' => 'member']);}])
            ->orderBy(['id' => SORT_DESC]);

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
            'account_type' => $this->account_type,
            'type' => $this->type,
            'amount' => $this->amount,
            'fee' => $this->fee,
            'total' => $this->total,
        ]);

        if ($this->created_at) {
            $date = explode(' - ', $this->created_at);
            if (count($date)  == 2) {
                $query->andFilterWhere(['>=', $this::tableName() . '.created_at', $date[0] . ' 00:00:00']);
                $query->andFilterWhere(['<=', $this::tableName() . '.created_at', $date[1] . ' 23:59:59']);
            }
        }

        $query->andFilterWhere(['like', 'note', $this->note])
            ->andFilterWhere(['like','member.username',$this->membername])
            ->orderBy(['id' => SORT_DESC]);

        return $dataProvider;
    }

    public function export($params)
    {

        $query = OutRecord::find()
            ->select(array(
                'username',
                'type',
                'account_type',
                'amount',
                'fee',
                'total',
                $this::tableName() . '.created_at as created_at',
                'note'
            ))
            ->joinWith(['member' => function($query) { $query->from(['member' => 'member']);}])
            ->orderBy([$this::tableName() . '.id' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!Yii::$app->user->identity->isAdmin()) {
            $this->member_id = Yii::$app->user->identity->id;
        }
        $query->andFilterWhere([
            'id' => $this->id,
            'member_id' => $this->member_id,
            'account_type' => $this->account_type,
            'type' => $this->type,
            'amount' => $this->amount,
            'fee' => $this->fee,
            'total' => $this->total,
        ]);

        if ($this->created_at) {
            $date = explode(' - ', $this->created_at);
            if (count($date)  == 2) {
                $query->andFilterWhere(['>=', $this::tableName() . '.created_at', $date[0] . ' 00:00:00']);
                $query->andFilterWhere(['<=', $this::tableName() . '.created_at', $date[1] . ' 23:59:59']);
            }
        }

        $query->andFilterWhere(['like', 'note', $this->note])
            ->andFilterWhere(['like','member.username',$this->membername])
            ->orderBy([$this::tableName() . '.id' => SORT_DESC]);

        $sql = ($query->createCommand()->getRawSql()) . ' limit 5000';

        $connection = Yii::$app->db;

        $command = $connection->createCommand($sql);

        $result = $command->queryAll();

        $header = array(
            'username' => '会员编号',
            'type' => '出账类型',
            'account_type' => '账户类型',
            'amount' => '金额',
            'fee' => '手续费',
            'total' => '出账后余额',
            'created_at' => '日期',
            'note' => '摘要'
        );

        $data = array($header);
        foreach ($result as $row) {
            $row['amount'] =  $row['amount'] . ' ';
            $row['fee'] =  $row['fee'] . ' ';
            $row['total'] =  $row['total'] . ' ';
            $row['type'] = Yii::$app->options->getOptionLabel('out_type', $row['type']);
            $row['account_type'] = Yii::$app->options->getOptionLabel('account_type', $row['account_type']);
            $data[] = $row;
        }

        CSVExport::Export([
            'dirName' => Yii::getAlias('@webroot') . '/assets/',
            'fileName' => 'outlist.xls',
            'data' => $data
        ], 'cash');
    }
}
