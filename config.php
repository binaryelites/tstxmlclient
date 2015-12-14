<?php
define("hostname", "http://localhost/tstnew/");
include_once("libraries/Requests.php");
Requests::register_autoloader();
    
function get_xml_file($filename, $params = array()){
    extract($params);
    //var_dump($params);
    //echo "asdas - " + $user_id;
    ob_start();
    include($filename);
    $buffer = ob_get_contents();
    @ob_end_clean();
    @ob_end_flush();
    $buffer = '<?xml version="1.0" encoding="UTF-8" ?>'.$buffer;
    return $buffer;
}

function get_tour_info($tour_id){
    $apiurl = hostname."api/xml/tours/tour_info";

    $params['tour_id'] = $tour_id;    

    $payload = file_get_contents("buyer.xml");
    // Now let's make a request!
    $request = Requests::post($apiurl."?".http_build_query($params), array(), array('__payload__' => $payload));
    
    return simplexml_load_string($request->body);
}