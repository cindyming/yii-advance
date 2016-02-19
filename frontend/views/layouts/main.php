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
        <div class="container">
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
    </div>
    <div class="header">
        <div class="container">
            <h2 class="site-name">QC(启程)股票在线交易平台</h2>
            
            <?php
            NavBar::begin();
            echo Nav::widget([
                'options' => ['class' => 'navbar-nav navbar-left'],
                'items' => [
                    ['label' => '首页', 'url' => ['/stack/index']],
                    [
                        'label' => '股票管理',
                        'url' => ['/stack/index'],
                        'items' => [
                            ['label' => '交易中心', 'url' => ['/stack/index']],
                            ['label' => '股价动态', 'url' => ['/stack/trends']],
                            ['label' => '股票交易记录', 'url' => ['/stack/transactions']],
                            ['label' => '股票资产', 'url' => ['/stack/fund']],
                            ['label' => '我的委托', 'url' => ['/authorize/index']]
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
