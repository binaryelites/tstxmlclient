<?php
//define("hostname", "http://localhost/tstnew/");
define("hostname", "http://travelshoptours.com/");
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

function array_to_xml($array, &$xml) {
    foreach($array as $key => $value) {
        if(is_array($value)) {
            if(!is_numeric($key)){
                $subnode = $xml->addChild("$key");
                array_to_xml($value, $subnode);
            }else{
                $subnode = $xml->addChild("item");
                array_to_xml($value, $subnode);
            }
        }else {
            $xml->addChild("$key",htmlspecialchars("$value"));
        }
    }
}

function my_xmlapi_output($array, $print = true){
    if(isset($array['success']) && is_bool($array['success'])){
        $array['success'] = ($array['success'] == true) ? 1 : 0;
    }

    // creating object of SimpleXMLElement
    $xml = new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"UTF-8\"?><root></root>");

    // function call to convert array to xml
    array_to_xml($array,$xml);

    if($print){
        header('Content-Type: text/xml');
        print $xml->asXML(); 
        die();
    }

    return $xml->asXML();        
}