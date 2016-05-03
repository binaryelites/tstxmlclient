<?php
include("../config.php");
$payload = file_get_contents("buyer.xml");


$postparams = $_POST;

$room_id_array = $_POST['room_id'];

$rooms_with_qty = array();
$params = array();

$params['user_id'] = 1;
$params['api_key'] = 123;
$params['api_pass'] = 123;
$params['hotel_id'] = $_POST['hotel_id'];
$params["check_in_date"] = $_POST['check_in_date'];
$params["check_out_date"] = $_POST['check_out_date'];

foreach($room_id_array as $k => $v){
    if(isset($_POST['quantity'][$v]) && (int)$_POST['quantity'][$v] > 0){
        $guestInformation = array();

        if(isset($_POST['guest_name'][$v])){
            //$guestInformation = $_POST['guest_name'][$v];
            foreach($_POST['guest_name'][$v] as $gk => $gname){
                $guestInformation[] = array(
                    "name" => $_POST['guest_name'][$v][$gk]
                );
            }
        }

        $params['rooms'][] = array(
            "room_id" => $v,            
            "quantity" => isset($_POST['quantity'][$v]) ? $_POST['quantity'][$v] : 0,
            "max_children" => isset($_POST['max_children'][$v]) ? $_POST['max_children'][$v] : 0,
            "guest_information" => $guestInformation,
        );
        
        $rooms_with_qty[$v] = $v;
    }
}




$apiurl = hostname . "api/xml/hotels/hotel_info";

$gparams = array();
$gparams['hotel_id'] = $params['hotel_id'];
$gparams['room_id'] = $rooms_with_qty;
$gparams['search_hotel_checkin'] = $_POST['check_in_date'];
$gparams['search_hotel_checkout'] = $_POST['check_out_date'];

$payload = file_get_contents("buyer.xml");
// Now let's make a request!
$request = Requests::post($apiurl . "?" . http_build_query($gparams), array(), array('__payload__' => $payload));

