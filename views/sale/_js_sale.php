<?php
use app\models\SaleFilters;
use yii\helpers\ArrayHelper;

$array = ArrayHelper::toArray($models,['app\models\Sale' => ['id','id_address', 'floor','grossarea', 'price']]);
$dispersion = SaleFilters::PERSENTAGE_OF_AREA_DIVERGENSCE;
$array = json_encode($array);

// данный script проверяет на странице наличие вариантов по данному шаблону и отмечает их  $('#row_' + item.id).css('background-color', '#e0e0e0');
// check_ONCONTROL - варианты которые на контроле
// check_ONBLAKC - варианты которые полностью в БАНЕ
// ajaxom добавляем шаблон
// copyToClipboard копирование варианта в бубер обмена
$script = <<<JS
var array = $array;
var dispersion = $dispersion;

check_ONCONTROL = function(id_address,floor, grossarea, price ) {
var b = 0;
var e = 0;
var m = 0;
array.forEach(function(item, i, array) {
     if ((item.id_address == id_address) & (item.floor == floor) & (item.grossarea >= (grossarea - dispersion)) & (item.grossarea <= (grossarea + dispersion))) 
         {
           if (item.price > price)  b++;
           if (item.price == price)  e++;
           if (item.price < price)  m++;
           if  (item.price >= price) {
               $('#row_' + item.id).css('background-color', '#e0e0e0');
           }
          }
   }
 );
if (((b+e)> 1) || (m > 0)) toastr.success('удалили' + (b+e) + 'элем.,'+ m +' элем. остался');
}

check_ONBLACK = function(id_address,floor, grossarea) {
var b = 0;
array.forEach(function(item, i, array) {
     if ((item.id_address == id_address) & (item.floor == floor) & (item.grossarea >= (grossarea - dispersion)) & (item.grossarea <= (grossarea + dispersion))) 
         {
           b++;
           $('#row_' + item.id).css('background-color', '#757575');
          }
     
     }
 );
toastr.error('удалили ' + b + ' элем.');
}


$('.filter-add-template').on('click', function (e) {
e.preventDefault();
var id = $(this).data('id');
var id_item = $(this).data('id_item');
var id_address = $(this).data('id_address');
var floor = $(this).data('floor');
var grossarea = $(this).data('grossarea');
var price = $(this).data('price');
var type = $(this).data('type');
if (type == 'ONCONTROL') check_ONCONTROL(id_address,floor,grossarea,price);
if (type == 'ONBLACK') check_ONBLACK(id_address,floor,grossarea);
$.ajax({
        url: '/sale-filters/add-template',
        data: {id: id, id_item: id_item, type: type},
        type: 'get',
        success: function (data) {
            toastr.success(data);

        },

    });
});

JS;
$this->registerJs($script, yii\web\View::POS_READY);
