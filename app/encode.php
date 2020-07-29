<?php
function encodeBigData($bigData){
    $prefix = '';
    $data ='{';
    foreach($bigData as $key => $val) {

    $data.= $prefix.json_encode($key).':'.json_encode($val);
    $prefix = ','; 
}
    $data.='}';
    
    return $data;
}

function dlPage($href) {

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
    curl_setopt($curl, CURLOPT_ENCODING,  '');
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curl, CURLOPT_URL, $href);
    curl_setopt($curl, CURLOPT_REFERER, $href);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/533.4 (KHTML, like Gecko) Chrome/5.0.375.125 Safari/533.4");
    $str = curl_exec($curl);
    curl_close($curl);

    // Create a DOM object

    // Load HTML from a string
 

    return  $str;
    }

    function curlPost($href, $params) {

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
        curl_setopt($curl, CURLOPT_ENCODING,  '');
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_URL, $href);
        curl_setopt($curl, CURLOPT_REFERER, $href);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
           serialize($params));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/533.4 (KHTML, like Gecko) Chrome/5.0.375.125 Safari/533.4");
        $str = curl_exec($curl);
        curl_close($curl);
    
        // Create a DOM object
    
        // Load HTML from a string
     
    
        return  $str;
        }

 function jsonEncode($arr) {
    $str = '{';
    $count = count($arr);
    $current = 0;

    foreach ($arr as $key => $value) {
        $str .= sprintf('"%s":', sanitizeForJSON($key));

        if (is_array($value)) {
            $str .= '[';
            foreach ($value as &$val) {
                $val = sanitizeForJSON($val);
            }
            $str .= '"' . implode('","', $value) . '"';
            $str .= ']';
        } else {
            $str .= sprintf('"%s"', sanitizeForJSON($value));
        }

        $current ++;
        if ($current < $count) {
            $str .= ',';
        }
    }

    $str.= '}';

    return $str;
}

/**
 * @param string $str
 * @return string
 */
 function sanitizeForJSON($str)
{
    // Strip all slashes:
    $str = stripslashes($str);

    // Only escape backslashes:
    $str = str_replace('"', '\"', $str);

    return $str;
}