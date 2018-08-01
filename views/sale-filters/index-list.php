<?php


use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\models\Tags;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SaleFiltersSearch2 */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Фильтры поиска';
$this->params['breadcrumbs'][] = $this->title;
$session = Yii::$app->session;
$user_full_name = Yii::$app->user->identity->fullname;
?>
<div class="sale-filters-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

  <?  echo  ListView::widget([
    'dataProvider' => $dataProvider,
    'itemView' => '_view',

    'layout' => "{summary}\n{items}\n{pager}",
    'summary' => 'Показано {count} из {totalCount} фильтров',
    'summaryOptions' => [
    'tag' => 'span',
    'class' => 'badge red'
    ],
   // 'viewParams' => ['controls' => true, 'salefilter' => $salefilter],

    'emptyText' => '<p>Нет фильтров</p>',
    'emptyTextOptions' => [
    'tag' => 'p'
    ],

    'pager' => [
    'options' => ['class' => 'pagination pagination-circle pg-blue mb-0'],
    'linkOptions' => ['class' => 'page-link'],
    'firstPageCssClass' => 'page-item first',
    'prevPageCssClass' => 'page-item last',
    'nextPageCssClass' => 'page-item next',
    'activePageCssClass' => 'page-item active',
    'disabledPageCssClass' => 'page-item invisible'
    ],
    ]) ?>