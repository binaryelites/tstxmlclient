<?php
include("config.php");
$apiurl = hostname."api/xml/tours/save_order_item";

include "libraries/Requests.php";
Requests::register_autoloader();

$payload = $_POST['__payload__']; //file_get_contents("save_order_item.xml"); 
//$payload = file_get_contents("travelers_details.xml"); 
//var_dump($payload); die();
// Now let's make a request!
$request = Requests::post($apiurl, array(), array('__payload__' => $payload));
//echo "<pre>";
?>
<div style="width: 100%">
    <div style="width: 1024px; margin: 0 auto">    
<?php
// Check what we received
try {
    $result = simplexml_load_string($request->body);
    if((int)$result->success == 0){
        echo "Success : false <br />";
        echo "Message : ".$result->msg;
    }
    else {
        echo "Success : true <br />";
        echo "Item ID : ".$result->items->item->ID."<br />";
        echo "Order ID : ".$result->items->item->Order_ID."<br />";
        ?>        
        <h2>Response from server</h2>        
        <textarea style="width: 100%; height: 400px" rows="10"><?=$request->body?></textarea>
        <?php
    }
}
catch (Exception $ex) {
     var_dump($ex->getMessage());
}
?>
        <h3>Response from server will always be printed</h3>
        <textarea style="width: 100%; height: 400px" rows="10"><?=$request->body?></textarea>
    </div>
</div>



