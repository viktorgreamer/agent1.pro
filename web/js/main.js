$('.salelist-del-button').on('click', function (e) {
    e.preventDefault();
    var id = $(this).data('id');
    var id_item = $(this).data('id_item');

    $.ajax({
        url: '/sale-lists/delete-item-ajax',
        data: {id: id, id_item: id_item},
        type: 'get',
        success: function (res) {

        },

        error: function () {
            alert('error')
        }
    });
    this.disabled = true;
});
$('.salefilter-confirm-rewrite-button').on('click', function (e) {
    e.preventDefault();
    var rewrite = 1;

    $.ajax({
        url: '/sale/rewrite-salefilter',
        data: {rewrite: rewrite},
        type: 'get',
        success: function (res) {

        },

        error: function () {
            alert('error')
        }
    });
    $('#ExistedSaleFilter').modal('hide');

});$('.salelist-confirm-rewrite-button').on('click', function (e) {
    e.preventDefault();
    var rewrite = 1;

    $.ajax({
        url: '/sale/rewrite-salelist',
        data: {rewrite: rewrite},
        type: 'get',
        success: function (res) {

        },

        error: function () {
            alert('error')
        }
    });
    $('#ExistedSaleList').modal('hide');

});
$('.error-geocodetion').on('click', function (e) {
    e.preventDefault();
    var id_item = $(this).data('id_item');

    $.ajax({
        url: '/sale/error-geocodetion',
        data: {geocodated: 9, id: id_item},
        type: 'get',
        success: function (res) {

        },

        error: function () {
            alert('error')
        }
    });
    this.disabled = true;

});
$('.set-sold').on('click', function (e) {
    e.preventDefault();
    var id_item = $(this).data('id_item');

    $.ajax({
        url: '/sale/set-sold',
        data: {id: id_item},
        type: 'get',
        success: function (res) {

        },

        error: function () {
            alert('error')
        }
    });
    this.disabled = true;
});

$('.add-object-to-favourites').on('click', function (e) {
    e.preventDefault();
    var id_item = $(this).data('id_item');


    $.ajax({
        url: '/sale/add-to-favourites',
        data: {id_item: id_item},
        type: 'get',
        success: function (res) {

        },

        error: function () {
            alert('error')
        }
    });
    this.disabled = true;
});

$('.add-favourites-to-list').on('click', function (e) {
    e.preventDefault();
    var id_list = $(this).data('id_list');


    $.ajax({
        url: '/sale-lists/add-favourites',
        data: {id_list: id_list},
        type: 'get',
        success: function (res) {

        },

        error: function () {
            alert('error')
        }
    });
    this.disabled = true;
});

$('.filter-item-del-button').on('click', function (e) {
    e.preventDefault();
    var id = $(this).data('id');
    var id_item = $(this).data('id_item');

    $.ajax({
        url: '/sale-filters/delete-item-ajax',
        data: {id: id, id_item: id_item},
        type: 'get',


        error: function () {
            alert('error')
        }
    });
    this.disabled = true;
});

$('.filter-item-add-button').on('click', function (e) {
    e.preventDefault();
    var id = $(this).data('id');
    var id_item = $(this).data('id_item');

    $.ajax({
        url: '/sale-filters/add-item-ajax',
        data: {id: id, id_item: id_item},
        type: 'get',
        success: function (data) {


        },

        error: function () {
            alert('error')
        }
    });
    this.disabled = true;
});

$('.filter-delete-item-from-white-list-button').on('click', function (e) {
    e.preventDefault();
    var id = $(this).data('id');
    var id_item = $(this).data('id_item');

    $.ajax({
        url: '/sale-filters/delete-item-from-white-list-ajax',
        data: {id: id, id_item: id_item},
        type: 'get',
        success: function (data) {


        },

        error: function () {
            alert('error')
        }
    });
    this.disabled = true;
});


