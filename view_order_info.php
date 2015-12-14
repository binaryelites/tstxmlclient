<?php
include("config.php");
$apiurl = hostname."api/xml/tours/get_order_info";

$payload = file_get_contents("buyer.xml"); 
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
            ?>
        
            <h3>Order Info</h3>
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
        </div>
    </body>
</html>

