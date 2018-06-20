<?php

if ($header == '') {
    $header = 'Заголовок';
    $modalDialogClass = "modal-side modal-bottom-right modal-notify modal-success";
    $body = "Lorem ipsum dolor sit amet, consectetur adipisicing elit. Impedit iusto nulla aperiam blanditiis ad consequatur in dolores culpa, dignissimos, eius non possimus fugiat. Esse ratione fuga, enim, ab officiis totam.";
    $triggerButton = ['class' => "btn btn-primary btn-lg",
        'body' => "тело"];
    $idModal = 'modalid';
    $modalFooter = [
        'class' => 'justify-content-center',
        'body' => "<div class=\"modal-footer justify-content-center\">
                        <a type=\"button\" class=\"btn btn-primary-modal waves-effect waves-light\">Get it now
                          </a>
                        <a type=\"button\" class=\"btn btn-outline-secondary-modal waves-effect\" data-dismiss=\"modal\">No,
                            thanks</a>
                    </div>"
    ];

}

?>
<?php if ($trigger['type'] == 'button') { ?>
    <!-- Button trigger modal -->
    <button type="button" class="<?= $trigger['class']; ?>" data-toggle="modal" data-target="#<?= $idModal; ?>">
        <?= $trigger['body']; ?>
    </button>
    <?php
}
?>


    <!-- Modal -->
    <div class="modal fade" id="<?= $idModal; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog <?= $modalDialogClass; ?>" role="document">
            <!--Content-->
            <div class="modal-content">
                <!--Header-->
                <div class="modal-header">
                    <p class="heading lead"> <?php echo $header; ?> </p>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="white-text">×</span>
                    </button>
                </div>

                <!--Body-->
                <div class="modal-body">
                    <div class="text-center">

                        <?= $body ?>
                    </div>
                </div>

                <!--Footer-->
                <?php if (!empty($modalFooter)) { ?>
                    <div class="modal-footer <?= $modalFooter['class']; ?>">
                        <?= $modalFooter['body']; ?>
                    </div>
                <? } ?>

            </div>
            <!--/.Content-->
        </div>
    </div>
    <!-- Modal -->


<?
if ($trigger['type'] == 'timeout') {
    $this->registerJs("setTimeout('$(\'#$idModal\').modal(\'show\');', ".$trigger['timeout'].");", \yii\web\View::POS_READY);
}