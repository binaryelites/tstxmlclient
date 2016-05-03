<?php
include("../config.php");
$apiurl = hostname."api/xml/hotels/search";

$params = $_GET;

$payload = file_get_contents("buyer.xml");

// Now let's make a request!
$request = Requests::post($apiurl."?".http_build_query($params), array(), array('__payload__' => $payload));

?>
<div style="width: 100%">
    <div style="width: 1024px; margin: 0 auto">    
        <?php
        include("menu.php");
        ?>
<?php
// Check what we received
try {
    $result = simplexml_load_string($request->body);
    
    foreach($result->item as $h){
    ?>

        <div style="width: 100%; display: table;clear: both;margin-bottom: 10px; border-bottom: 1px solid #ccc;padding-bottom: 10px">
            <div style="float: left;width: 25%;padding-right: 1%">
                <img src="<?=$result->_config_data_->image_path.$h->Image_Banner?>" style="float:left; width: 100%; margin-right: 10px" />                
            </div>
            <div style="float: right;width: 74%">
                <h3 style="margin-top: 0px; margin-bottom: 5px">
                    <a href="hotel_info.php?hotel_id=<?=$h->ID?>&<?=http_build_query($params)?>"><?=$h->Name?></a>
                    <small style="float: right"><?=$h->Customer_Rating?>/5 out of <?=$h->Total_Reviews?></small>
                </h3>
                <b><?=$h->City_Name?>, <?=$h->Country_Name?></b><Br />
                <b>Address :</b> <?=$h->Address?><Br /><Br />
                <?=$h->Description?>       <br />

                <div style="display: table;width: 100%">
                    <h3 style="margin: 5px">Min Room Price</h3>
                    <table style="width: 100%">
                        <tr>
                            <th style="width: 120px;background-color: #eee;text-align: left">Room</th>
                            <th style="background-color: #eee;text-align: left">Price Starts With</th>
                        </tr>
                        <tr>
                            <td>
                                <a href="hotel_info.php?hotel_id=<?=$h->ID?>&<?=http_build_query($params)?>">
                                    <b><?=$h->Room_Name?></b>
                                </a>            
                            </td>
                            <td>
                                <?=$h->Room_Price?>
                                <a href="hotel_info.php?hotel_id=<?=$h->ID?>&<?=http_build_query($params)?>">
                                    <b>Book Now</b>
                                </a>
                            </td>
                        </tr>
                    </table>                    
                </div>                
            </div>
        </div>
    
    <?php
    }
}
catch (Exception $ex) {
     var_dump($ex->getMessage());
}
?>
        <textarea style="width: 100%;" rows="10"><?=$request->body?></textarea>
    </div>
</div>