<?php
function my_var_dump($array)
{
    echo " <pre>";
    print_r($array);
    echo " </pre>";
}


function rus2translit($string) {
    $converter = array(
        'а' => 'a',   'б' => 'b',   'в' => 'v',
        'г' => 'g',   'д' => 'd',   'е' => 'e',
        'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
        'и' => 'i',   'й' => 'y',   'к' => 'k',
        'л' => 'l',   'м' => 'm',   'н' => 'n',
        'о' => 'o',   'п' => 'p',   'р' => 'r',
        'с' => 's',   'т' => 't',   'у' => 'u',
        'ф' => 'f',   'х' => 'h',   'ц' => 'c',
        'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
        'ь' => '\'',  'ы' => 'y',   'ъ' => '\'',
        'э' => 'e',   'ю' => 'yu',  'я' => 'ya',

        'А' => 'A',   'Б' => 'B',   'В' => 'V',
        'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
        'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',
        'И' => 'I',   'Й' => 'Y',   'К' => 'K',
        'Л' => 'L',   'М' => 'M',   'Н' => 'N',
        'О' => 'O',   'П' => 'P',   'Р' => 'R',
        'С' => 'S',   'Т' => 'T',   'У' => 'U',
        'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
        'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',
        'Ь' => '\'',  'Ы' => 'Y',   'Ъ' => '\'',
        'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
    );
    return strtr($string, $converter);
}
function str2url($str) {
    // переводим в транслит
    $str = rus2translit($str);
    // в нижний регистр
    $str = strtolower($str);
    // заменям все ненужное нам на "-"
    $str = preg_replace('~[^-a-z0-9_]+~u', '-', $str);
    // удаляем начальные и конечные '-'
    $str = trim($str, "-");
    return $str;
}

function mb_ucfirst($str, $encoding='UTF-8')
{
    $str = mb_ereg_replace('^[\ ]+', '', $str);
    $str = mb_strtoupper(mb_substr($str, 0, 1, $encoding), $encoding).
        mb_substr($str, 1, mb_strlen($str), $encoding);
    return $str;
}


function span($message, $type = '')
{
    if (in_array($type, ['success', 'primary', 'danger', 'info', 'warning', 'black'])) {
        return "<span class='badge badge-".$type." h5-responsive'>" . $message . "</span>";
    } else return "<span class='badge badge-default h5-responsive'>" . $message . "</span>";
}

function info($message = "Hello", $type = '')
{
    switch ($type) {
        case 'success': {
            echo "<h4><span class='badge badge-success'>" . $message . "</span></h4>";
            break;
        }
        case 'alert': {
            echo "<h4><span class='badge badge-danger'>" . $message . "</span></h4>";
            break;
        }
        case 'danger': {
            echo "<h4><span class='badge badge-danger'>" . $message . "</span></h4>";
            break;
        }
        case 'info': {
            echo "<h4><span class='badge badge-info'>" . $message . "</span></h4>";
            break;
        }
        case 'primary': {
            echo "<h4><span class='badge badge-primary'>" . $message . "</span></h4>";
            break;
        }

        default: {
            echo "<h4><span class='badge badge-default'>" . $message . "</span></h4>";
            break;
        }
    }


}

function my_implode($array)
{
    if ($array) {
        if (count($array) > 1) {
            return implode(",", $array);
        } else return $array[0];
    }
}


