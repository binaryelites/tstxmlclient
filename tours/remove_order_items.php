<?php
function get_xml($order_id, $item_id){
    $orderxml = "<root>
    <user_id>1</user_id>
    <api_key>123</api_key>
    <api_pass>123</api_pass>               
    <order_id>{$order_id}</order_id>    <!-- which order which item for this user -->
    <item_id>{$item_id}</item_id> <!-- which item to remove -->   
</root>";
    return $orderxml;
}

include("../config.php");
$apiurl = hostname."api/xml/tours/remove_order_items";

$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
$item_id = isset($_GET['item_id']) ? (int)$_GET['item_id'] : 0;

if($order_id <= 0 || $item_id <= 0){
    die("Order ID : {$order_id}, Item ID : {$item_id}");
}

$payload = get_xml($order_id, $item_id);

$request = Requests::post($apiurl, array(), array('__payload__' => $payload));

$result = simplexml_load_string($request->body);
if($result->success == 0):
    //die($result->msg);
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
            <?php if($result->success == 1): ?>
            <h1 style="color: greenyellow">Item removed successfully</h1>
            <?php else :?>
            <h1 style="color: red"><?=$result->msg?></h1>
            <?php endif; ?>
            <a href="view_order_info.php?order_id=<?=$order_id?>"><b>Go Back To Order</b></a>
            
            <h3>XML Response</h3>
            <textarea style="width: 100%; height: 400px;float: right" rows="10"><?=($request) ? $request->body : ""?></textarea>
        </div>
    </body>
</html>