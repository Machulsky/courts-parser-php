<?php
require 'encode.php';
require 'requisits.php';
$court_types = [
1 => [
	'name' =>'Федеральные суды общей юрисдикции',
	'query' =>'https://sudrf.ru/index.php?id=300&act=go_search&searchtype=fs&court_name=&court_subj=<region_id>&court_type=0&court_okrug=0&vcourt_okrug=0',
	'regions_url' => 'https://sudrf.ru/index.php?id=300',
	'parse_type' => 1,
],
2 => [
	'name' =>'Мировые судьи',
	'query' =>'https://sudrf.ru/index.php?id=300&act=go_ms_search&searchtype=ms&var=true&ms_type=ms&court_subj=<region_id>',
	'parse_type' => 2,
],

3 => [
	'name' =>'Федеральные арбитражные суды',
	'query' =>'http://arbitr.ru/',
	'parse_type' => 3,
],

];

function checkPost($site){
	
}

function dd($var){
	die(var_dump($var));
}


function getBetween($string, $start, $end){
	if($end != ""){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}else{
   return str_replace($start, "", stristr($string, $start));;
}
}

function getRegions(){
	$parse = file_get_contents("https://sudrf.ru/index.php?id=300");
$parse = mb_convert_encoding($parse, "utf-8", "windows-1251");
$array = explode("<div", $parse);
foreach ($array as $key => $value) {
	if (!stristr($value, 'Субъект Российской Федерации:') === FALSE) {
		$parse = $value;
	}
}
$array = explode("<option", $parse);

foreach ($array as $key => $value) {
	if (!stristr($value, 'value') === FALSE) {
		$region_id = getBetween($value, "'","'");

		if(is_numeric($region_id ) && $region_id  != 0){
		$regions[] = $region_id;
	}
		
	}
}
return $regions;
}

function getAllGeneralCourts(){
	$count = 0;
	$regions = getRegions();
	$courts = [];
	foreach ($regions as $key => $value) {
		$arr = getCourts($value, 1);
		$courts[$value] = $arr['out'];
		$count += $arr['count'];
	}

	ksort($courts);
	$out = [];
	foreach ($courts as $key => $value) {
		foreach ($value as $k => $val) {
			$out[] = $val;
		}
		
	}

	return ['count' => $count, 'out' => $out];
}

function getArbitrageCourt($query){
	$parse = file_get_contents('http://arbitr.ru/'.$query);

$parse = mb_convert_encoding($parse, "utf-8", "windows-1251");

//$parse=explode("</script>", $parse);
$parse = strip_tags($parse);
$array = explode("Контакты", $parse);

$array = explode("\n", $array[1]);
$newArray = [];
$subjects = [];
foreach ($array as $key => $value) {
	$value = trim($value);
	if(isset($array[$key-1]) && !stristr($array[$key-1], 'Адрес:') === FALSE){
		$adress = $value;

	}elseif(isset($array[$key-1]) && !stristr($array[$key-1], 'МСК:') === FALSE){
		$diffMsk = $value;

	}elseif(isset($array[$key-1]) && !stristr($array[$key-1], 'суда:') === FALSE){
		$code = $value;
	}elseif(isset($array[$key-1]) && !stristr($array[$key-1], 'Телефон:') === FALSE){
		$phone = $value;
	}elseif(isset($array[$key-1]) && !stristr($array[$key-1], 'Факс:') === FALSE){
		$fax = $value;
	}elseif(isset($array[$key-1]) && !stristr($array[$key-1], 'mail:') === FALSE){
		$email = $value;
	}elseif(isset($array[$key-1]) && !stristr($array[$key-1], 'Сайт:') === FALSE){
		$site = $value;
	}
	elseif(isset($array[$key-1]) && !stristr($array[$key-1], 'Председатель:') === FALSE){
		$chief= $value;
		$subjects = [
			'code' => $code,
			'adress' => $adress,
			'phone' => $phone,
			'email' => $email,
			'site' => $site,
			'chief' => $chief

		];

	}
}



return $subjects;

}

function getArbitrageCourts(){
$parse = file_get_contents("http://arbitr.ru/");
$parse = mb_convert_encoding($parse, "utf-8", "windows-1251");
$array = explode("<div", $parse);
foreach ($array as $key => $value) {
	if (!stristr($value, 'Информация об арбитражных судах') === FALSE) {
		$parse = $value;
	}
}

$array = explode("<option", $parse);
foreach ($array as $key => $value) {
	if (!stristr($value, 'value') === FALSE && !stristr($value, '?') === FALSE) {
		$query = getBetween($value, '"','"');
		$data = getArbitrageCourt($query);
		$name = getBetween($value, '>', '<');
		$subjects[$key] = [
			'query' => $query,
			'name' => $name,
			

		];
		$subjects[$key] = array_merge($subjects[$key], $data);	
	}
}

return ['count' => count($subjects), 'out' => $subjects];
}