function my_transliterate($string, $gost = false)
{
    if ($gost) {
        $replace = array("А" => "A", "а" => "a", "Б" => "B", "б" => "b", "В" => "V", "в" => "v", "Г" => "G", "г" => "g", "Д" => "D", "д" => "d",
            "Е" => "E", "е" => "e", "Ё" => "E", "ё" => "e", "Ж" => "Zh", "ж" => "zh", "З" => "Z", "з" => "z", "И" => "I", "и" => "i",
            "Й" => "I", "й" => "i", "К" => "K", "к" => "k", "Л" => "L", "л" => "l", "М" => "M", "м" => "m", "Н" => "N", "н" => "n", "О" => "O", "о" => "o",
            "П" => "P", "п" => "p", "Р" => "R", "р" => "r", "С" => "S", "с" => "s", "Т" => "T", "т" => "t", "У" => "U", "у" => "u", "Ф" => "F", "ф" => "f",
            "Х" => "Kh", "х" => "kh", "Ц" => "Tc", "ц" => "tc", "Ч" => "Ch", "ч" => "ch", "Ш" => "Sh", "ш" => "sh", "Щ" => "Shch", "щ" => "shch",
            "Ы" => "Y", "ы" => "y", "Э" => "E", "э" => "e", "Ю" => "Iu", "ю" => "iu", "Я" => "Ia", "я" => "ia", "ъ" => "", "ь" => "");
    } else {
        $arStrES = array("ае", "уе", "ое", "ые", "ие", "эе", "яе", "юе", "ёе", "ее", "ье", "ъе", "ый", "ий");
        $arStrOS = array("аё", "уё", "оё", "ыё", "иё", "эё", "яё", "юё", "ёё", "её", "ьё", "ъё", "ый", "ий");
        $arStrRS = array("а$", "у$", "о$", "ы$", "и$", "э$", "я$", "ю$", "ё$", "е$", "ь$", "ъ$", "@", "@");

        $replace = array("А" => "A", "а" => "a", "Б" => "B", "б" => "b", "В" => "V", "в" => "v", "Г" => "G", "г" => "g", "Д" => "D", "д" => "d",
            "Е" => "Ye", "е" => "e", "Ё" => "Ye", "ё" => "e", "Ж" => "Zh", "ж" => "zh", "З" => "Z", "з" => "z", "И" => "I", "и" => "i",
            "Й" => "Y", "й" => "y", "К" => "K", "к" => "k", "Л" => "L", "л" => "l", "М" => "M", "м" => "m", "Н" => "N", "н" => "n",
            "О" => "O", "о" => "o", "П" => "P", "п" => "p", "Р" => "R", "р" => "r", "С" => "S", "с" => "s", "Т" => "T", "т" => "t",
            "У" => "U", "у" => "u", "Ф" => "F", "ф" => "f", "Х" => "Kh", "х" => "kh", "Ц" => "Ts", "ц" => "ts", "Ч" => "Ch", "ч" => "ch",
            "Ш" => "Sh", "ш" => "sh", "Щ" => "Shch", "щ" => "shch", "Ъ" => "", "ъ" => "", "Ы" => "Y", "ы" => "y", "Ь" => "", "ь" => "",
            "Э" => "E", "э" => "e", "Ю" => "Yu", "ю" => "yu", "Я" => "Ya", "я" => "ya", "@" => "y", "$" => "ye");

        $string = str_replace($arStrES, $arStrRS, $string);
        $string = str_replace($arStrOS, $arStrRS, $string);
        $string = str_replace('-', '', $string);
        $string = str_replace(' ', '', $string);
    }

    return iconv("UTF-8", "UTF-8//IGNORE", strtr($string, $replace));
}

// просто функции не объектно ориентированные

