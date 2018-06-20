<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 10.10.2017
 * Time: 22:43
 */
use app\models\SaleFilters;

?>
<div class="row">
    <div class="col-8">
        <div class="row">
            <br>
            <h1>Как быстро продать недвижимость.</h1>
            <br>

            <blockquote class="blockquote bq-success">
                <ol>
                    <li> Необходимо установить РЕАЛЬНУЮ (!!!) рыночную цену. Обзвон агентств по
                        недвижимости
                        не всегда дает точную рыночную стоимость. В попытке за вас «зацепиться» вам могут предложить
                        завышенную
                        цену, но так и не продать по обещанной цене. Лучше всего: найти в интернете все объявления по
                        вашему
                        району
                        на похожие квартиры и назначить цену чуть ниже средней.
                    </li>
                    <li> Минимизируйте минусы и недочеты. Плохой запах, элементарный беспорядок,
                        неработающая
                        лампочка могут стать той самой «ложкой дегтя».
                    </li>

                    <li>Есть покупатели, которые еще на стадии первого звонка хотят, чтобы в квартире не
                        был
                        никто прописан, и все документы были бы в порядке и свежими. Не поленитесь и сделайте ,так как
                        будто
                        завтра
                        готовы продать квартиру.
                    </li>
                </ol>
            </blockquote>
            <blockquote class="blockquote bq-primary">
                <div class="row">
                    <div class="col-1">
                        <i class="fa fa-exclamation red-text fa-5x" aria-hidden="true"></i>
                    </div>
                    <div class="col-11">
                        У нас есть еще 11 действенных способов, чтобы быстро продать вашу недвижимость.
                        Сначала мы поищем среди <span
                                class="badge badge-pill light-blue"><?= SaleFilters::find()->Where(['type' => 1])->count(); ?> </span>
                        наших клиентов.
                        В силу "коммерческой тайны" мы не можем все их раскрыть, но вы можете оставить <a href="#" type="button" class="badge badge-info" style="font-size: larger" data-toggle="modal"
                                                                                                          data-target="#modalContactForm"> заявку </a> или
                        позвонить по номеру 90-40-30 и убедиться в этом сами.
                    </div>
                </div>

            </blockquote>
        </div>
    </div>
    <div class="col-4">

        <div class="row">
            <?= \app\components\webWidgets\AdvicesWidgets::widget(['count' => 4, 'not_q' => $_GET['q']]); ?>
        </div>
    </div>
</div>
<?php
echo $this->render('order',[ 'get' => 'advices']);
?>