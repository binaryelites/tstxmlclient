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

include("../config.php");
$apiurl = hostname."api/xml/hotels/get_order_info";

$payload = file_get_contents("buyer.xml"); 

$action = isset($_GET['action']) ? $_GET['action'] : false;

$actionPayload = null;
$actionMsg = "";
$request1 = "";
//if action then do it
if(strtolower($action) == "completed" || strtolower($action) == "canceled"){
    $actionUrl = hostname."api/xml/hotels/save_order";
    
    $actionPayload = get_order_xml(strtolower($action), $_GET['order_id']);
    
    $request1 = $request = Requests::post($actionUrl, array(), array('__payload__' => $actionPayload));
    
    $result = simplexml_load_string($request->body);
    if($result->success == 0):
        die($result->msg);
    endif;
    
    $actionMsg = "Order status set to $action";
    //var_dump($result);
}

$request = Requests::post($apiurl."?order_id=".$_GET['order_id'], array(), array('__payload__' => $payload));

$result = simplexml_load_string($request->body);
if($result->success == 0):
    die("no order info found");
endif;

$orderInfo = $result->order;

$apiurl = hostname."api/xml/hotels/get_order_items";
$request = Requests::post($apiurl."?order_id=".$_GET['order_id'], array(), array('__payload__' => $payload));

$items = false;
$rooms = false;
$result = simplexml_load_string($request->body);
if($result->success == 1):
    $items = $result->items->item;    
    $rooms = $result->rooms->item;
endif;

$room_items = array();
foreach($rooms as $r){
    $room_items[] = $r;
}

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
            <b>Rooms : </b> 
            <table style="width: 100%">
                <tr style="background: #ccc">
                    <td>Hotel</td>
                    <td>Room Type</td>
                    <td>Qty</td>
                    <td>Adult</td>
                    <td>Children</td>
                    <td>Guest Info</td>
                    <td>Price</td>
                </tr>
            
            
            <?php              
            if(count($room_items)):                 
                foreach($room_items as $roomInfo):
            ?>
                <tr>
                    <td>
                        <?=$roomInfo->Hotel_Name?>, <?=$roomInfo->Country_Name?>, <?=$roomInfo->City_Name?>
                    </td>
                    <td><?=$roomInfo->Room_Name?></td>
                    <td><?=$roomInfo->Quantity?></td>
                    <td><?=$roomInfo->Num_Adults?></td>
                    <td>
                        <?php if((int)$roomInfo->Num_Children > 0): ?>
                        <?=$roomInfo->Num_Children?>
                        at <?=$roomInfo->Child_Price?> each
                        <?php endif; ?>
                    </td>
                    <td>
                    <?php
                    $guestInfo = json_decode($roomInfo->Guest_Name,true);                    
                    $rid = (int)$roomInfo->Room_ID;
                    if(isset($guestInfo[$rid])){
                        foreach($guestInfo[$rid] as $gk => $gv){
                            echo 'Name : '.$gv." <br />";
                        }
                    }
                    ?>
                    </td>
                    <td><?=$roomInfo->Room_Price?></td>
                </tr>
                <?php  //json_encode($room_items[(int)$i->Item_ID]) ?>
            <?php 
                endforeach;
            endif; 
            ?>
            </table>
            <br />
            <a href="remove_order_items.php?order_id=<?=$i->Order_ID?>&item_id=<?=$i->Item_ID?>"><b>Remove Item</b></a>
            <hr />
            <?php
               endforeach; 
            endif;
            ?>
            <div style="width: 49%;float: right">
                <h5>Order Status Update XML</h5>
                <textarea style="width: 100%; height: 400px;float: left" rows="10"><?=($request1) ? $request1->body : ""?></textarea>                
            </div>
            <div style="width: 49%;float: left">                
                <h5>Order Info XML</h5>
                <textarea style="width: 100%; height: 400px;float: right" rows="10"><?=($request) ? $request->body : ""?></textarea>
            </div>
        </div>
    </body>
</html>

