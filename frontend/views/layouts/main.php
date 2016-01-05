<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use common\widgets\Alert;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <div class="top-header">
        <ul>
            <li>
                会员ID: <?php echo Yii::$app->user->identity->username?>
            </li>
            <li>
                理财账户余额: <?php echo Yii::$app->user->identity->finance_fund; ?>
            </li>
            <li>
                股票账户余额: <?php echo Yii::$app->user->identity->stack_fund?>
            </li>
        </ul>
    </div>
    <div class="header">
        <h2>QC(启程)股票在线交易平台</h2>
        
        <?php
        NavBar::begin();
        echo Nav::widget([
            'options' => ['class' => 'navbar-nav navbar-left'],
            'items' => [
                ['label' => '首页', 'url' => ['/news/index']],
                [
                    'label' => '业务中心', 'url' => ['/member/create'],
                    'items' => [
                        (\common\models\System::loadConfig('show_add_member')) ?
                        ['label' => '会员注册', 'url' => ['/member/create']]:'',
                        ['label' => '我的注册', 'url' => ['/member/index']],
                    ]
                ],
                [
                    'label' => '基金资产', 'url' => ['/account/fund'],
                ],
                [
                    'label' => '股票管理',
                    'url' => ['/stack/index'],
                    'items' => [
                        ['label' => '交易中心', 'url' => ['/stack/index']],
                        ['label' => '股价动态', 'url' => ['/stack/trends']],
                        ['label' => '交易记录', 'url' => ['/stack/transactions']],
                        ['label' => '股票资产', 'url' => ['/stack/fund']],
                    ]
                ],
                [
                    'label' => '财务管理', 'url' => ['/blank'],
                    'items' => [
                        ['label' => '入账明细', 'url' => ['/account/inlist']],
                        ['label' => '出账明细', 'url' => ['/account/outlist']],
                        [
                            'label' => '提现管理', 'url' => ['/account/cashlist'],
                        ],
                        ['label' => '申请提现', 'url' => ['/account/charge']],
                    ]
                ],
                [
                    'label' => '系统公告', 'url' => ['/news/index'],
                    'items' => [
                        ['label' => '新闻公告', 'url' => ['/news/index']],
                        ['label' => '留言列表', 'url' => ['/message/index', 'user_id' => Yii::$app->user->identity->id]],
                        ['label' => '添加留言', 'url' => ['/message/create']]
                    ]
                ],
                [
                    'label' => '会员管理', 'url' => ['/member/view', 'id' => Yii::$app->user->identity->id],
                    'items' => [
                        ['label' => '修改密码', 'url' => ['/member/changepassword']],
                        ['label' => '会员资料', 'url' => ['/member/view', 'id' => Yii::$app->user->identity->id]]
                    ]
                ],
                Yii::$app->user->isGuest ?
                    ['label' => '安全退出', 'url' => ['/site/login']] :
                    [
                        'label' => '安全退出 (' . Yii::$app->user->identity->username . ')',
                        'url' => ['/site/logout'],
                        'linkOptions' => ['data-method' => 'post']
                    ],
            ],
        ]);
        NavBar::end();
        ?>
    </div>
    <div class="container">
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
