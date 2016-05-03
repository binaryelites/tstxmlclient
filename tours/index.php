<?php
include("../config.php");
$payload = file_get_contents("buyer.xml");

$apiurl = hostname."api/xml/tours/get_countries";
$request = Requests::post($apiurl, array(), array('__payload__' => $payload));
$result = simplexml_load_string($request->body);
$countries = array();
if($result->success == 1):
    $countries = $result->countries->item;
endif;


$apiurl = hostname."api/xml/tours/get_durations";
$request = Requests::post($apiurl, array(), array('__payload__' => $payload));
$result = simplexml_load_string($request->body);
$duration_list = array();
if($result->success == 1):
    $duration_list = $result->durations->item;
endif;


?>

<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body style="width: 100%">
        <div style="margin: 0 auto; width: 1024px">
            <?php
            include("menu.php");
            ?>
            <h3>Search Tours</h3>
            <form action="search.php" method="post">
                <p>
                    <b>Name</b><br />                    
                    <input type="text" value="" name="tour_name_like"  />
                </p>
                <p>
                    <b>Country</b><br />
                    <select name="country_id" id="country" onchange="return app.get_cities_subcategories_by_country(this);">
                        <option></option>
                    <?php foreach($countries as $c): ?>
                        <option value="<?=$c->ID?>"><?=$c->Name?></option>
                    <?php endforeach; ?>
                    </select>
                </p>
                <p>
                    <label for="search_tour_city">City</label><br />
                    <select class="form-control input-sm" name="city_name" id="search_tour_city">   
                        <option></option>
                    </select>
                </p>
                <p>
                    <label for="search_tour_category">Category</label><br />
                    <select class="form-control input-sm" name="sub_category_id" id="search_tour_category">                    
                        <option></option>
                    </select>
                </p>
                <p>
                    <label for="search_tour_duration">Duration</label><br /> 
                    <select class="form-control input-sm" name="duration" id="search_tour_duration"> 
                        <option value=""></option>
                <?php if($duration_list): ?>
                    <?php 
                    foreach($duration_list as $dv):                         
                    ?>
                    <option value="<?php print $dv->ID; ?>"><?php print $dv->Label; ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>    
                    </select>
                </p>
                <p>
                    <b>Budget</b><br />                    
                    <input type="text" value="" name="budget"  />
                </p>
                <button type="submit">Search</button>
            </form>
        </div>
        
        <script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
        <script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
        
        <script>
            var app = {};
            app.baseUrl = '';
            app.disableElement = function($domId){
                $("#"+$domId).attr("disabled", "disabled");
            };
            app.enableElement = function($domId){
                $("#"+$domId).removeAttr("disabled");
            };
            
            app.parseInt = function(val, defaultval){
                return !isNaN(parseInt(val)) ? parseInt(val) : (defaultval == undefined ? 0 : defaultval) ;
            };
            app.parseFloat = function(val, defaultval){
                return !isNaN(parseFloat(val)) ? parseFloat(val) : (defaultval == undefined ? 0.00 : defaultval) ;
            };
            
            app.get_cities_subcategories_by_country = function($this){
                var formData = {
                    country_id : $($this).val(),
                    action : 'get_cities_subcategories_by_country'
                };

                app.disableElement($($this).attr("id"));
                app.disableElement("tour_search_form #tour_search_btn");

                $.getJSON(app.baseUrl+"ajax.php/get_cities_subcategories_by_country", formData)
                .success(function(data){
                    app.enableElement($($this).attr("id"));
                    app.enableElement("tour_search_form #tour_search_btn");

                    console.log(data);
                    app.render_city_list(data.result.cities.item, "search_tour_city");
                    app.render_subcategory_list(data.result.sub_categories.item, "search_tour_category");
                })
                .error(function(data){
                    app.enableElement($($this).attr("id"));
                    app.enableElement("tour_search_form #tour_search_btn");
                    console.log("error");
                    console.log(data);
                })
            };

            app.render_city_list = function($result, $domid){                
                var $html = "<option value=''></option>";
                if(!$.isArray($result)){
                    $result = [$result]; 
                }
                $.each($result, function(i,c){                    
                    $html += '<option value="'+c.Name+'">'+c.Name+'</option>';
                });

                $("#"+$domid).html($html);
            };

            app.render_subcategory_list = function($result, $domid){
                console.log($result);
                if(!$.isArray($result)){
                    $result = [$result]; 
                }
                var $html = "<option value=''></option>";
                $.each($result, function(i,c){                    
                    $html += '<option value="'+c.ID+'">'+c.Name+'</option>';
                });

                $("#"+$domid).html($html);
            };
        </script>
    </body>
</html>
