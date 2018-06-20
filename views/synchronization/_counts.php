<?
use app\models\Synchronization;
  // my_var_dump($dataProvider->query);
?>
<p>
    <a class="btn btn-primary" data-toggle="collapse" href="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
        <i class="fa fa-bar-chart" aria-hidden="true"></i>
    </a>
</p>
<div class="collapse" id="collapseExample">
    <?= Synchronization::Counts($dataProvider->query, [
        'disactive' => [0,1,2],
        'status' => [3,4,5,6,7,8,9],
        'geocodated' => [1,2,3,4,5,6,7,8,9],
        'parsed' => [1,2,3],
        'sync' => [1,2,3],
        'load_analized' => [1,2,3],
    ]) ;?>
    </div>