// парсинг даты
function parsing_date($date)
{
    $today = getdate();
    $year = $today['year'];
    $mon = $today['mon'];
    $day = $today['mday'];
    $hour = 0;
    $min = 0;
    $sec = 0;

    // Удаляем теги
    $date = strip_tags($date);
    // Удаляем пробелы в начале и конце строки
    $date = trim($date);
    $date = str_replace('Размещено ', ' ', $date);
    $date = str_replace(array("Сегодня", "сегодня"), date("d.m.Y"), $date);
    $date = str_replace(' в ', ' ', $date);


    if (
        strpos($date, '.') !== false ||
        strpos($date, '-') !== false ||
        strpos($date, '/') !== false
    ) {    // Цифровой формат даты
        $datetime_mas = explode(' ', $date);
        if (isset($datetime_mas[0]) && $datetime_mas[0] != '') {
            if (strpos($date, '.') !== false) $razd = '.';
            if (strpos($date, '-') !== false) $razd = '-';
            if (strpos($date, '/') !== false) $razd = '/';

            $date_mas = explode($razd, $datetime_mas[0]);

            if ($date_mas[0] > 0 && $date_mas[0] < 100) { // 16-04-2016 02:03:50
                if ($date_mas[0] > 0) $day = $date_mas[0];
                if ($date_mas[1] > 0) $mon = $date_mas[1];
                if (isset($date_mas[2]) && $date_mas[2] > 1000) $year = $date_mas[2];
            } elseif ($date_mas[0] > 100) { //2016-04-16 02:03:50
                if (isset($date_mas[0]) && $date_mas[0] > 1000) $year = $date_mas[0];
                if ($date_mas[1] > 0) $mon = $date_mas[1];
                if ($date_mas[2] > 0) $day = $date_mas[2];
            } else {
                return 0;
            }
        }

        if (isset($datetime_mas[1]) && $datetime_mas[1] != '') {
            $time_arr = explode(':', $datetime_mas[1]);
            if (isset($time_arr[0])) $hour = (int)$time_arr[0];
            if (isset($time_arr[1])) $min = (int)$time_arr[1];
            if (isset($time_arr[2])) $sec = (int)$time_arr[2];
        }


    } else {   // Текстовый формат даты
        $date_mas = explode(' ', $date);

        if ($date_mas[0] > 0) $day = $date_mas[0];
        if (isset($date_mas[2]) && $date_mas[2] > 1000 && strpos($date_mas[2], ':') === false) {
            $year = $date_mas[2];
        } else {
            $year = (int)date("Y");
        }
        if ($date_mas[1] != '') {
            $mon = parsing_date_mounth($date_mas[1]);
        }
        //print "МЕСЯЦ: ".$mon.' COUNT '.count($date_mas[1])."<br>";
        // Проверяем время
        $datetime = false;
        if (strpos($date_mas[2], ':') !== false) $datetime = $date_mas[2];
        if (strpos($date_mas[3], ':') !== false) $datetime = $date_mas[3];
        if ($datetime !== false) {
            $time_arr = explode(':', $datetime);
            if (isset($time_arr[0])) $hour = (int)$time_arr[0];
            if (isset($time_arr[1])) $min = (int)$time_arr[1];
            if (isset($time_arr[2])) $sec = (int)$time_arr[2];
        }
    }

    return mktime($hour, $min, $sec, $mon, $day, $year);
}


function parsing_phone($phone, $phone_code = '495')
{
    $phone = strip_tags($phone);
    $phone = str_replace(' ', '', $phone);
    $phone = str_replace('-', '', $phone);
    $phone = str_replace('(', '', $phone);
    $phone = str_replace(')', '', $phone);
    $phone = str_replace(' ', '', $phone);
    $phone = str_replace('+7', '8', $phone);
    $phone = substr($phone, 0, 11);
    $strlen = strlen($phone);
    $strlen_phone_code = strlen($phone_code);
    if ($strlen == (10 - $strlen_phone_code)) $phone = '8' . $phone_code . $phone;
    if ($strlen == 10) $phone = '8' . $phone;
    if ($strlen == 11 && $phone[0] != '8') $phone[0] = 8;
    $strlen = strlen($phone);
    if ($strlen > 11) $phone = '';
    if ($strlen < 11) $phone = '';
    return trim($phone);
}

function parsing_id_resourse($source)
{
    if (isset($source) && $source != '') {
        switch ($source) {
            case  'avito.ru':
                return 3;

            case  'realty.yandex.ru':
                return 2;

            case  'irr.ru':
                return 1;

            case  'cian.ru':
                return 5;

            case  'youla.io':
                return 4;

            default:
                return 6;

        }

    }
}

function render_id_resourse($source)
{

    switch ($source) {
        case  3;
            return 'avito.ru';

        case  2;
            return 'yandex.ru';

        case  1;
            return 'irr.ru';

        case  5;

            return 'cian.ru';

        case  4;
            return 'youla.io';

        default:
            return 6;

    }


}

function parsing_floorcounts($title)
{
    preg_match_all('/(.?)([0-9]{1,2})\/([0-9]{1,2})(.*)/i', $title, $math_data);

    if (isset($math_data[3][0]) && $math_data[3][0] > 0) {
        return (int)$math_data[3][0];
    } else return 0;
}