function getAllCourts(){
	global $court_types;
	$arbitrCourts = getArbitrageCourts();
	$generalJDCourts = getAllGeneralCourts();
	$smallCourts = getCourts(0,2);

	$globalData[] = [
		'court_type' => 1,
		'courts' => $generalJDCourts['out'],
		'count' =>$generalJDCourts['count']
	];
	$globalData[] = [
		'court_type' => 2,
		'courts' => $smallCourts['out'],
		'count' =>$smallCourts['count']
	];
	$globalData[] = [
		'court_type' => 3,
		'courts' => $arbitrCourts['out'],
		'count' =>$arbitrCourts['count']

	];
	$globalData['global_count'] = $generalJDCourts['count']+$smallCourts['count']+$arbitrCourts['count'];

	
	return $globalData;
}

function getCourts($region_id, $court_type){

	global $court_types;

	$url = $court_types[$court_type]['query'];
	$url = str_replace("<region_id>", $region_id, $url);
	$parse = file_get_contents($url);
	$parse = mb_convert_encoding($parse, "utf-8", "windows-1251");
	$parse=explode("</script>", $parse);
	$subjects = [];

switch ($court_types[$court_type]['parse_type']) {
	case 1:
		foreach ($parse as $key => $value) {
 	if(!stristr($value, 'Адрес') === FALSE) {
    $parse = strip_tags(str_replace("&nbsp;", "", $value));
	}
}

$array = explode("\n", $parse);



$i = 0;
foreach ($array as $key => $value) {
	$value = trim($value);
	

	$array[$key] = $value;

	if($value === ''){
		unset($array[$key]);
	
	}

	if(stristr($value, 'суд ') === FALSE && stristr($value, 'Адрес') === FALSE) {
    unset($array[$key]);

	}else if(!stristr($value, 'суд ') === FALSE){

		$name =  $value;

	}else if (!stristr($value, 'Адрес') === FALSE){
		//$value = str_replace(" ", "", $value);
		$subjects[$i] = [
			'region_id' => $region_id,
			'code' => getBetween(str_replace(" ", "", $value), "Классификационныйкод:", "Адрес"),
			'adress' => getBetween($value, "Адрес:", "Телефон"),
			'phone' => getBetween(str_replace(" ", "", $value), "Телефон:", "E-mail"),
			'email' => getBetween($value, "E-mail:", "Официальный сайт"),
			'site' => getBetween($value, "Официальный сайт:", "")	
		];

		if(isset($name)){
			$subjects[$i]['name'] = $name;
			$i++;
		}
	}

	
}
$count = count($subjects);
		break;
	case 2:
		foreach ($parse as $key => $value) {
 	if(!stristr($value, 'код:') === FALSE) {
    $parse = strip_tags(str_replace("&nbsp;", "", $value));
	}
}


$array = explode("\n", $parse);
$array = implode(":", $array);
$array = explode("\n", $parse);
$i = 0;
$newArray = [];
foreach ($array as $key => $value) {
	$value = trim($value);
	

	$newArray[$i] = $value;

	if($value === ''){

		unset($newArray[$key]);
			$i -=1;

	}



	$i++;
}

foreach ($newArray as $key => $value) {
	if (!stristr($value, 'участок') === FALSE) {
		$name = $value;

	}elseif (isset($newArray[$key+1])&&!stristr($newArray[$key+1], 'код:')=== FALSE) {

		$dist = $newArray[$key];
		$code = str_replace(" ", "", $newArray[$key+1]);

	}elseif (isset($newArray[$key+1])&&!stristr($newArray[$key-1], 'сайт:')=== FALSE) {
		$site = $newArray[$key];
		$subjects[] = [
			'name' => $name,
			'dist' => $dist,
			'code' => getBetween($code, 'код:', ''),
			'site' => $site
		];
	}
}
$count = count($subjects);
		break;
	case 3:
		$subjects = getArbitrageCourts();
		$count = $subjects['count'];
		break;
	
	default:
		# code...
		break;
}
	

 return ['count' => $count, 'out' => $subjects];
}



