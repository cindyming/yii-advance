<?php

namespace common\models\search;

use common\models\CSVExport;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Member;

/**
 * MemberSerach represents the model behind the search form about `common\models\Member`.
 */
class MemberSearch extends Member
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status', 'locked', 'role_id', 'investment', 'buy_stack', 'added_by'], 'integer'],
            [['auth_key', 'username', 'access_token', 'country', 'nickname', 'password_hash', 'password_hash2', 'identity', 'phone', 'title', 'bank', 'cardname', 'cardnumber', 'bankaddress', 'email', 'qq', 'created_at', 'updated_at', 'approved_at'], 'safe'],
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
        $query = Member::find()->orderBy(['created_at' => SORT_DESC]);
        if (!Yii::$app->user->identity->isAdmin()) {
            $this->added_by = Yii::$app->user->identity->id;
        }

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
            'buy_stack' => $this->buy_stack,
            'added_by' => $this->added_by,
            'country' => $this->country,
            'stack_fund' => $this->stack_fund,
            'finance_fund' => $this->finance_fund,
        ]);

        if ($this->approved_at) {
            $date = explode(' - ', $this->approved_at);
            if (count($date)  == 2) {
                $query->andFilterWhere(['>=', $this::tableName() . '.approved_at', $date[0] . ' 00:00:00']);
                $query->andFilterWhere(['<=', $this::tableName() . '.approved_at', $date[1] . ' 23:59:59']);
            }
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

        $query->andFilterWhere(['like', 'auth_key', $this->auth_key])
            ->andFilterWhere(['like', 'username', $this->getUsername()])
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
            ->andFilterWhere(['like', 'qq', $this->qq])
            ->orderBy(['created_at' => SORT_DESC]);

        return $dataProvider;
    }

    public function export($params)
    {
        $query = Member::find()
            ->select(array(
                'username', 'nickname', 'phone', 'identity', 'approved_at',
                'locked','country','buy_stack'
            ))
            ->orderBy(['approved_at' => SORT_DESC]);


        $this->load($params);

        $query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->status,
            'locked' => $this->locked,
            'role_id' => $this->role_id,
            'investment' => $this->investment,
            'buy_stack' => $this->buy_stack,
            'added_by' => $this->added_by,
            'country' => $this->country,
            'stack_fund' => $this->stack_fund,
            'finance_fund' => $this->finance_fund,
        ]);

        if ($this->approved_at) {
            $date = explode(' - ', $this->approved_at);
            if (count($date)  == 2) {
                $query->andFilterWhere(['>=', $this::tableName() . '.approved_at', $date[0] . ' 00:00:00']);
                $query->andFilterWhere(['<=', $this::tableName() . '.approved_at', $date[1] . ' 23:59:59']);
            }
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

        $query->andFilterWhere(['like', 'auth_key', $this->auth_key])
            ->andFilterWhere(['like', 'username', $this->getUsername()])
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
            ->andFilterWhere(['like', 'qq', $this->qq])
            ->orderBy(['approved_at' => SORT_DESC]);


        $sql = ($query->createCommand()->getRawSql());

        $connection = Yii::$app->db;

        $command = $connection->createCommand($sql);

        $result = $command->queryAll();

        $header = array(
            'username' => '会员编号',
            'nickname' => '会员昵称',
            'phone' => '手机号码',
            'identity' => '证件号',
            'approved_at' => '审核日期',
            'locked' => '是否锁定',
            'country' => '站点',
            'buy_stack' => '购股权限',
        );

        $data = array($header);
        foreach ($result as $row) {
            unset($row['member_id']);
            $row['phone'] = $row['phone'] . ' ';
            $row['identity'] = $row['identity'] . ' ';
            $row['locked'] = Yii::$app->options->getOptionLabel('locked', $row['locked']);
            $row['country'] = Yii::$app->options->getOptionLabel('country', $row['country']);
            $row['buy_stack'] = Yii::$app->options->getOptionLabel('buy_stack', $row['buy_stack']);
            $data[] = $row;
        }

        CSVExport::Export([
            'dirName' => Yii::getAlias('@webroot') . '/assets/',
            'fileName' => 'member.xls',
            'data' => $data
        ], 'member');

    }

    public function exportList($data) {
        $query = Member::find()
            ->select(array(
                'username', 'investment', 'finance_fund', 'stack_fund'
            ))
            ->orderBy(['approved_at' => SORT_DESC]);


        $this->load($data);
        $query->andFilterWhere(['like', 'username', $this->getUsername()]);

        $sql = ($query->createCommand()->getRawSql());

        $connection = Yii::$app->db;

        $command = $connection->createCommand($sql);

        $result = $command->queryAll();

        $header = array(
            'username' => '会员编号',
            'investment' => '投资金额',
            'finance_fund' => '理财基金余额',
            'stack_fund' => '购股基金余额',
        );

        $data = array($header);
        foreach ($result as $row) {
            $data[] = $row;
        }

        CSVExport::Export([
            'dirName' => Yii::getAlias('@webroot') . '/assets/',
            'fileName' => 'huobi.xls',
            'data' => $data
        ], 'member');
    }
}
