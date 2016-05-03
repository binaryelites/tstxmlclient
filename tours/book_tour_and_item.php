<?php
include("../config.php");
$tour_id = $_GET['tour_id'];

$tourRes = get_tour_info($tour_id);
if($tourRes->success == 0):
     die("no tour info found");
endif;

$tourInfo = $tourRes->tourInfo;

$apiurl = hostname."api/xml/tours/save_order";

$payload = file_get_contents("book_tour_and_item.xml"); 
        
// Now let's make a request!
$request = Requests::post($apiurl, array(), array('__payload__' => $payload));
$result = simplexml_load_string($request->body);
if((int)$result->success == 0){
    echo "Success : false<br />";
    echo "Message : {$result->msg} <br /> No order could be generated";
}

$order_id = $result->order->ID;

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
        
            <h3>Save order item with order id : <?=$order_id?> (Order id has to be generated before we can save item)</h3>
            <b>Tour Name</b> : <?=$tourInfo->Name?><br />
            <form action="save_order_item.php" method="post" target="_blank">

                <textarea style="width: 400px; height: 300px" name="__payload__"><?php include('book_tour_and_item2.php'); ?></textarea>

                <button type="submit">Save</button>
            </form>
        </div>
    </body>
</html>


