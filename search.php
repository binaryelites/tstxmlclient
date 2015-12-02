<?php
$apiurl = "http://localhost/tstnew/api/xml/tours/search";

$params['tour_name_like'] = $_POST['tour_name'];


include "libraries/Requests.php";
Requests::register_autoloader();

$payload = file_get_contents("buyer.xml");

// Now let's make a request!
$request = Requests::post($apiurl."?".http_build_query($params), array(), array('__payload__' => $payload));
//echo "<pre>";

// Check what we received
try {
    $result = simplexml_load_string($request->body);
    
    foreach($result->item as $t){
    ?>
<div style="width: 100%">
    <div style="width: 1024px; margin: 0 auto">
        <div style="width: 100%; display: table;clear: both">
            <img src="<?=$result->_config_data_->image_path.$t->Image_Long?>" style="float:left; width: 200px; height: 130px; margin-right: 10px" />
            <h3><a href="tour_info.php?tour_id=<?=$t->ID?>"><?=$t->Name?></a></h3>
            <?=$t->Highlights?>
        </div>
    </div>
</div>
    <?php
    }
}
catch (Exception $ex) {
     var_dump($ex->getMessage());
}



