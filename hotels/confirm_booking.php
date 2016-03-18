<?php
include("../config.php");

$payload = $_POST['payload'];

$apiurl = hostname . "api/xml/hotels/save_order_item";

// Now let's make a request!
$request = Requests::post($apiurl, array(), array('__payload__' => $payload));

try {    
    $result = simplexml_load_string($request->body);
    echo json_encode($result);
}
catch(Exception $ex){
    var_dump($ex->getMessage());
}

?>
