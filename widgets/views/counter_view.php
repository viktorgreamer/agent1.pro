<div id="<?= $id; ?>"><?= $from; ?> </div>
<?php
$script = <<<JS
var id = '$id';
var to$id = $to;
var time$id = $time;
	$(function () { 
		var currentNumber$id = $('#' + id).text(); 
		var target_block$id = $('#' + id);
		var blockStatus$id = true;  
		$(window).scroll(function() { 
			var scrollEvent$id = ($(window).scrollTop() > (target_block$id.position().top - $(window).height())); 
			if(scrollEvent$id && blockStatus$id) {  
				blockStatus$id = false;
				$({numberValue: currentNumber$id}).animate({numberValue: to$id}, {
				    duration: time$id,
                    easing: 'linear',
                    step: function() { 
                        $('#' + id).text(Math.ceil(this.numberValue)); 
                    }
});
			} 
		}); 
	});
JS;
$this->registerJs($script, yii\web\View::POS_READY);
?>
