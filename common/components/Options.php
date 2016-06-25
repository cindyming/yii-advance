<?php

namespace common\components;

class Options
{
    private $_options = array(
        'bank' => array(
            'ICBC' => '工商银行',
            'ABC' => '农业银行'
        ),
        'title' => array(
            'mis' => '女士',
            'mr' => '先生'
        ),
        'role' => array(
            2 => '待审核',
            3 => '正式',
            4 => '已拒绝'
        ),
        'country' => array(
            "CN" => '中国',
            "VI" => '越南',
        ),
        'question_type' => array(
            1 => '付款问题',
            2 => '代理问题',
            3 => '账户问题',
            4 => '技术问题',
            5 => '建议问题',
            6 => '提现问题',
            7 => '其他问题',
        ),
        'be_top' => array(
            '0' => '正常',
            '1' => '置顶'
        ),
        'status' => array(
            0 => '正常',
            1 => '锁定'
        ),
        'buy_stack' => array(
            1 => '是',
            0 => '否'
        ),
        'locked' => array(
            0 => '未锁定',
            1 => '已锁定'
        ),
        'cleared' => array(
            0 => '未清仓',
            1 => '已清仓'
        ),
        'date_status' => array(
            0 => '普通',
            1 => '***假期***'
        ),
        'account_type' => array(
            1 => '理财账户',
            2 => '购股账户'
        ),
        'out_type' => array(
            1 => '提现',
            2 => '购股',
            3 => '转账',
            4 => '其他'
        ),
        'stack_type' => array(
            0 => '购买',
            1 => '出售'
        ),
        'in_type' => array(
            1 => '充值',
            2 => '出售',
            3 => '转账',
            4 => '其他',
            5 => '基金'
        ),
        'cash_status' => array(
            1 => '未处理',
            2 => '已发放',
            3 => '已拒绝',
        ),
        'transcation_status' => array(
            0 => '交易锁定',
            1 => '交易完成',
            2 => '交易失败',
        )
    );

    public function getOptions($attribute_name, $filter = false)
    {
        $options = isset($this->_options[$attribute_name]) ? $this->_options[$attribute_name] : array();
        if (count($options) && $filter) {
            $options = array('' => '不限') + $options;
        }
        return $options;

    }
    public function getOptionLabel($attribute_name, $value)
    {
        $label = '';
        if ( isset($this->_options[$attribute_name]) &&  isset($this->_options[$attribute_name][$value])){
            $label = $this->_options[$attribute_name][$value];
        }
        return $label;
    }
}