<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 04.12.2017
 * Time: 0:19
 */

namespace app\components;


use yii\helpers\Html;
use yii\web\View;


class Mdb
{
    public static function ActiveSelect($model, $attrName, $data, $options = [])
    {

        // инициализация
        $value = $model[$attrName];

        if (($options['label']) === false) $label = false;
        elseif ($options['label']) $label = $options['label'];
        else $label = $model->attributeLabels()[$attrName];
        $class = "mdb-select colorful-select dropdown-primary";
        // добавляем свойства если пришел class
        if (!empty($options['class'])) $class .= " " . $options['class'];


        $select_options = '';
        // если пришел multiple
        if ($options['multiple']) {
            $select_options .= "name=\"" . $attrName . "[]\"";
            $select_options .= " multiple";
        } else $select_options .= " name=\"" . $attrName . "\"";
        // если пришел id
        if ($options['id']) {
            $id = $options['id'];
        } else $id = substr(md5(rand()), 0, 10);
        $label_for = $id;
        $id = " id=" . $id . " ";
        // формируем непосредственно body

        $body = "<select class=\"" . $class . "\" " . $select_options . " " . $id . ">";
        if ($options['placeholder']) $body .= "<option value=\"\" disabled selected>" . $options['placeholder'] . "</option>";


        foreach ($data as $key => $option) {
            if (($options['multiple']) and (is_array($value)) and (!empty($value))) {
                if (in_array($key, $value)) $selected = " selected"; else $selected = '';
            } elseif ($key == $value) $selected = " selected";
            else $selected = '';

            $body .= "<option value=\"" . $key . "\"" . $selected . ">" . $option . "</option>";

        }
        $body .= "</select>";
        if ($label) $body .= "<label for='#" . $label_for . "'>" . $label . "</label>";

        return $body;
    }

    public static function Select($attrName, $label = '', $data, $options = [])
    {

        // инициализация
       // $body = "<div class=\"md-form " . $options['div_class'] . " \">";
        if (($options['label']) === false) $label = false;
        elseif ($options['label']) $label = $options['label'];
        $value = $_GET[$attrName];
        $class = "mdb-select colorful-select dropdown-primary";
        // добавляем свойства если пришел class
        if (!empty($options['class'])) $class .= " " . $options['class'];


        $select_options = '';
        // если пришел multiple
        if ($options['multiple']) {
            $select_options .= "name=\"" . $attrName . "[]\"";
            $select_options .= " multiple";
        } else $select_options .= " name=\"" . $attrName . "\"";
        // если пришел id
        if ($options['id']) {
            $id = $options['id'];
        } else $id = substr(md5(rand()), 0, 10);
        $label_for = $id;
        $id = " id=" . $id . "\"";
        // формируем непосредственно body

        $body .= "<select class=\"" . $class . "\" " . $select_options . " " . $id . ">";
        if ($options['placeholder']) $body .= "<option value=\"\" disabled selected>" . $options['placeholder'] . "</option>";


        foreach ($data as $key => $option) {
            if (($options['multiple']) and (is_array($value)) and (!empty($value))) {
                if (in_array($key, $value)) $selected = " selected"; else $selected = '';
            } elseif ($key == $value) $selected = " selected";
            else $selected = '';

            $body .= "<option value=\"" . $key . "\"" . $selected . ">" . $option . "</option>";

        }
        $body .= "</select>";
        if ($label) $body .= "<label for='#" . $label_for . "'>" . $label . "</label>";
       // $body .= "</div>";
        return $body;
    }

    public static function ActiveTextInput($model, $attrName, $options = [])
    {
        // инициализация
        $value = $model[$attrName];
        $label = $model->attributeLabels()[$attrName];
        $body = "<div class=\"md-form\">";

        if ($options['prefix']) $body .= $options['prefix'];
        $body .= "<input type=\"text\" name=\"" . $attrName . "\" " . $options['hidden'] . " " . $options['disabled'];
        if ($options['id']) {
            $id = $options['id'];
        } else $id = substr(md5(rand()), 0, 10);
        $label_for = $id;
        $id = " id=" . $id;

        if ($options['class']) {
            $class = "class = 'form-control " . $options['class'] . "'";
        }
        if (($options['label']) === false) $label = false;
        elseif ($options['label']) $label = $options['label'];
        else $label = $model->attributeLabels()[$attrName];

        $label = "<label for=\"" . $label_for . "\">" . $label . "</label>";
        if (($options['autocomplete'])) {
            $datas = json_encode($options['autocomplete'], false);
            $script = <<< JS
var data_$label_for = $datas;
$('#$label_for').mdb_autocomplete({
    data: data_$label_for
});
JS;
            \Yii::$app->view->registerJs($script, View::POS_READY);
        }

        $body .= " " . $id . " " . $class . " value=\"" . $value . "\">";
        $body .= $label . "</div>";
        return $body;
    }

    public static function TextInput($attrName, $options = [])
    {
        // инициализация
        $value = $_GET[$attrName];

        if ($options['label']) {
            $label = $options['label'];
        }
        $body = "<div class=\"md-form\">";

        if ($options['prefix']) $body .= $options['prefix'];
        $body .= "<input type=\"text\" name=\"" . $attrName . "\"";
        if ($options['id']) {
            $id = $options['id'];
        } else $id = substr(md5(rand()), 0, 10);
        $id = " id=" . $id;

        if ($options['class']) {
            $class = "form-control " . $options['class'];
        }
        if ($options['label']) $label = $options['label'];
        $label = "<label for=\"" . $id . "\">" . $label . "</label>";

        $body .= " " . $id . " " . $class . " value=" . $value . ">";
        $body .= $label . "</div>";
        return $body;
    }

