<?php
include("../config.php");
$apiurl = hostname."api/xml/tours/search";

$params['tour_name_like'] = $_POST['tour_name_like'];
$params['continent_id'] = $_POST['continent_id'];
$params['country_id'] = $_POST['country_id'];
$params['city_name'] = $_POST['city_name'];
$params['style_id'] = $_POST['style_id'];
$params['duration'] = $_POST['duration'];
$params['budget'] = $_POST['budget'];
$params['discount_from'] = $_POST['discount_from'];
$params['discount_to'] = $_POST['discount_to'];

$payload = file_get_contents("buyer.xml");

// Now let's make a request!
$request = Requests::post($apiurl."?".http_build_query($params), array(), array('__payload__' => $payload));
//echo "<pre>";
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
    $rates = get_currency_rates($result->Currency_Rates);
    
    foreach($result->item as $t){
    ?>
        <div style="width: 100%; display: table;clear: both;margin-bottom: 10px; border-bottom: 1px solid #ccc">
            <img src="<?=$result->_config_data_->image_path.$t->Image_Long?>" style="float:left; width: 200px; height: 130px; margin-right: 10px" />
            <h3><a href="tour_info.php?tour_id=<?=$t->ID?>"><?=$t->Name?></a></h3>
            <?=$t->Highlights?>            
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