function parsing_grossarea($string)
{

    preg_match_all('/(.?)([0-9]{2,7})([,.0-9]{0,4})(м|кв\.м|сот|га)(.*)/i', str_replace(' ', '', $string), $math_data);

    if (isset($math_data[2][0]) && $math_data[2][0] > 0) {
        if (isset($math_data[3][0]) && $math_data[3][0] != '') {
            $math_data[3][0] = str_replace(array(',', '.'), '', $math_data[3][0]);
            $math_data[2][0] = (float)$math_data[2][0] . '.' . $math_data[3][0];
        }
        if (isset($math_data[4][0]) && $math_data[4][0] == 'га') {
            $math_data[2][0] = $math_data[2][0] * 10;
        }

        return (int)round($math_data[2][0]);
    } else return 0;
}

function rooms_count($string)
{

    preg_match_all('/(.*)([1-9])(-к | к |к |-ком|ком| ком)(.*)/i', $string, $math_data);
    if (isset($math_data[2][0]) && $math_data[2][0] > 0) {
        $otvet = (int)$math_data[2][0];

    } else {
        if (preg_match('/Студия/', $string)) $otvet = 20;
    }
    return $otvet;
}

function parsing_floors($title)
{
    preg_match_all('/(.?)([0-9]{1,2})\/([0-9]{1,2})(.*)/i', $title, $math_data);

    if (isset($math_data[2][0]) && $math_data[2][0] > 0) {
        return $math_data[2][0];
    } else return 0;
}


// парсим тип дома
function parsing_house_type($str)
{

    switch ($str) {
        case "Кирпичный": {

            return 2;
        }
        case "кирпичный": {

            return 2;
        }


        case "Панельный": {

            return 1;
        }
        case "панельный": {

            return 1;
        }


        case "Монолитный": {

            return 3;
        }
        case "монолитный": {

            return 3;
        }


        case "Блочный": {

            return 4;
        }
        case "блочный": {

            return 4;
        }


        case "Деревянный": {

            return 5;
        }
        case "деревянный": {

            return 5;
        }


        default: {

            return 0;
        }


    }
}

function preg_match_house_type($str)
{
    preg_match("/ирпич|анельн|онолитн|еревян|лочн/", $str, $output_array);
    switch ($output_array[0]) {
        case "ирпич": {

            return 2;
        }
        case "анельн": {

            return 1;
        }
        case "онолитн": {

            return 3;
        }


        case "лочн": {

            return 4;
        }


        case "еревян": {

            return 5;
        }


        default: {

            return 0;
        }


    }
}

function isDomainAvailible($domain)

{

    //Проверка на правильность URL

    if (!filter_var($domain, FILTER_VALIDATE_URL)) {

        return false;

    }


    //Инициализация curl

    $curlInit = curl_init($domain);

    curl_setopt($curlInit, CURLOPT_CONNECTTIMEOUT, 10);

    curl_setopt($curlInit, CURLOPT_HEADER, true);

    curl_setopt($curlInit, CURLOPT_NOBODY, true);

    curl_setopt($curlInit, CURLOPT_RETURNTRANSFER, true);


    //Получаем ответ

    $response = curl_exec($curlInit);


    curl_close($curlInit);


    if ($response) return true;


    return false;

}

function request($url, $postdata = null, $cookiefile = 'tmp/cookie.txt')
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $uagent = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2785.116 Safari/537.36";
    curl_setopt($ch, CURLOPT_USERAGENT, $uagent);
    if ($postdata) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);

    }
    $html = curl_exec($ch);
    curl_close($ch);
    return $html;


}

function date_to_unix($string_date)
{
    $array_of_month_rus = ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'];
    $array_of_month_eng = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

    date_default_timezone_set('Europe/Moscow');


    foreach ($array_of_month_rus as $month) {
        if (stripos($string_date, $month)) {
            $string_date_eng = str_replace($month, $array_of_month_eng[array_search($month, $array_of_month_rus)], $string_date);
            echo $string_date_eng;
        }

    }
    echo "<br>";
    return strtotime($string_date_eng);


}


function dlPage3($href, $proxy, $ref)
{
    $header = array();
    $header[] = "Host: www.avito.ru";
    $header[] = "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64; rv:54.0) Gecko/20100101 Firefox/54.0";
    $header[] = "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8";
    $header[] = "Accept-Language: ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3";
    $header[] = "Accept-Encoding: gzip, deflate, br";
    $header[] = "Accept: */*";
    $header[] = "Connection: keep-alive";
    $header[] = "DNT: 1";
    $header[] = "Referer: $ref";

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 90);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
    curl_setopt($curl, CURLOPT_PROXY, $proxy);
    curl_setopt($curl, CURLOPT_COOKIEJAR, 'cookie2.txt');
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curl, CURLOPT_URL, $href);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $str = curl_exec($curl);
    curl_close($curl);
    //$dom = new simple_html_dom();
    // $dom->load(gzdecode($str));
    return $str;
}


