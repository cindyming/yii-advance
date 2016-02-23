<?php

/* @var $this \yii\web\View */
/* @var $content string */

use backend\assets\AppAsset;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use common\widgets\Alert;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta name="renderer" content="webkit" >
    <meta http-equiv="X-UA-Compatible" content="IE=edge" >
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <div class="header">
        <div class="container">
            <h2 class="site-name">管理平台</h2>
            <?php
            NavBar::begin();
            $menuItems = [
                [
                    'label' => '会员管理',
                    'url' => ['/member/approvedindex'],
                ],
                [
                    'label' => '股票管理',
                    'url' => ['/stack/index'],
                    'items' => [
                        ['label' => '股票记录', 'url' => ['/stack/index']],
                        ['label' => '股价动态', 'url' => ['/stack/trends']],
                        ['label' => '股票交易', 'url' => ['/stack/transactions']],
                        ['label' => '股票资产', 'url' => ['/stack/fund']],
                        ['label' => '委托', 'url' => ['/authorize/index']]
                    ]
                ],
                [
                    'label' => '货币管理', 'url' => ['/account/list'],
                    'items' => [
                        ['label' => '账户管理', 'url' => yii\helpers\Url::to('/account/list')],
                        ['label' => '出账明细', 'url' => yii\helpers\Url::to('/account/outlist')],
                        ['label' => '入账明细', 'url' => yii\helpers\Url::to('/account/inlist')],
                        ['label' => '提现管理', 'url' => yii\helpers\Url::to('/account/cashlist')],
                    ]
                ],
                [
                    'label' => '密码修改', 'url' => ['/system/password'],
                ],
                Yii::$app->user->isGuest ?
                    ['label' => 'Login', 'url' => ['/site/login']] :
                    [
                        'label' => '(' . Yii::$app->user->identity->username . ')退出',
                        'url' => ['/site/logout'],
                        'linkOptions' => ['data-method' => 'post']
                    ],
            ];
            echo Nav::widget([
                'options' => ['class' => 'navbar-nav navbar-right'],
                'items' => $menuItems,
            ]);
            NavBar::end();
            ?>
        </div>
    </div>

    <div class="container">
        <?php
        foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
            echo '<div class="alert alert-' . $key . '">' . $message . '</div>';
        }
        ?>
        <?= $content ?>
    </div>
</div>

<div id="errorMessageContainer" style="display:none">
    <div id="errorMessage">
        <div id="errorMessageHtml">
        </div>
        <button id="skipError">确认</button>
    </div>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
