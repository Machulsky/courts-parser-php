<?php
include_once 'MultiCurl.class.php';


function findRequisitsLink($site){

	$requisits_url = '';
	$i = 1;

	$qVars = [
		'&id=20',
		'&rid=20',
		'&rid=',
		'&id='
	];

	while ($requisits_url === '' && $i <=400) {

		foreach ($qVars as $key => $value) {
			if(stristr($value, '20') === FALSE){
				$url = $site.'/modules.php?name=information'.$value.$i;
				
			}else{
				$url = $site.'/modules.php?name=information'.$value;
			}
			$parse = dlPage($url);
			$parse = mb_convert_encoding($parse, "utf-8", "windows-1251");
			if (!stristr($parse, 'ИНН') === FALSE && !stristr($parse, 'БИК') === FALSE && !stristr(strtolower($parse), 'пошлин') === FALSE){
				
				$requisits_url = $url;

			}
		}
			$i++;
	}
	if($requisits_url === ''){
		return FALSE;
	}

	return $requisits_url;
}


function findRequisitsLinkMT($site){

	$requisits_url = '';
	$i = 1;
	$qcheck = [
		'&id=20',
		'&rid=20'
	];

	$qVars = [
		
		'&rid=',
		'&id='
	];
	$urls = [];
foreach ($qcheck as $key => $value) {
				$urls[] = $site.'/modules.php?name=information'.$value;
		}

	while ($requisits_url === '' && $i <=400) {
		foreach ($qVars as $key => $value) {
			if($i != 20){
				$urls[] = $site.'/modules.php?name=information'.$value.$i;
			}
				
		}
			$i++;
	}
	try {
		$mc = new MyMultiCurl();
		$mc->setMaxSessions(2000); // limit 2 parallel sessions (by default 10)
		$mc->setMaxSize(5240); // limit 10 Kb per session (by default 10 Mb)
		$mc->addUrl($urls);

	} catch (Exception $e) {
		unset($mc);
		$array = explode(md5(5), $e->getMessage());
		
		return $array;

	}	
}

function findRequisitsLinkWords($site){

	$url = $site.'/modules.php?name=information';
	$out = '';
	$parse = dlPage($url);
	$parse = mb_convert_encoding($parse, "utf-8", "windows-1251");
	if (!stristr($parse, 'ИНН') === FALSE && !stristr($parse, 'БИК') === FALSE && !stristr(strtolower($parse), 'пошлин') === FALSE){
		return [$url, $parse];
	}

	

	$parse = explode("<a", $parse);

	foreach ($parse as $key => $value) {

		if(!stristr($value, 'пошлина') === FALSE){
			$value = str_replace("'", "", $value);
			$value = str_replace('"', '', $value);
			$parse = $value;

		}
	}

	$url = getBetween($parse, "href=", ">");
	$url = $site.$url;
	$parse = dlPage($url);

	$parse = mb_convert_encoding($parse, "utf-8", "windows-1251");

	if (!stristr($parse, 'ИНН') === FALSE && !stristr($parse, 'БИК') === FALSE && !stristr(strtolower($parse), 'пошлин') === FALSE){
		return $out = [$url, $parse];
	}

	$parse = explode("<a", $parse);

	foreach ($parse as $key => $value) {
		if((!stristr($value, 'еквизиты') === FALSE or 
			!stristr($value, 'ланк') === FALSE  or 
			!stristr($value, 'итанция') === FALSE or 
			!stristr($value, 'анковские') === FALSE) && 
			!stristr($value, 'ошлин') === FALSE

		){
			$value = str_replace("'", "", $value);
			$value = str_replace('"', '', $value);
			$parse[] = $value;
		}else{
			unset($parse[$key]);
		}
	}

	foreach ($parse as $key => $value) {
		$value = str_replace("'", "", $value);
			$value = str_replace('"', '', $value);
		$url = getBetween($value, "href=", ">");
		$url = $site.$url;
		$page = dlPage($url);
		$page = mb_convert_encoding($page, "utf-8", "windows-1251");
		
		if (!stristr($page, 'ИНН') === FALSE && !stristr($page, 'БИК') === FALSE && !stristr(strtolower($page), 'пошлин') === FALSE){
		return [$url, $page];
	}
	}
	return FALSE;
}

function findRequisits($site){
	$req_url = findReqFromCalc($site);

	if($req_url === FALSE)
	$req_url = findRequisitsLinkWords($site);

	if($req_url === FALSE){
		$req_url = findRequisitsLinkMT($site);
	}

	return $req_url;
}

function updateRequisitsUrls(){

global $db;
$st = $db->query("SELECT * FROM courts WHERE type = 1");
$courts = $st->fetchAll(); 
foreach($courts as $court){
	
	$req = findRequisits($court['site']);
	$requ_url = $req[0];
	$requ_html = str_replace("'", '"',$req[1]);
	
	$time = time();
	$st = $db->exec("UPDATE courts SET requ_url = '".$requ_url."', requ_html = '".$requ_html."', updated_at = '".$time."' WHERE code = '".$court['code']."'");
}

}

function getData($file){
	 $data = unserialize(file_get_contents($file));

 //$data = unserialize($data);
 return $data;
}


function findReqFromCalc($site){
$query = "/modules.php?name=govduty";
$url = $site.$query;

$data = dlPage($url);
$parse = strtolower($data);
if (!stristr($parse, 'inn') === FALSE && !stristr($parse, 'kpp') === FALSE && !stristr($parse, 'bik') === FALSE && !stristr($parse, '$(document).ready(function ()') === FALSE){
    return [$url, mb_convert_encoding($parse, "utf-8", "windows-1251")];
}else{
	return false;
}
}