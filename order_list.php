<?php
include ("config.php");

$order_list = false;
$request = false;
$buyer_id = isset($_GET['buyer_id']) ? (int)$_GET['buyer_id'] : "";
if($buyer_id > 0):
    $apiurl = hostname."api/xml/tours/get_order_list";

    $orderxml = "<root>
    <user_id>{$buyer_id}</user_id>
    <api_key>123</api_key>
    <api_pass>123</api_pass>               
</root>";

    $payload = $orderxml; 
    
    $request = Requests::post($apiurl, array(), array('__payload__' => $payload));
    $result = simplexml_load_string($request->body);
        
        
    if($result->success == 1):
        $order_list = $result->orders->item;
    endif;
endif;
?>

<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <style>
            label {display: block; margin-bottom: 10px};
            table tr th {text-align: left}
        </style>
    </head>
    <body>
        <div style="width: 1024px; margin: 0 auto">
            <?php
            include("menu.php");
            ?>
            
            <h3>Order List</h3>
            <form action="order_list.php" method="get">
                <b>Buyer ID</b><br />
                <input type="text" placeholder="buyer id or user id" name="buyer_id" value="<?=$buyer_id?>" />
                <button type="submit">Show Order List</button>
            </form>
            
            <h4>Order List</h4>   
            <table style="width: 100%">
                <tr>
                    <th style="text-align: left">SL</th>
                    <th style="text-align: left">Order ID</th>
                    <th style="text-align: left">Status</th>
                    <th style="text-align: left">Total Items</th>
                    <th style="text-align: left">Total</th>
                </tr>
            <?php if($order_list): 
                $sl = 1;
                ?>
                <?php foreach($order_list as $o): ?>
                <tr>
                    <td><?=$sl++?></td>
                    <td><?=$o->Order_ID?></td>
                    <td><?=$o->Status_ID?></td>
                    <td><?=$o->Total_Items?></td>
                    <td><?=$o->Order_Total?></td>
                    <td>
                        <a href="view_order_info.php?order_id=<?=$o->Order_ID?>">View</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="6">
                        <h2>Response from server</h2>        
                        <textarea style="width: 100%; height: 400px" rows="10"><?=($request) ? $request->body : ""?></textarea>
                    </td>
                </tr>
            <?php else : ?>
                <tr><td colspan="6">No orders found</td></tr>
            <?php endif; ?>
                
            </table>
        </div>
    </body>     
</html>