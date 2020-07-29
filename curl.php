<?php
include_once 'MultiCurl.class.php';
class MyMultiCurl extends MultiCurl {
    protected function onLoad($url, $content, $info) {
    	$content = mb_convert_encoding($content, "utf-8", "windows-1251");
    	if (!stristr($content, 'ИНН') === FALSE && !stristr($content, 'БИК') === FALSE && !stristr(strtolower($content), 'пошлин') === FALSE){
    		throw new Exception($url);
			}
       // print "[$url] $content ";
        //print_r($url);
    }

}

function findReqMulti($site){

}
 $start_time = microtime();

 $start_array = explode(" ",$start_time);

 $start_time = $start_array[1] + $start_array[0];

 $site = 'http://volodarsky.ast.sudrf.ru';
 $requisits_url = '';
 	$i = 1;

 	$qVars = [
 		'&rid=',
 		'&id='
 	];

 	while ($i <=1000) {
 		foreach ($qVars as $key => $value) {
 		 $urls[] = $site.'/modules.php?name=information'.$value.$i;
 		}
 		$i++;
	}


try {
    $mc = new MyMultiCurl();
    $mc->setMaxSessions(10); // limit 2 parallel sessions (by default 10)
    $mc->setMaxSize(10240); // limit 10 Kb per session (by default 10 Mb)

    $mc->addUrl($urls);
} catch (Exception $e) {
    return $e->getMessage();
}

// function multiRequest($urls){
// $multi = curl_multi_init();
// 	$handles = [];
// 	$htmls = [];

// 	foreach ($urls as $url) {
// 		$ch = curl_init( $url );
// 		curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
//     	curl_setopt($ch, CURLOPT_ENCODING,  '');
//     	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
//     	curl_setopt($ch, CURLOPT_HEADER, false);
//     	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
//     	curl_setopt($ch, CURLOPT_URL, $url);
//     	curl_setopt($ch, CURLOPT_REFERER, $url);
//     	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
//     	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/533.4 (KHTML, like Gecko) Chrome/5.0.375.125 Safari/533.4");
// 		curl_multi_add_handle($multi, $ch);
// 		$handles[$url] = $ch;
// 	}

// 	do{
// 		$mrc = curl_multi_exec($multi, $active);
// 	}while ( $mrc == CURLM_CALL_MULTI_PERFORM);

// 	while ($active && $mrc == CURLM_OK) {
// 		if(curl_multi_select($multi)== -1){
// 			usleep(100);
// 		}

// 		do{
// 			$mrc = curl_multi_exec($multi, $active);
// 		}while ($mrc == CURLM_CALL_MULTI_PERFORM);
// 		# code...
// 	}

// 	foreach ($handles as $channel) {
// 		$htmls[] = mb_convert_encoding(curl_multi_getcontent($channel), "utf-8", "windows-1251");
// 		curl_multi_remove_handle($multi, $channel);
// 	}

// 	curl_multi_close($multi);

// 	return $htmls;
// }


// $site = 'http://giaginsky.adg.sudrf.ru';
// $requisits_url = '';
// 	$i = 1;

// 	$qVars = [
// 		'&rid=',
// 		'&id='
// 	];

// 	while ($i <=1000) {
// 		foreach ($qVars as $key => $value) {
// 		 $urls[] = $site.'/modules.php?name=information'.$value.$i;
// 		}
// 		$i++;
// 	}
// 	$urls = array_chunk($urls, 5);

// foreach ($urls as $chunk) {
// 	$htmls[] = multiRequest($chunk);
// 	# code...
// }

// var_dump($htmls);


	
$end_time = microtime();

$end_array = explode(" ",$end_time);

$end_time = $end_array[1] + $end_array[0];

$time = $end_time - $start_time;

printf("Скрипт выполнен за %f секунд\n", $time);