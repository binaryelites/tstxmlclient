<?php
include("config.php");
$apiurl = hostname."api/xml/tours/get_order_info";

include "libraries/Requests.php";
Requests::register_autoloader();

$params['order_id'] = $_GET['order_id'];

$payload = file_get_contents("buyer.xml"); 
        
// Now let's make a request!
$request = Requests::post($apiurl."?".  http_build_query($params), array(), array('__payload__' => $payload));
//echo "<pre>";
?>
<div style="width: 100%">
    <div style="width: 1024px; margin: 0 auto">    
<?php
// Check what we received
try {
    $result = simplexml_load_string($request->body);
    if((int)$result->success == 0){
        echo "Success : false<br />";
        echo "Message : {$result->msg} <br />";
    }else {
        echo "Success : true<br />";
        echo "Order ID : {$result->order->ID} <br />";
        echo "Status : {$result->order->Status_ID} <br />";
    }    
}
catch (Exception $ex) {
     var_dump($ex->getMessage());
}
?>
        <h3>Response from server</h3>
        <textarea style="width: 100%;" rows="10"><?=$request->body?></textarea>
    </div>
</div>