try {
    $result = simplexml_load_string($request->body);

    if ($result->success == 0):
        echo "<h3>No hotel found</h3>";
        ?>
        <textarea style="width: 100%;" rows="10"><?= $request->body ?></textarea>
        <?php
        die();
    endif;
    $hotelInfo = $result->hotelInfo;
    $hotelRooms = $result->hotelRooms;
    
    
    $apiurl = hostname."api/xml/hotels/save_order";

    $payload = file_get_contents("create_order.xml"); 
    // Now let's make a request!
    $bookingrequest = Requests::post($apiurl, array(), array('__payload__' => $payload));
    $bookingresult = simplexml_load_string($bookingrequest->body);
    if((int)$bookingresult->success == 0){
        echo "Success : false<br />";
        echo "Message : {$bookingresult->msg} <br /> No order could be generated";
    }

    $order_id = $bookingresult->order->ID;
        
    $params['order_id'] = $order_id;
    $params['item_id'] = 0;
    $bookingpayload = my_xmlapi_output($params, false);
?>
<html>
    <head>            
        <title>Hotel Booking Confirm</title>

        <script>
            window.app = {};
            app.baseUrl = '';
            app.assetUrl = '';
            app.disableElement = function ($domId) {
                $("#" + $domId).attr("disabled", "disabled");
            };
            app.enableElement = function ($domId) {
                $("#" + $domId).removeAttr("disabled");
            };

            app.parseInt = function (val, defaultval) {
                return !isNaN(parseInt(val)) ? parseInt(val) : (defaultval == undefined ? 0 : defaultval);
            };
            app.parseFloat = function (val, defaultval) {
                return !isNaN(parseFloat(val)) ? parseFloat(val) : (defaultval == undefined ? 0.00 : defaultval);
            };
        </script>

        <script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
        <script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>        

        <link href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.css" rel="stylesheet" />

        <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
        <script src="../js/jquery/js/jquery.ui.autocomplete.html.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.14.0/jquery.validate.min.js"></script>


    </head>
    <body style="width: 100%">    

        <div style="width: 1024px; margin: 0 auto">
            <?php
            include("menu.php");
            ?>
            <h3>Save order item with order id : <?=$order_id?> (Order id has to be generated before we can save item)</h3>
            <div style="width: 100%; display: table;clear: both">
                <img src="<?= $result->_config_data_->image_path . $hotelInfo->Image_Banner ?>" style="float:left; width: 200px; height: 130px; margin-right: 10px" />
                <h3><?= $hotelInfo->Name ?></a></h3>
                <p><?= $hotelInfo->City_Name ?>, <?= $hotelInfo->Country_Name ?></p>
                <p><?= $hotelInfo->Address ?></p>
                <p><?= $hotelInfo->Description ?></p>            
            </div>
            
             <div style="width: 100%; display: table;clear: both">                
                 <form action="confirm_booking.php" id='bookingForm' method="post" >
                    <h3>Available Rooms</h3>
                    <div style="width: 100%; display: table;clear: both">
                        <table style="width: 100%">
                            <thead>
                                <tr style="background-color: #eee;text-align: left">
                                    <th colspan="7">Check In/Check Out Date</th>
                                </tr>
                            </thead>
                            <tr>
                                <td colspan="7">
                                    <b>Hotel ID: </b><?=$gparams['hotel_id']?> <br />
                                    <b>Checkin: </b><?=$gparams['search_hotel_checkin']?> <br />
                                    <b>Checkout: </b><?=$gparams['search_hotel_checkout']?>
                                </td>
                            </tr>
                            <thead>                
                                <tr style="background-color: #eee">
                                    <th>Room type</th>
                                    <th>Pax</th>
                                    <th>Children for 1 night(s)</th>
                                    <th>No of Child</th>
                                    <th>Price for 1 night(s) (EUR)</th>
                                    <th>Booking Qty</th>
                                    <th>Reservation</th>
                                </tr>
                            </thead>
                            <?php
                            $availableRoomQuantity = 0;
                            $rooms = array();
                            if (isset($result->hotelRooms->item) && count($result->hotelRooms->item)) {
                                $tcount = 1;
                                foreach ($result->hotelRooms->item as $r) {
                                    $rooms[] = $r;
                                    $availableRoomQuantity += (int) $r->Quantity - (int) $r->Booked_Quantity;
                                    ?>    
                                    <tr>
                                        <td>
                                            <img class="img-responsive room-thumb" style="width: 100px" alt="<?= $r->Name ?>" src="<?= $result->_config_data_->image_path . $r->Image ?>" />
                                            <b><?= $r->Name ?></b> <br />
                                            <?= (int) $r->Quantity - (int) $r->Booked_Quantity ?> Rooms Left
                                        </td>
                                        <td>
                                            <?php
                                            $paxToolTipText = "{$r->Max_Adults} adult(s) and {$r->Max_Children} children are allowed";
                                            ?>
                                            <?= ($r->Max_Adults + $r->Max_Children) ?> x <i class="glyphicon glyphicon-user" data-toggle="tooltip" data-placement="top" title="<?= addslashes($paxToolTipText) ?>"></i>
                                        </td>
                                        <td>
                                            Children will cost &euro; <?= $r->Price_Per_Child ?> each
                                        </td>
                                        <td>                                            
                                            <?=$postparams['max_children'][(int)$r->ID]?>
                                        </td>
                                        <td>
                                            <b>&euro; <?= ((float) $r->Min_Room_Price <= 0) ? $r->Tariff : (float) $r->Min_Room_Price ?></b><br />
                                            <small>8% vat included</small>
                                        </td>
                                        <td>                                            
                                            <?=$postparams['quantity'][(int)$r->ID]?>
                                        </td>
                                        <?php
                                        if ($tcount == 1):
                                            $tcount++;
                                            ?>
                                            <td rowspan="<?= count($hotelRooms->item) ?>" style="vertical-align: middle">
                                                <span id="total_order_price">&euro; <?=$postparams['order_total']?></span>                                                        
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                        </table>
                    </div>
                    <hr style="margin: 10px 5px" />
                     <?php 
                     if(count($rooms)) {    
                         $roomno = 1;
                        foreach($rooms as $r){                            
                     ?>
                    <div id="guestInformationContainer-<?=$r->ID?>">
                        <h3 class="page-title-underlined">Room <?=$roomno++?>: <?=$r->Name?></h3>
                        <small>
                            Please tell us the name of the guest staying at the hotel as it appears on the ID that they’ll present at check out. If the guest has more than one last name, please enter them all.
                        </small>
                        <br />
                        <?php 
                        if(isset($postparams['guest_name'][(int)$r->ID])){
                            foreach($postparams['guest_name'][(int)$r->ID] as $nk => $n){
                                echo "Guest Name ".($nk + 1).": ".$n."<br />";
                            }
                        }
                        ?>
                    </div>    
                    <?php
                        }
                     }
                    ?>
                    <textarea style="display: none" name="payload"><?=$bookingpayload?></textarea>  
                    <br />
                    <br />
                    <button id="" onclick="return app.saveBooking(this);">Save Booking</button>
                 </form>
                    
                    <div style="float: left; width: 45%">
                        <h3>Payload For Booking</h3>
                         <small>This pay load is used to save booking of hotel rooms</small><br />
                         <textarea style="width: 100%;height: 300px" readonly="true" id='xmlnew'><?=$bookingpayload?></textarea>                        
                    </div>
                    <div style="float: right; width: 45%">
                        <h3>Sample Payload For Booking</h3>
                        <textarea style="width: 100%;height: 300px;background-color: #ededed" readonly="true"><?=  file_get_contents("room_booking.xml")?></textarea>                        
                    </div>
            </div>
        </div>    
        <script> 
            var order_id = '<?=$order_id?>';
            app.saveBooking = function($this){
                var $formdata = $("#bookingForm").serialize();
                
                $.ajax({
                    url : 'confirm_booking.php',
                    type : 'post',
                    data : $formdata,
                    dataType : 'json',
                    success : function(data){
                        if(data.success == true){
                            alert("Order saved successfully");
                            window.location = 'view_order_info.php?order_id='+order_id;
                            return false;
                        }
                        alert(data.msg);
                    },
                    error : function(data){
                        alert("There was a problem. Please try again later");
                        console.log(data);
                    }
                });
                
                return false;
            };
            
            $(document).ready(function(){
                var xml_formatted = formatXml($("#xmlnew").val());
                var xml_escaped = xml_formatted.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/ /g, '&nbsp;');//.replace(/\n/g,'<br />');
                $("#xmlnew").html(xml_escaped);
            });
            function formatXml(xml) {
                var formatted = '';
                var reg = /(>)(<)(\/*)/g;
                xml = xml.replace(reg, '$1\r\n$2$3');
                var pad = 0;
                jQuery.each(xml.split('\r\n'), function(index, node) {
                    var indent = 0;
                    if (node.match( /.+<\/\w[^>]*>$/ )) {
                        indent = 0;
                    } else if (node.match( /^<\/\w/ )) {
                        if (pad != 0) {
                            pad -= 1;
                        }
                    } else if (node.match( /^<\w[^>]*[^\/]>.*$/ )) {
                        indent = 1;
                    } else {
                        indent = 0;
                    }

                    var padding = '';
                    for (var i = 0; i < pad; i++) {
                        padding += '  ';
                    }

                    formatted += padding + node + '\r\n';
                    pad += indent;
                });

                return formatted;
            }
        </script>
    </body>
    <script>
    </script>
</html> 
<?php
} catch (Exception $ex) {
    var_dump($ex->getMessage());
    die();
}