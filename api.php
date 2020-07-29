<?php
require 'app/functions.php';

header('content-type: text/html; charset=utf-8');

$parse = file_get_contents('https://sudrf.ru/index.php?id=300&act=go_ms_search&searchtype=ms&var=true&ms_type=ms&court_subj=');

$parse = mb_convert_encoding($parse, "utf-8", "windows-1251");

$parse=explode("</script>", $parse);

$subjects = [];

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

ksort($newArray);
dd($subjects);



var_dump($subjects);
//var_dump($array);



