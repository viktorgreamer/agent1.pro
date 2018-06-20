<?
use yii\widgets\ListView;
?>
<?= ListView::widget([
    'dataProvider' => $dataProvider,
    'itemView' => 'sale-table-web',

    'layout' => "{summary}\n{items}\n{pager}",
    'summary' => 'Показано {count} из {totalCount} предложений',
    'summaryOptions' => [
        'tag' => 'span',
        'class' => 'badge red'
    ],
    'viewParams' => ['controls' => true],

    'emptyText' => '<p>Нет вариантов</p>',
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