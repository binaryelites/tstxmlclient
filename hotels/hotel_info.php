<?php
include("../config.php");
$apiurl = hostname . "api/xml/hotels/hotel_info";

$params = $_GET;

$payload = file_get_contents("buyer.xml");
// Now let's make a request!
$request = Requests::post($apiurl . "?" . http_build_query($params), array(), array('__payload__' => $payload));

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
    ?>
    <html>
        <head>            
            <title>Hotel Search</title>

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

            <link href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.css" rel="stylesheet">

            <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
            <script src="../js/jquery/js/jquery.ui.autocomplete.html.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.14.0/jquery.validate.min.js"></script>


        </head>
        <body style="width: 100%">    

            <div style="width: 1024px; margin: 0 auto">
                <?php
                include("../menu.php");
                ?>
                <div style="width: 100%; display: table;clear: both">
                    <img src="<?= $result->_config_data_->image_path . $hotelInfo->Image_Banner ?>" style="float:left; width: 200px; height: 130px; margin-right: 10px" />
                    <h3><?= $hotelInfo->Name ?></a></h3>
                    <p><?= $hotelInfo->City_Name ?>, <?= $hotelInfo->Country_Name ?></p>
                    <p><?= $hotelInfo->Address ?></p>
                    <p><?= $hotelInfo->Description ?></p>            
                </div>
                <div style="width: 100%; display: table;clear: both">
                    <h3>Terms</h3>
                    <?= $hotelInfo->Terms_Conditions ?>
                </div>
                <div style="width: 100%; display: table;clear: both">
                    <h3>Available Rooms</h3>
                    <div style="width: 100%; display: table;clear: both">
                        <table style="width: 100%">
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
                                            <div id="hotel_room_child_div_<?= $r->ID ?>"></div>
                                        </td>
                                        <td>
                                            <b>&euro; <?= ((float) $r->Min_Room_Price <= 0) ? $r->Tariff : (float) $r->Min_Room_Price ?></b><br />
                                            <small>8% vat included</small>
                                        </td>
                                        <td>
                                            <select class="form-control input-sm" data-rid="<?= $r->ID ?>" name="quantity[<?= $r->ID ?>]" id="quantity_<?= $r->ID ?>" onchange="app.calculateTotalPrice(<?= $r->ID ?>, true);">
                                                <?php
                                                $rCount = (int) $r->Quantity - (int) $r->Booked_Quantity;
                                                $ic = 0;
                                                while ($ic <= $rCount) {
                                                    ?>
                                                    <option class="<?= $ic ?>"><?= $ic ?></option>
                                                    <?php
                                                    $ic++;
                                                }
                                                ?>
                                            </select>
                                        </td>
                                        <?php
                                        if ($tcount == 1):
                                            $tcount++;
                                            ?>
                                            <td rowspan="<?= count($hotelRooms->item) ?>" style="vertical-align: middle">
                                                <span id="total_order_price"></span>                        
                                                <button class="btn btn-primary btn-block hidden" id='hotelReserveButton'>
                                                    Reserve
                                                </button>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                        </table>
                    </div>
                </div>
                <textarea style="width: 100%;" rows="10"><?= $request->body ?></textarea>
            </div>
            <script>
                var app = app || {};
                app.hotelInfo = <?= json_encode($hotelInfo) ?>;
                app.rooms = <?= json_encode($rooms) ?>;
                app.roomlist = [];
                app.currency = '&euro;';
                app.availableRoomQuantity = '<?= $availableRoomQuantity ?>';
                app.hotelInfoSearchCriteria = <?=json_encode($params)?>;
                
                $(document).ready(function(e){
                    app.initHotelInfo();
                });
                
                app.initHotelInfo = function(){
                    if(app.parseInt(app.availableRoomQuantity) <= 0 ){
                        $("#hotelReserveButton").remove();
                    }

                    $.each(app.rooms,function(i,r){           
                        app.roomlist[r.ID] = r;
                    });

                    $("#check_in_date").val(app.hotelInfoSearchCriteria.search_hotel_checkin);
                    $("#check_out_date").val(app.hotelInfoSearchCriteria.search_hotel_checkout);

                    app.calculateTotalPrice();
                };
                
                app.renderRoomChildren = function ($room, $qty) {
                    var $maxchild = app.parseInt($room.Max_Children);
                    var $cCount = 0;

                    var $html = '<div class="row"><div class="col-sm-12 col-xs-12"><div class="form-group"><label>Room ' + ($qty) + '</label>';
                    $html += '<select class="input-sm form-control" data-rid="' + $room.ID + '" id="max_children_' + $room.ID + '_' + ($cCount + 1) + '" name="max_children[' + $room.ID + '][' + ($cCount + 1) + ']" onchange="app.calculateTotalPrice(' + $room.ID + ');" >';
                    while ($cCount <= $maxchild) {
                        $html += '<option value="' + $cCount + '">' + $cCount + '</option>';
                        $cCount++;
                    }
                    $html += '</select>';
                    $html += '</div></div></div>';

                    return $html;
                };

                app.getChildQuantity = function ($roomid) {
                    var $cQty = 0;
                    $("select[id^=max_children_" + $roomid + "_]").each(function (e) {
                        $cQty += app.parseInt($(this).val());
                    });

                    return $cQty;
                };

                app.calculateTotalPrice = function ($roomid, $renderChildren) {
                    $roomid = app.parseInt($roomid);


                    var $subtotal = 0.00;
                    var $total = 0.00;
                    var $roomQty = 0;

                    $("select[id^=quantity_]").each(function () {
                        var $rid = $(this).attr("data-rid");
                        var $qty = app.parseInt($("select#quantity_" + $rid).val());

                        var $room = app.roomlist[$rid];

                        var $roomPrice = (app.parseFloat($room.Min_Room_Price) <= 0) ? $room.Tariff : $room.Min_Room_Price;

                        var $roomhtml = '';
                        if ($qty > 0) {
                            if ($roomid == $rid && ($renderChildren != undefined || $renderChildren == true)) {
                                var $qi = 1;
                                while ($qi <= $qty) {
                                    $roomhtml += app.renderRoomChildren($room, $qi);
                                    $qi++;
                                }
                                $("#hotel_room_child_div_" + $rid).html($roomhtml);
                            }
                            var $childQty = app.getChildQuantity($rid); //app.parseInt($("select#max_children_"+$rid).val());            
                            $subtotal += ($qty * app.parseFloat($roomPrice)) + ($childQty * app.parseFloat($room.Price_Per_Child));
                        }
                        else {
                            $("#hotel_room_child_div_" + $rid).html("");
                        }

                        $roomQty += $qty;
                    });

                    console.log(app.currency + $subtotal);
                    $("#total_order_price").html(app.currency + $subtotal);

                    if ($roomQty > 0) {
                        $("#hotelReserveButton").removeClass("hidden");
                    }
                    else {
                        $("#hotelReserveButton").addClass("hidden");
                    }
                    return $subtotal;
                };
            </script>
        </body>
    </html>
<?php
} catch (Exception $ex) {
    var_dump($ex->getMessage());
    die();
}