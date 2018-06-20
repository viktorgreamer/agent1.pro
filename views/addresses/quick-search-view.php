<?php if ($addresses) {
    // my_var_dump($addresses);
    foreach ($addresses as $address) {
        echo "<br><button class='btn btn-sm btn-success set-id-address' data-id_address=\"" . $address->id . "\"
                                data-id=\"" . $id . "\">" . $address->address . "
                        </button>";
    }
} else echo $message;
?>
<?php
$script = <<< JS
// ручное добавление id_address
$('.set-id-address').on('click', function (e) {

e.preventDefault();
var id_address = $(this).data('id_address');
var id = $(this).data('id');

$.ajax({
url: '/addresses/set-id-address',
data: {id_address: id_address, id: id},
type: 'get',
success: function (res) {
    this.disabled = true;
// toastr.success(res);
},

error: function () {
alert('error')
}
});
this.disabled = true;
});

JS;

//маркер конца строки, обязательно сразу, без пробелов и табуляции
$this->registerJs($script, yii\web\View::POS_READY);