$('.filter-delete-item-from-black-list-button').on('click', function (e) {
    e.preventDefault();
    var id = $(this).data('id');
    var id_item = $(this).data('id_item');

    $.ajax({
        url: '/sale-filters/delete-item-from-black-list-ajax',
        data: {id: id, id_item: id_item},
        type: 'get',
        success: function (data) {


        },

        error: function () {
            alert('error')
        }
    });
    this.disabled = true;
});
$('.set-year-and-fix-it').on('click', function (e) {
    e.preventDefault();
    var id_address = $(this).data('id_address');
    var year = $(this).data('year');

    $.ajax({
        url: '/addresses/set-year-and-fix-it',
        data: {id_address: id_address, year: year},
        type: 'get',
        success: function (data) {


        },

        error: function () {
            alert('error')
        }
    });
    this.disabled = true;
});

// удаление дубликатов телефонов в списке на рассылку
$('.sms-api-del-dublicate-button').on('click', function (e) {
    e.preventDefault();
    var id_list = $(this).data('id_list');


    $.ajax({
        url: '/sms-api/delete-dublicate',
        data: {id_list: id_list},
        type: 'get',
        success: function (res) {

        },

        error: function () {
            alert('error')
        }
    });
    this.disabled = true;
});

// удаление телефона из текущей рассылки
$('.sms-api-del-button').on('click', function (e) {
    e.preventDefault();
    var id_list = $(this).data('id_list');
    var id = $(this).data('id');


    $.ajax({
        url: '/sms-api/delete-one',
        data: {id: id, id_list: id_list},
        type: 'get',
        success: function (res) {

        },

        error: function () {
            alert('error')
        }
    });
    this.disabled = true;
});

// сохранение тикущего списка рассылки
$('.sms-api-save-button').on('click', function (e) {
    e.preventDefault();
    var id_list = $(this).data('id_list');
    var dot_text_sms = $(this).data('dot_text_sms');
    var status = $(this).data('status');


    $.ajax({
        url: '/sms-api/save',
        data: {id_list: id_list, dot_text_sms: dot_text_sms, status: status},
        type: 'get',
        success: function (res) {

        },

        error: function () {
            alert('error')
        }
    });
    this.disabled = true;
});


// отложить телефона из текущей рассылки
$('.sms-api-delay-button').on('click', function (e) {
    e.preventDefault();
    var id = $(this).data('id');


    $.ajax({
        url: '/sms-api/delay-one',
        data: {id: id},
        type: 'get',
        success: function (res) {

        },

        error: function () {
            alert('error')
        }
    });
    this.disabled = true;
});


// сохранить фильтр
$('.save-filter-button').on('click', function (e) {
    e.preventDefault();
    var name = $('#salefilters-name').val();


    $.ajax({
        url: '/sale/save-current-filter-ajax',
        data: {name: name},
        type: 'get',
        success: function (data) {
            if (data == 'Exist_current_name') {
                var is_rewrite = confirm('Фильтр с данным именем уже существует. Заменить?');

            }


            if (is_rewrite) {


                $.ajax({
                    url: '/sale/save-current-filter-ajax-rewrite',
                    data: {name: name},
                    type: 'get'

                });
            }


        }


    });
});

// $('#saveModal').on('show.bs.modal', function () {
//     $('#myInput').focus()
// })
// $('#saveListModal').on('show.bs.modal', function () {
//     $('#myInput').focus()
// })


// сохранить список
$('.save-list-button').on('click', function (e) {
    e.preventDefault();
    var name = $('#salelists-name').val();


    $.ajax({
        url: '/sale/save-current-list-ajax',
        data: {name: name},
        type: 'get',
        success: function (data) {
            if (data == 'Exist_current_name') {
                var is_rewrite_list = confirm('Список с данным именем уже существует. Заменить?');

            }


            if (is_rewrite_list) {


                $.ajax({
                    url: '/sale/save-current-list-ajax-rewrite',
                    data: {name: name},
                    type: 'get'

                });
            }


        }


    });
});


