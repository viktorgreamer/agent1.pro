<?php
if (!isset($get)) $get = '';
?>

<div class="modal fade" id="modalContactForm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog cascading-modal" role="document">
        <!--Content-->
        <div class="modal-content">

            <!--Header-->
            <div class="modal-header light-blue darken-3 white-text">
                <h4 class="title"><i class="fa fa-pencil"></i>Контактная форма
                    <button type="button" class="close waves-effect waves-light" data-dismiss="modal"
                            aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
            </div>

            <form action="<?= \yii\helpers\Url::to(["web/" . $get]) ?>" method="post">
                <div class="modal-body mb-0">
                    <div class="md-form form-sm">
                        <i class="fa fa-user prefix"></i>
                        <input type="text" name="name" id="form19" class="form-control">
                        <label for="form19">Как к вам обращасться?</label>
                    </div>

                    <div class="md-form form-sm">
                        <i class="fa fa-phone prefix"></i>
                        <input type="text" id="form21" name="phone" class="form-control">
                        <label for="form21">Телефон</label>
                    </div>

                    <div class="md-form form-sm">
                        <i class="fa fa-pencil prefix"></i>
                        <textarea type="text" id="form8" name="description" class="md-textarea mb-0"></textarea>
                        <label for="form8">Описание вашей заявки</label>
                    </div>

                    <div class="text-center mt-1-half">
                        <button class="btn btn-info mb-2" type="submit">Оправить<i class="fa fa-send ml-1"></i>
                        </button>
                    </div>
                </div>
            </form>


        </div>
        <!--/.Content-->
    </div>
</div>