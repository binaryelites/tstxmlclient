<?php
include("../config.php");
$apiurl = hostname."api/xml/tours/tour_info";

$params['tour_id'] = $_GET['tour_id'];

$payload = file_get_contents("buyer.xml");
// Now let's make a request!
$request = Requests::post($apiurl."?".http_build_query($params), array(), array('__payload__' => $payload));

// Check what we received
try {
    $result = simplexml_load_string($request->body);
    
    if($result->success == 0):
        echo "<h3>No tour found</h3>";
    ?>
    <textarea style="width: 100%;" rows="10"><?=$request->body?></textarea>
    <?php    
        die();
    endif;
    $tourInfo = $result->tourInfo;
    ?>
<div style="width: 100%">
    <div style="width: 1024px; margin: 0 auto">
        <?php
        include("menu.php");
        ?>
        <div style="width: 100%; display: table;clear: both">
            <img src="<?=$result->_config_data_->image_path.$tourInfo->Image_Long?>" style="float:left; width: 200px; height: 130px; margin-right: 10px" />
            <h3><a href="tour_info.php?tour_id=<?=$tourInfo->ID?>"><?=$tourInfo->Name?></a></h3>
            <p>Tour Start City : <?=$tourInfo->Tour_Start_City?></p>
            <p>Tour End City : <?=$tourInfo->Tour_End_City?></p>
            <p>Tour Route : <?=$tourInfo->Tour_Route?></p>
            <a href="book_tour_and_item.php?tour_id=<?=$tourInfo->ID?>" style="float: right">
                <b>Book Tour</b>
            </a>
        </div>
        <div style="width: 100%; display: table;clear: both">
            <h3>Hotels</h3>
            <table style="width: 100%">
                <tr>
                    <th style="text-align: left;background: #ccc;">Name</th>
                    <th style="text-align: left;background: #ccc;">City</th>
                    <th style="text-align: left;background: #ccc;">Hotel Type</th>
                </tr>
            
            <?php 
            foreach($tourInfo->Hotel_Info->item as $h){
            ?>
            <tr>
                <td><?=$h->Name?></td>
                <td><?=$h->City_Name?></td>
                <td><?=$h->Hotel_Type?></td>
            </tr>
            <?php    
            }
            ?>
            </table>
            <h3>Terms</h3>
            <?=$tourInfo->Terms_Conditions?>
        </div>
        <div style="width: 100%; display: table;clear: both">
            <h3>Facilities Include</h3>
            <?=$tourInfo->Facilities_Include_Old?>
            <h3>Facilities Exclude</h3>
            <?=$tourInfo->Facilities_Exclude_Old?>
        </div>
        <div style="width: 100%; display: table;clear: both">
            <h3>Itinerary</h3>
            <?php
            if(isset($result->tourItinerary->item) && count($result->tourItinerary->item)){
                foreach($result->tourItinerary->item as $t){
                 ?>
        <div style="width: 100%; display: table;clear: both">
            <h5><?=$t->Name?></h5>
            <p><?=$t->Description?></p>
            <p>Start Time : <?=$t->Time_Start?>, End Time : <?=$t->Time_End?></p>            
        </div>
                 <?php   
                }
            }
            ?>
        </div>
        <textarea style="width: 100%;" rows="10"><?=$request->body?></textarea>
    </div>
</div>    
    <?php    
}
catch (Exception $ex) {
     var_dump($ex->getMessage());
}