// помеместить телефон в список нерассылки
$('.sms-api-ban-button').on('click', function (e) {
    e.preventDefault();
    var phone = $(this).data('phone');


    $.ajax({
        url: '/sms-api/move-to-sms-api-ban-list',
        data: {phone: phone},
        type: 'get',
        success: function (res) {

        },

        error: function () {
            alert('error')
        }
    });
    this.disabled = true;
});

// export в список рассылки смс
$('.export-to-sms-api-button').on('click', function (e) {
    e.preventDefault();
    var id = $(this).data('id');


    $.ajax({
        url: '/sale-lists/export-ajax',
        data: {id: id},
        type: 'get',
        success: function (res) {

        },

        error: function () {
            alert('error')
        }
    });
    this.disabled = true;
});


// сохранить текущий фильтр
$('.save-current-filter').on('click', function (e) {
    e.preventDefault();
    var id = $(this).data('id');


    $.ajax({
        url: '/sale/save-current-filter-ajax',
        data: {id: id},
        type: 'get',
        success: function (res) {

        },

        error: function () {
            alert('error')
        }
    });
    this.disabled = true;
});

// зафиксировать данный адрес
$('.address-ajax-fix').on('click', function (e) {
    e.preventDefault();
    var id = $(this).data('id');


    $.ajax({
        url: '/addresses/fix',
        data: {id: id},
        type: 'get',
        success: function (res) {

        },

        error: function () {
            alert('error')
        }
    });

    $(this).hide();
});

// зафиксировать данный адрес как не жилой объект
$('.address-ajax-not-living').on('click', function (e) {
    e.preventDefault();
    var id = $(this).data('id');


    $.ajax({
        url: '/addresses/not-living',
        data: {id: id},
        type: 'get',
        success: function (res) {

        },

        error: function () {
            alert('error')
        }
    });

    $(this).hide();
});


// работа с tags

// добавление или удаление текущего tags для sale
$('.tags-action-button').on('click', function (e) {
    e.preventDefault();
    var sale_id = $(this).data('sale_id');
    var tag_id = $(this).data('tag_id');

    $.ajax({
        url: '/real-tags/tag',
        data: {sale_id: sale_id, tag_id: tag_id},
        type: 'get',
        success: function (res) {

        },

        error: function () {
            alert('error')
        }
    });
    this.disabled = true;
});

// добавление или удаление текущего tags для address
$('.tags-action-button-address').on('click', function (e) {
    e.preventDefault();
    var address_id = $(this).data('address_id');
    var tag_id = $(this).data('tag_id');

    $.ajax({
        url: '/real-tags/tag-address',
        data: {address_id: address_id, tag_id: tag_id},
        type: 'get',
        success: function (res) {

        },

        error: function () {
            alert('error')
        }
    });
    this.disabled = true;
});
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

        },

        error: function () {
            alert('error')
        }
    });
    this.disabled = true;
});
// добавление или удаление текущего tags для salefilter
$('.tags-action-button-salefilter').on('click', function (e) {
    e.preventDefault();
    var salefilter_id = $(this).data('salefilter_id');
    var tag_id = $(this).data('tag_id');

    $.ajax({
        url: '/real-tags/tag-sale-filter',
        data: {salefilter_id: salefilter_id, tag_id: tag_id},
        type: 'get',
        success: function (res) {

        },

        error: function () {
            alert('error')
        }
    });
    this.disabled = true;
});

function copyToClipboard(element) {
    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val($(element).text()).select();
    document.execCommand("copy");
    $temp.remove();
}
$(".button-collapse").sideNav();
// SideNav Options


/*!
 * Bootstrap-select v1.12.1 (http://silviomoreto.github.io/bootstrap-select)
 *
 * Copyright 2013-2016 bootstrap-select
 * Licensed under MIT (https://github.com/silviomoreto/bootstrap-select/blob/master/LICENSE)
 */