function dlPage4($href, $proxy, $ref)
{
    $header = array();
    $header[] = "Host: www.avito.ru";
    $header[] = "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64; rv:54.0) Gecko/20100101 Firefox/54.0";
    $header[] = "Accept: */*";
    $header[] = "Accept-Language: ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3";
    $header[] = "Accept-Encoding: gzip, deflate, br";
    $header[] = "Origin: https://www.avito.ru";
    $header[] = "Referer: $ref";
    $header[] = "Connection: keep-alive";
    $header[] = "Cache-Control: max-age=0";

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 90);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
    curl_setopt($curl, CURLOPT_PROXY, $proxy);
    curl_setopt($curl, CURLOPT_COOKIEFILE, 'cookie2.txt');

    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curl, CURLOPT_URL, $href);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $str = curl_exec($curl);
    curl_close($curl);
    // $dom = new simple_html_dom();
    //  $dom->load(gzdecode($str));
    return $str;
}


function curl_response($url, $proxy = '', $ref = '')
{

    $header = [];
    $header[] = "Host: www.avito.ru";
    $header[] = "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64; rv:54.0) Gecko/20100101 Firefox/54.0";
    $header[] = "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8";
    $header[] = "Accept-Language: ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3";
    $header[] = "Accept-Encoding: gzip, deflate, br";
    $header[] = "Accept: */*";
    $header[] = "Connection: keep-alive";
    $header[] = "DNT: 1";
    $header[] = "Referer: $ref";

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 90);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
    curl_setopt($curl, CURLOPT_PROXY, $proxy);
    curl_setopt($curl, CURLOPT_COOKIEJAR, 'cookie2.txt');
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = gzdecode(curl_exec($curl));
    curl_close($curl);
    //  echo $response;
    return $response;
}

function my_curl_response($url, $proxy = '', $ref = '', $host = '')
{

    $ch = curl_init($url);
//Установка опций
    $uagent = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2785.116 Safari/537.36";
    curl_setopt($ch, CURLOPT_USERAGENT, $uagent);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_REFERER, $ref);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
//выполнение (результат отобразится на экран)
    $html = curl_exec($ch);
//Закрытие соединения
    curl_close($ch);

    return $html;
}


function isPointInsidePolygon($polygon = [], $point = [])
{

    $count_cross = 0;

    $lines = [];
    // echo "<pre>";

    //var_dump($point);
    //  echo "</pre>";
    $lines = polygonToLines($polygon);

    foreach ($lines as $line) {
        if (intersectionTest($line, $point)) {
            // echo " есть!!!! пересечения";
            $count_cross++;
        }  //echo " НЕТ!! пересечения";
    }

    if ($count_cross % 2 == 1) {
        return true;
    } else {
        return false;
    }

}

function polygonToLines($polygon)
{
    $len = count($polygon);
    $lines = [];
    for ($i = 1; $i < $len; $i++) {
        $lines[] = [$polygon[$i - 1], $polygon[$i]];
    }
    return $lines;
}

function intersectionTest($line = [], $point = [])
{

    $x = $point[0];
    $y = $point[1];
    $x1 = $line[0][0];
    $y1 = $line[0][1];
    $x2 = $line[1][0];
    $y2 = $line[1][1];

    if ($y2 == $y1) {
        return false;
    }

    $x_cross = (($x2 - $x1) / ($y2 - $y1)) * ($y - $y1) + $x1;

    if ($x_cross > min([$x1, $x2]) && $x_cross < max([$x1, $x2]) && $x_cross > $x) {
        return true;
    } else {

        return false;

    }
}

function polygotRect($polygon)
{
    $x = [];
    $y = [];
    foreach ($polygon as $point) {
        $x[] = $point[0];
        $y[] = $point[1];
    }
    return [[min($x), min($y)], [max($x), max($y)]];
}



