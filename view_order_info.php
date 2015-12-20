<?php
function get_order_xml($status, $order_id){
    $orderxml = "<root>
    <user_id>1</user_id>
    <api_key>123</api_key>
    <api_pass>123</api_pass>               
    <order_id>{$order_id}</order_id>    <!-- optional : if present then order will be upated -->
    <status>$status</status> <!-- pending payment, completed -->   
</root>";
    return $orderxml;
}

include("config.php");
$apiurl = hostname."api/xml/tours/get_order_info";

$payload = file_get_contents("buyer.xml"); 

$action = isset($_GET['action']) ? $_GET['action'] : false;

$actionPayload = null;
$actionMsg = "";
$request1 = "";
//if action then do it
if(strtolower($action) == "completed" || strtolower($action) == "canceled"){
    $actionUrl = hostname."api/xml/tours/save_order";
    
    $actionPayload = get_order_xml(strtolower($action), $_GET['order_id']);
    
    $request1 = $request = Requests::post($actionUrl, array(), array('__payload__' => $actionPayload));
    
    $result = simplexml_load_string($request->body);
    if($result->success == 0):
        die($result->msg);
    endif;
    
    $actionMsg = "Order status set to $action";
}

$request = Requests::post($apiurl."?order_id=".$_GET['order_id'], array(), array('__payload__' => $payload));

$result = simplexml_load_string($request->body);
if($result->success == 0):
    die("no order info found");
endif;

$orderInfo = $result->order;

$apiurl = hostname."api/xml/tours/get_order_items";
$request = Requests::post($apiurl."?order_id=".$_GET['order_id'], array(), array('__payload__' => $payload));

$items = false;
$result = simplexml_load_string($request->body);
if($result->success == 1):
    $items = $result->items->item;    
endif;
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <style>
            label {display: block; margin-bottom: 10px};
        </style>
    </head>
    <body>
        <div style="width: 1024px; margin: 0 auto">
            <?php
            include 'menu.php';
            if($actionMsg != ""):
            ?>
            <h1 style="color: greenyellow"><?=$actionMsg?></h1>
            <?php
            endif;
            ?>
        
            <h3>
                Order Info
                <a href="view_order_info.php?order_id=<?=$orderInfo->Order_ID?>&action=canceled" style="float: right; margin: 0px 10px">Cancel Order</a>
                <a href="view_order_info.php?order_id=<?=$orderInfo->Order_ID?>&action=completed" style="float: right">Complete Order</a>
            </h3>
            <b>Order ID</b> : <?=$orderInfo->Order_ID?><br />
            <b>Status</b> : <?=$orderInfo->Status_ID?><br />
            <b>Total Item</b> : <?=$orderInfo->Total_Items?><br />
            <b>Order Total</b> : <?=$orderInfo->Order_Total?><br />
            
            <h3>Items</h3>
            <?php
            if($items):
               foreach($items as $i):
            ?>
            <b>Item Id : </b> <?=$i->Item_ID?><br />
            <b>Price_Selling : </b> <?=$i->Price_Selling?><br />
            <b>Discount : </b> <?=$i->Discount?><br />
            <b>Data : </b> <?=$i->Data?><br />
            <hr />
            <?php
               endforeach; 
            endif;
            ?>
            
            <textarea style="width: 100%; height: 400px" rows="10"><?=($request1) ? $request1->body : ""?></textarea>
        </div>
    </body>
</html>