    public static function Fa($name, $options = [])
    {
        $class = "fa fa-" . $name;
        if ($options['class']) {
            $class .= " " . $options['class'] . "";
        }
        if ($options['id']) {
            $id = " id=" . $options['id'];
        }
        if ($options['title']) {
            $title = " title='" . $options['title'] . "'";
        }


        return "<i class='" . $class . "' " . $id . " " . $title . "></i>";
    }


    public static function ModalBegin($options)
    {
        // инициализация
        if ($options['class']) {
            $class = $options['class'];

            $colored_classes = [
                0 => 'modal-success',
                1 => 'modal-info',
                2 => 'modal-danger',
                3 => 'modal-warning',
            ];
            foreach ($colored_classes as $colored_class) {
                if (preg_match("/" . $colored_class . "/", $class)) {
                    $class_close_button = "class = 'white-text'";
                    //  info("pregmatch" . $colored_class);
                    break;
                }


            }

        }
        if ($options['id']) {
            $id = $options['id'];
        } else $id = substr(md5(rand()), 0, 10);
        if ($options['header']) {
            $header_title = $options['header'];
        } else $header_title = '';


        $body = '';
        if ($options['button']['class']) {
            $array = preg_split("/\./", $options['button']['class']);

            $buttonClass = $options['button']['class'];
            $title = $options['button']['title'];
            if ($array[0] == 'span') {
                $body .= "<a data-toggle=\"modal\" data-target=\"#" . $id . "\"><span type=\"button\" class=\"" . $array[1] . "\" >" . $title . "</span></a>";
            } else $body .= "<a type=\"button\" class=\"" . $buttonClass . "\" data-toggle=\"modal\" data-target=\"#" . $id . "\">" . $title . "</a>";
        }


        $body .= "<div class=\"modal fade\" id=\"" . $id . "\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"exampleModalLabel\" aria-hidden=\"true\">
        <div class=\"modal-dialog " . $class . "\" role=\"document\">
        <div class=\"modal-content\">
            <div class=\"modal-header\">
                <p class=\"heading lead\" style=\"margin-bottom: 0px;\">" . $header_title . "</p>
                <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\">
                    <span aria-hidden=\"true\" " . $class_close_button . ">&times;</span>
                </button>
            </div>";
        return $body;

    }

    public static function ModalEnd()
    {

        return "</div></div></div>";
    }

    public static function ProgresBar($class = 'bg-danger', $now = 100, $from = '0', $to = 100)
    {
        return "<div class=\"progress\">
    <div class=\"progress-bar " . $class . "\" role=\"progressbar\" style=\"width: 100%\" aria-valuenow=\"" . $now . "\" aria-valuemin=\"" . $from . "\" aria-valuemax=\"" . $to . "\"></div>
</div>";


    }

    public static function Alert($message, $type)
    {
        return \Yii::$app->view->registerJs("toastr." . $type . "(" . $message . ")", View::POS_READY);
    }

    public static function ActiveCheckbox($model, $attrName, $options = [])
    {

        // инициализация
        $body = "<div class=\"form-group\">";
        if ($model[$attrName]) $checked = "checked";
        if (($options['label']) === false) $label = false;
        elseif ($options['label']) $label = $options['label'];
        else $label = $model->attributeLabels()[$attrName];
        // добавляем свойства если пришел class
        if (!empty($options['class'])) $class = " " . $options['class'];


        $select_options = '';
        // если пришел multiple
        $select_options .= " name=\"" . $attrName . "\"";
        // если пришел id
        if ($options['id']) {
            $id = $options['id'];
        } else $id = substr(md5(rand()), 0, 10);
        $label_for = $id;
        $id = " id=" . $id . "";

        $body .= " <input type=\"checkbox\" " . $id . " " . $class . " " . $select_options . " " . $checked . " >";
        if ($label) $body .= "<label for=\"" . $label_for . "\">" . $label . "</label>";
        $body .= "</div>";

        return $body;

    }

    public static function ActiveDatepicker($model, $attrName, $options = [])
    {
        $body = "<div class=\"md-form\">";
        if ($model[$attrName]) $value = date("j F, Y", $model[$attrName]);
        if (($options['label']) === false) $label = false;
        elseif ($options['label']) $label = $options['label'];
        else $label = $model->attributeLabels()[$attrName];
        if ($options['id']) {
            $id = $options['id'];
        } else $id = substr(md5(rand()), 0, 10);
        if ($options['placeholder']) {
            $placeholder = $options['placeholder'];
        } else $placeholder = '';

        // добавляем свойства если пришел class
        $class = "form-control datepicker ";
        if (!empty($options['class'])) $class .= $options['class'];
        $body .= "<input name='" . $attrName . "' placeholder='" . $placeholder . "' type='text' id='" . $id . "' class='" . $class . "' value='" . $value . "'>";
        if ($label) $body .= "<label for=" . $id . ">" . $label . "</label>";
        $body .= "</div>";
        return $body;
    }

    public static function Badge($text, $options = [])
    {

        return Html::tag('span', $text, ['class' => "badge badge-" . $options['class']]);
    }

}

