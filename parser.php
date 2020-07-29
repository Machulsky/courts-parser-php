<?php


$db = new PDO('sqlite:'.__DIR__.DIRECTORY_SEPARATOR.'data.db', 'admin', 'admin',[PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
require 'app/functions.php';


$start_time = microtime();

$start_array = explode(" ",$start_time);

$start_time = $start_array[1] + $start_array[0];


 //$data = unserialize(file_get_contents('data/courts_main.db'));

 //$data = unserialize($data);

 // $bigData = getAllCourts();



//$data = findRequisits('http://giaginsky.adg.sudrf.ru');

 // $arr = getData('data/courts_main.db');

 // foreach ($arr[0]['courts'] as $key => $value) {
 // 	foreach ($value as $k => $v) {
 // 		if($k === 'site'){
 // 		$rUrl = findRequisits($v);
 // 		$value['requisits_url'] = $rUrl;
 // 	}

 // 	}

	
 // }

//$db->exec("DROP TABLE courts");
//  $db->exec("CREATE TABLE IF NOT EXISTS courts (
//                      id INTEGER PRIMARY KEY, 
//                      name TEXT, 
//                      code TEXT,
//                      site TEXT,
//                      type TEXT,
//                      phone TEXT,
//                      adress TEXT,
//                      email TEXT,
//                      dist TEXT,
//                      query TEXT,
//                     chief TEXT,
//                      region_id TEXT,
// 					 requ_url TEXT,
//                      updated_at INTEGER,
// 					 created_at INTEGER)");
//$st = $db->query("SELECT * FROM courts");
//$result = $st->fetchAll(); 

updateRequisitsUrls();
// $data = getData('array.last');

//   foreach ($data as $key => $value) {


//   	foreach ($value['courts'] as $k => $v) {

//   		$query = 'INSERT INTO courts (';
//   		$params = 'type,';
//   		$values = $value['court_type'].',';
//   		$del = ',';
//   		foreach ($v as $param => $val) {
//   			if(end($v) === $val){
//   				$del = '';
//   			}
//   			$params .= $param.$del;
//   			$values.= "'".$val."'".$del;
//   			$del = ',';
// 	 	}

//   		$query.=$params.") VALUES (".$values.");";
 	
//   		file_put_contents("last_query.sql", file_get_contents("last_query.sql")."\n".$query);
//   		$db->exec($query);
//   	}

 
//   }

//$requ = findRequisits($site);



$end_time = microtime();

$end_array = explode(" ",$end_time);

$end_time = $end_array[1] + $end_array[0];

$time = $end_time - $start_time;

printf("Скрипт выполнен за %f секунд\n", $time);

 //var_dump($requ);




