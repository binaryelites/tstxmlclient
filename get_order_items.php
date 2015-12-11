<?php
include("config.php");
$apiurl = hostname."api/xml/tours/get_order_items";

include "libraries/Requests.php";
Requests::register_autoloader();

$params['order_id'] = 45;
$params['item_id'] = 41;

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
        
        foreach($result->items->item as $v){
            echo "Order ID : {$v->Order_ID} <br />";
            echo "Status : {$v->Status_ID} <br />";
            echo "Item ID : {$v->Item_ID} <br />";
            echo "<hr />";
        }        
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



