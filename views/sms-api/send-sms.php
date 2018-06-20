<?php

use yii\helpers\Html;
use yii\widgets\LinkPager;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SmsApiSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Sms Apis';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sms-api-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        Количество посланных смс <?= count($array_sms_test); ?> <br>
        token смс <?= $token; ?> <br>
        утройство <?= $device; ?> <br>
        результат <? $error = json_decode($result);
     echo $error->error ?>

    </p>

    <div class="sale-lists-index">


                <div class="container">
                    <table class='table table-striped'>
                        <thead>
                        <tr>
                            <th>текст</th>

                            <th>Телефон</th>
                        </tr>
                        </thead>
                        <tbody>


                        <?php foreach ($array_sms_test as $tr): ?>
                            <tr>




                                <td><?php echo $tr['msg']; ?>

                                </td>
                                <td><?php echo $tr['phone']; ?></td>


                            </tr>
                        <?php endforeach; ?>

                        </tbody>
                    </table>
                </div>



            </div>
    </div>
