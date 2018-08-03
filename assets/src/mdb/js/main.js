// salelists

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
$('.salelist-ok-button').on('click', function (e) {
    e.preventDefault();
    var id = $(this).data('id');
    var id_item = $(this).data('id_item');

    $.ajax({
        url: '/sale-lists/ok-item',
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
$('.salelist-ban-button').on('click', function (e) {
    e.preventDefault();
    var id = $(this).data('id');
    var id_item = $(this).data('id_item');

    $.ajax({
        url: '/sale-lists/ban-item',
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
$('.change-status').on('click', function (e) {
    e.preventDefault();
    var status_name = $(this).data('status_name');
    var id_item = $(this).data('id_item');

    $.ajax({
        url: '/synchronization/change-status',
        data: {status_name: status_name, id_item: id_item},
        type: 'get',
        success: function (data) {
            toastr.success(data);
        },

        error: function () {
            alert('error')
        }
    });
    this.disabled = true;
});


$('.salelist-confirm-rewrite-button').on('click', function (e) {
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
$(document).on('click', '.set-sold', function (e) {
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
// salefilter-buttons
$('.filter-item-del-button').on('click', function (e) {
    e.preventDefault();
    var id = $(this).data('id');
    var id_item = $(this).data('id_item');

    $.ajax({
        url: '/sale-filters/delete-item-ajax',
        data: {id: id, id_item: id_item},
        type: 'get',
        success: function (data) {
            $('#row_' + id_item).css('background-color', '#e0e0e0');
            toastr.success(data);

        },

        error: function () {
            alert('error')
        }
    });
    this.disabled = true;
});
// salefilter-buttons
$('.filter-item-check-button').on('click', function (e) {
    e.preventDefault();
    var id = $(this).data('id');
    var id_item = $(this).data('id_item');

    $.ajax({
        url: '/sale-filters/add-item-to-check',
        data: {id: id, id_item: id_item},
        type: 'get',
        success: function (data) {
            $('#row_' + id_item).css('background-color', '#18ffff');
            toastr.warning(data);

        },

        error: function () {
            alert('error')
        }
    });
    this.disabled = true;
});
$('.list-address-del-button').on('click', function (e) {
    e.preventDefault();
    var id = $(this).data('id');
    var id_address = $(this).data('id_address');

    $.ajax({
        url: '/sale-lists/delete-id-address',
        data: {id: id, id_address: id_address},
        type: 'get',
        success: function (data) {
            toastr.success(data);
        },

        error: function () {
            alert('error')
        }
    });
    this.disabled = true;
});
$('.filter-address-del-button').on('click', function (e) {
    e.preventDefault();
    var id = $(this).data('id');
    var id_address = $(this).data('id_address');

    $.ajax({
        url: '/sale-filters/delete-id-address',
        data: {id: id, id_address: id_address},
        type: 'get',
        success: function (data) {
            toastr.success(data);
        },

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
            $('#row_' + id_item).css('background-color', '#b9f6ca');
            toastr.success(data);

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

// копирование элемента в буфер обмена

function copyToClipboard(element) {
    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val($(element).val()).select();
    document.execCommand("copy");
    $temp.remove();
};

var ClipboardHelper = {

    copyElement: function ($element) {
        this.copyText($element.text())
    },
    copyText: function (text) // Linebreaks with \n
    {
        var $tempInput = $("<textarea>");
        $("body").append($tempInput);
        $tempInput.val(text).select();
        document.execCommand("copy");
        $tempInput.remove();
    }
};


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
// // сохранить текущий фильтр
// $('.set-moderated').on('click', function (e) {
//     e.preventDefault();
//     var id = $(this).data('id');
//
//
//     $.ajax({
//         url: '/sale/set-moderated',
//         data: {id: id},
//         type: 'get',
//         success: function (res) {
//
//         },
//
//         error: function () {
//             alert('error')
//         }
//     });
//     this.disabled = true;
// });

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
$(document).on('click', '.remove-plus-tags', function (e) {

    console.log('remove');
    $.ajax({
        url: '/tags/remove-plus-tags',

        type: 'get',
        success: function (res) {
            $("#plus_searching_tags").val(res);
            $('.searching_tags_div').load(encodeURI('/tags/render-tags'));
        },

        error: function () {
            alert('error')
        }
    });
    this.disabled = true;
});
// добавление или удаление текущего tags для sale
$(document).on('click', '.remove-minus-tags', function (e) {

    $.ajax({
        url: '/tags/remove-minus-tags',

        type: 'get',
        success: function (res) {


            $("#minus_searching_tags").val(res);
            $('.searching_tags_div').load(encodeURI('/tags/render-tags'));


        },

        error: function () {
            alert('error')
        }
    });
    this.disabled = true;
});
// добавление или удаление текущего tags для sale
$('.copytags').on('click', function (e) {
    e.preventDefault();
    var id_from = $(this).data('id_from');
    var id_to = $(this).data('id_to');
    $.ajax({
        url: '/sale/copy-tags',
        data: {id_from: id_from, id_to: id_to},
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
            //  $("<i class='fa fa-check'  aria-hidden='true'>").insertBefore('#tag_address_' + tag_id);
            if ($('#tag_address_' + tag_id).hasClass('z-depth-5')) {
                $('#tag_address_' + tag_id).removeClass('z-depth-5')
            } else {
                $('#tag_address_' + tag_id).addClass('z-depth-5');

            }

        },

        error: function () {
            alert('error')
        }
    });
    this.disabled = true;
});
// добавление или удаление текущего tags для поиска sale
$('.tags-action-button-sale-search').on('click', function (e) {
    e.preventDefault();
    var tag_id = $(this).data('tag_id');

    $.ajax({
        url: '/real-tags/tag-to-search',
        data: {tag_id: tag_id},
        type: 'get',
        success: function (res) {
            $("<i class='fa fa-check' aria-hidden='true'>").insertBefore('#tag_' + tag_id);
            $("#searching_tags").val(res);

        },

        error: function () {
            alert('error')
        }
    });
    this.disabled = true;
});


// добавление или удаление текущего tags для address
$('.tags-action-button-address-all').on('click', function (e) {
    e.preventDefault();
    $.ajax({
        url: '/real-tags/tags-addresses',
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

// установка верного имени агента
$('.set-person-to-agent').on('click', function (e) {
    e.preventDefault();
    var id = $(this).data('id');
    var name = $(this).data('name');

    $.ajax({
        url: '/agents/set-true-person',
        data: {id: id, name: name},
        type: 'get',
        success: function (res) {
            toastr.success(res);
        },

        error: function () {
            alert('error')
        }
    });
    // this.disabled = true;
});
// добавление или удаление текущего tags для salelists
$('.tags-action-button-salelist').on('click', function (e) {
    e.preventDefault();
    var salelist_id = $(this).data('salelist_id');
    var tag_id = $(this).data('tag_id');

    $.ajax({
        url: '/real-tags/tag-sale-list',
        data: {salelist_id: salelist_id, tag_id: tag_id},
        type: 'get',
        success: function (res) {

        },

        error: function () {
            alert('error')
        }
    });
    this.disabled = true;
});
// добавление или удаление текущего tags для salelists
$('.trigger-similar-lists').on('click', function (e) {
    e.preventDefault();
    var salelist_id = $(this).data('salelist_id');
    var similar_id = $(this).data('similar_id');

    $.ajax({
        url: '/sale-lists/trigger-similar-list',
        data: {salelist_id: salelist_id, similar_id: similar_id},
        type: 'get',
        success: function (res) {

        },

        error: function () {
            alert('error')
        }
    });
    this.disabled = true;
});


$(".button-collapse").sideNav();
// SideNav Options

$('[data-toggle="tooltip"]').tooltip();


$('[data-toggle="popover"]').popover();


$('.popover-dismiss').popover({
    trigger: 'focus'
});

$(document).ready(function () {
    $('.mdb-select').material_select();
});

$('#input_starttime').pickatime({
    twelvehour: true
});

// $(document).on('click', '.change-statuses', function (e) {
//     var id = $(this).data('id');
//     var model = $(this).data('model');
//     var attrname = $(this).data('attrname');
//     var value = $(this).data('value');
//     $.ajax({
//         url: '/statuses/set',
//         data: {id: id, modelname: model, attrname: attrname, value: value},
//         type: 'get',
//         success: function (data) {
//             data = JSON.parse(data);
//             if (data['type'] == 'similar') {
//                 // toastr.success(data['message']);
//                 $(data['selector']).css('background-color', data['color']);
//             } else {
//                 $('#row_' + id).css('background-color', '#e8f5e9');
//                 toastr.success(data);
//             }
//
//         },
//     });
// });
$(document).on('click', '.change-statuses', function (e) {
    var id_parent = $(this).data('id_parent');
    var id_model = $(this).data('id_model');
    var id_attr = $(this).data('id_attr');
    var id_status = $(this).data('id_status');
    $.ajax({
        url: '/actions/change-status',
        data: {id_parent: id_parent, id_model: id_model, id_attr: id_attr, id_status: id_status},
        type: 'get',
        success: function (data) {
            console.log(data);
            // data = JSON.parse(data);
            // if (data['type'] == 'similar') {
            //     // toastr.success(data['message']);
            //     $(data['selector']).css('background-color', data['color']);
            // } else {
            //     $('#row_' + id).css('background-color', '#e8f5e9');
            //     toastr.success(data);
            // }

        },
    });
    $(this.firstChild).toggleClass('green-text');
});


$(document).on('click', '.toggle-action-lists', function (e) {
    var id_parent = $(this).data('id_parent');
    var id_model = $(this).data('id_model');
    var id_attr = $(this).data('id_attr');
    var id = $(this).data('id');
    $.ajax({
        url: '/actions/toggle',
        data: {id_parent: id_parent, id: id, id_model: id_model, id_attr: id_attr},
        type: 'get',
        success: function (data) {
            data = JSON.parse(data);
            if (data.toggleClass) {
                selectRow(data.selectorClass,data.toggleClass);
            }
            console.log(data);
        },

    });

    $(this.firstChild).toggleClass('green-text');

});

$(document).on('click', '.action', function (e) {
    var id_parent = $(this).data('id_parent');
    var id_model = $(this).data('id_model');
    var id_action = $(this).data('id_action');

    $.ajax({
        url: '/actions/action',
        data: {id_parent: id_parent, id_model: id_model, id_action: id_action},
        type: 'get',
        success: function (data) {
            console.log(data);
        },

    });

    $(this.firstChild).toggleClass('green-text');

});


$(document).on('click', '.on-control', function (e) {
    var id_salefilter = $(this).data('id');
    var id_item = $(this).data('id_item');
    var price = $(this).data('price');

    $.ajax({
        url: '/sale-filters/on-control',
        data: {id_salefilter: id_salefilter, id_item: id_item, price: price},
        type: 'get',
        success: function (data) {
            console.log(data);
        },

    });

    $(this.firstChild).toggleClass('green-text');

});

$('.mirs_preloader').on('click', function (e) {
    $('.mirs_preloader').removeClass('fa-search');
    $('.mirs_preloader').addClass('fa-refresh fa-spin');
});

// добавление или удаление текущего tags для sale
$('.tags-action-button').on('click', function (e) {
   // var trigger_class = 'animated pulse infinite z-depth-5';
    var trigger_class = 'border-tag z-depth-5';
    e.preventDefault();
    var parent_id = $(this).data('parent_id');
    var tag_id = $(this).data('tag_id');
    var type = $(this).data('type');
    var a_type = $(this).data('a_type');
    var selector = '.tag_' + type + '_' + parent_id + '_' + tag_id;
  //  console.log('type=' + type + ', parent_id =' + parent_id + ', tag_id=' + tag_id + ', a_type =' + a_type + ', selector = ' + selector);

    // изменение свойств
    if (a_type) {
           console.log('РАБОТАЕМ С A_TYpe');

        if ($("." + a_type).hasClass(trigger_class)) {
            if ((type != 'plus_search') && (a_type != 'minus_search')) {
                if (!$(selector).hasClass(trigger_class)) addClass = true; else addClass = false;
                $("." + a_type).removeClass(trigger_class);
                if (addClass) $(selector).addClass(trigger_class);
            } else {
                 console.log('РАБОТАЕМ С type=' + type);
                $(selector).toggleClass(trigger_class);
            }
        }
        else {
            $(selector).toggleClass(trigger_class);
        }


    } else $(selector).toggleClass(trigger_class);


    $.ajax({
        url: '/real-tags/tag',
        data: {parent_id: parent_id, tag_id: tag_id, type: type},
        type: 'get',
        success: function (res) {
            // toastr.success(res);
            if (type == 'plus_search') {
                $("#plus_searching_tags").val(res);
                $('.searching_tags_div').load(encodeURI('/tags/render-tags'));

            }
            if (type == 'minus_search') {
                $("#minus_searching_tags").val(res);
                $('.searching_tags_div').load(encodeURI('/tags/render-tags'));
            }


        }
        ,

        error: function () {
            alert('error')
        }
    });
    this.disabled = true;
});

function selectRow(selectorClass,toggledClass) {
    $(selectorClass).toggleClass(toggledClass);
}
