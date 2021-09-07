# Grab Corporation Info for Yii2 

该模块用于抓取企业基本信息，可抓取到的信息如下：

Key             | Desc
--------------- | ---------------
corpNo          | 公司統一編號
status          | 公司狀況描述
corpName        | 公司名稱
capitalStock    | 資本總額(元)
paidCapital     | 實收資本額(元)
responsibleName | 代表人姓名
address         | 公司登記地址
registerOrganization | 登記機關名稱
setupDate       | 核准設立日期
approvalDate    | 最後核准變更日期
revokeDate      | 撤銷日期
caseStatus      | 停復業狀況
caseStatusDesc  | 停復業狀況描述
susDate         | 停業核准日期
susBegDate      | 停業/延展期間(起)
susEndDate      | 停業/延展期間(迄)

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist thirsight/yii2-tsmd-corpinfo
```

or add

```
"thirsight/yii2-tsmd-corpinfo": "~2.0.0"
```

to the require section of your `composer.json` file.

Usage
-----

抓取单个企業信息

```php
Yii::$app->get('corpinfo')->grabCorp('20828393');
```

抓取多个企業信息

```php
Yii::$app->get('corpinfo')->grabCorps(['20828393', '87454751']);
```
