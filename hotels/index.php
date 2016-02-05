<?php
include("../config.php");
$payload = file_get_contents("buyer.xml");

?>

<html>
    <head>
        <meta charset="UTF-8">
        <title>Hotel Search</title>
        
        <script>
            window.app = {};
            app.baseUrl = '';
            app.assetUrl = '';
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
        </script>
        
        <script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
        <script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>        
        
        <link href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.css" rel="stylesheet">
        
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
        <script src="../js/jquery/js/jquery.ui.autocomplete.html.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.14.0/jquery.validate.min.js"></script>
        

    </head>
    <body style="width: 100%">
        <div style="margin: 0 auto; width: 1024px">
            <?php
            include("../menu.php");
            ?>
            <h3>Search Hotels</h3>
            <form action="search.php" method="get" id="modHotelSearchForm">
                <h3 class="hotel-landing-search-title hidden">Hotel Search</h3>        
                <div class="form-group">  
                    <label for="search_hotel_name">Name</label>
                    <input type="text" class="form-control input-sm" name="search_hotel_name" id="search_hotel_name" placeholder="City, hotel">
                    <small>City, hotel</small>            
                </div>

                <div class="row">
                    <div class="col-sm-5 col-xs-5">
                        <div class="form-group">                
                            <label for="search_hotel_checkin">
                                <i class="glyphicon glyphicon-calendar"></i> Check In
                            </label>
                            <input type="text" class="form-control input-sm required" data-placement="left" name="search_hotel_checkin" id="search_hotel_checkin" />
                        </div>
                    </div>
                    <div class="col-sm-5 col-xs-5">
                        <div class="form-group">                
                            <label for="search_hotel_checkout">
                                <i class="glyphicon glyphicon-calendar"></i> Check Out 
                            </label>
                            <input type="text" class="form-control input-sm required" data-popover-position="top" name="search_hotel_checkout" id="search_hotel_checkout" />
                        </div>
                    </div>
                    <div class="col-sm-2 col-xs-2 text-center" id="nights-column-homes">
                        <div class="form-group">   
                            <label><span class="widget-query-nights-label">Nights</span></label>
                            <span id="nights" class="widget-query-nights">                                    
                                <span class="label label-primary" id="number-of-nights-home"></span> 
                                <i class="glyphicon glyphicon-lamp"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="room-container-homes">
                    <div class="row">
                        <div class="col-sm-4 col-xs-12">
                            <div class="form-group">                
                                <label for="search_hotel_room">Rooms</label>
                                <select class="form-control input-sm required" name="search_hotel_room_count" id="search_hotel_room_count" onchange="app.renderRooms();"> 
                                    <option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-8 col-xs-12 room-container-home">
                            <div class="row">                    
                                <div class="col-sm-6 col-xs-6">
                                    <div class="form-group">                
                                        <label for="search_hotel_adult_count">Adults</label>
                                        <select class="form-control input-sm required" name="search_hotel_adult_count" id="search_hotel_adult_count" onchange="app.renderRooms();">
                                            <option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option>
                                            <option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option>
                                            <option value="9">9</option><option value="10">10</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-xs-6">
                                    <div class="form-group">                
                                        <label for="search_hotel_child_count">Children</label>
                                        <select class="form-control input-sm" name="search_hotel_child_count" id="search_hotel_child_count" onchange="app.toggleChildAge()">    
                                            <?php
                                            $search_hotel_child_count = 0;
                                            while ($search_hotel_child_count < 10) {
                                                ?>
                                                <option value="<?= $search_hotel_child_count ?>"><?= $search_hotel_child_count ?></option>
                                                <?php
                                                $search_hotel_child_count++;
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>  
                                <div class="col-sm-12 col-xs-12">
                                    <div id="search_hotel_child_age_div" style="display:none" class="row"></div>
                                </div> 
                            </div>

                        </div>
                    </div>    

                </div>
                <input type="hidden" value="" name="search_hotel_type" id="search_hotel_type" />
                <input type="hidden" value="" name="search_hotel_type_id" id="search_hotel_type_id" />

                <button class="btn btn-primary btn-block">
                    <i class="glyphicon glyphicon-search"></i> Search
                </button>
            </form>

            <script>    
                var app = app || {};
                app.hotelSearchParams = <?= isset($hotelSearchParams) ? json_encode($hotelSearchParams) : "[]" ?>;
                app.hotelSearchDays = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
                app.hotelSearchInit = function(){        
                    if(app.hotelSearchParams.search_hotel_room_count != undefined){
                        app.populateHotelSearchForm();
                    }
                    else {
                        $("#search_hotel_room_count").val(1);
                        app.renderRooms();
                    }

                    $("#search_hotel_checkin").datepicker({
                        dateFormat: 'dd-mm-yy',
                        minDate: 1,
                        onSelect: function (dateText, inst) {
                            var date = $(this).datepicker('getDate');
                            var dayOfWeek = app.hotelSearchDays[date.getDay()];
                            $(this).next().find('span').html(dayOfWeek);

                            if (date) {
                                date.setDate(date.getDate() + 1);
                            }

                            $("#search_hotel_checkout").datepicker("option", "minDate", date);

                            app.calculateSearchNights();
                            $(this).tooltip('hide').removeClass("error");
                        }
                    });

                    $("#search_hotel_checkout").datepicker({
                        dateFormat: 'dd-mm-yy',
                        minDate: app.getMinCheckoutDate(),
                        beforeShowDay: function (date) {
                            if ($("#search_hotel_checkin").val() == "" || $("#search_hotel_checkin").val() == undefined) {
                                return[false];
                            }
                            return[true];
                        },
                        onSelect: function (dateText, inst) {
                            var date = $(this).datepicker('getDate');
                            var dayOfWeek = app.hotelSearchDays[date.getDay()];
                            $(this).next().find('span').html(dayOfWeek);
                            app.calculateSearchNights();
                            $(this).tooltip('hide').removeClass("error");
                        }
                    });

                    $("#search_hotel_name").on("keypress", function (e) {
                        if (e.which == 13) {
                            e.preventDefault();
                        }
                    });

                    $("#search_hotel_name").autocomplete({
                        html: true,
                        focusOpen: false,
                        source: function (request, response) {
                            $.ajax({
                                url: app.baseUrl + 'hotels/autocomplete_search?search=' + request.term,
                                type: 'get',
                                dataType: 'json',
                                success: function (data) {
                                    response(
                                            $.map(data, function (el) {
                                                return {
                                                    label: el.label,
                                                    value: el.value,
                                                    type: el.type,
                                                    obj: el.object
                                                };
                                            })
                                            );
                                }
                            });

                        },
                        minLength: 2,
                        select: function (event, ui) {
                            if (ui.item) {
                                $("#search_hotel_type").val(ui.item.type);
                                $("#search_hotel_type_id").val(ui.item.obj.ID);
                            }
                            else {
                                $("#search_hotel_type").val("");
                                $("#search_hotel_type_id").val("");
                            }
                        },
                        change: function (event, ui) {
                            if (ui.item) {
                                $("#search_hotel_type").val(ui.item.type);
                                $("#search_hotel_type_id").val(ui.item.obj.ID);
                            }
                            else {
                                $("#search_hotel_type").val("");
                                $("#search_hotel_type_id").val("");
                            }
                        }
                    });

                    var $searchCheckinDate = $.trim(app.hotelSearchParams["search_hotel_checkin"]);
                    var $searchCheckoutDate = $.trim(app.hotelSearchParams["search_hotel_checkout"]);

                    if ($searchCheckinDate == undefined || $searchCheckinDate == "") {
                        $("#search_hotel_checkin").val(app.getFutureDates(1));
                    }

                    if ($searchCheckoutDate == undefined || $searchCheckoutDate == "") {
                        $("#search_hotel_checkout").val(app.getFutureDates(2));
                    }

                    app.calculateSearchNights();
                };

                app.getFutureDates = function (daysToAdd) {
                    daysToAdd = (daysToAdd == undefined) ? 1 : daysToAdd;
                    var myDate = new Date();
                    myDate.setDate(myDate.getDate() + daysToAdd);
                    // format a date
                    var dt = myDate.getDate() + '-' + ("0" + (myDate.getMonth() + 1)).slice(-2) + '-' + myDate.getFullYear();
                    return dt;
                };

                app.getMinCheckoutDate = function () {
                    var date = $("#search_hotel_checkin").datepicker('getDate');
                    if (date) {
                        date.setDate(date.getDate() + 1);
                    }

                    return date;
                };

                app.populateHotelSearchForm = function () {
                    for (s in app.hotelSearchParams) {
                        $("#" + s).val(app.hotelSearchParams[s]);
                        if (s == "search_hotel_child_count") {
                            app.toggleChildAge();
                            var $ChildCount = app.parseInt($("#search_hotel_child_count").val());
                            var $ii = 1;
                            if ($ChildCount > 0) {
                                while ($ii <= $ChildCount) {
                                    $("#chil_age_" + $ii).val(app.hotelSearchParams["child_age_" + $ii]);
                                    console.log("child age " + $ii + " -- " + app.hotelSearchParams["child_age_" + $ii]);
                                    $ii++;
                                }
                            }
                        }
                    }
                };

                app.renderRooms = function () {
                    console.log("rendering room");

                    var $RoomCount = app.parseInt($("#search_hotel_room_count").val());
                    var $AdultCount = app.parseInt($("#search_hotel_adult_count").val());
                    if ($RoomCount > 0 && $AdultCount < $RoomCount) {
                        $("#search_hotel_adult_count").val($RoomCount);
                    }
                    app.toggleAdultOptions();
                    return false;

                };

                app.toggleAdultOptions = function () {
                    $("#search_hotel_adult_count option").each(function (e) {
                        var $RoomCount = app.parseInt($("#search_hotel_room_count").val());
                        if (app.parseInt($(this).val()) < $RoomCount) {
                            $(this).css("display", "none");
                        }
                        else {
                            $(this).css("display", "block");
                        }
                    });
                };

                app.toggleChildAge = function (domId, adultId) {
                    var $val = app.parseInt($("#search_hotel_child_count").val());
                    console.log("rendering child age");
                    if ($val > 0) {
                        var $chtml = '';
                        var $ii = 1;
                        while ($val > 0) {
                            $chtml += '<div class="col-xs-6 col-sm-6"><div class="form-group" id="search_hotel_child_age_div_' + $ii + '">';
                            $chtml += '<label for="search_hotel_child_age_' + $ii + '">Child ' + $ii + ' Age:</label>';
                            $chtml += '<select class="form-control input-sm required" name="child_age_' + $ii + '" id="child_age_' + $ii + '">';
                            $chtml += '<option selected="selected" value="">?</option value="0"><option>0</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option>';
                            $chtml += '</select>';
                            $chtml += '</div></div>';
                            $ii++;
                            $val--;
                        }
                        $("div#search_hotel_child_age_div").html($chtml).show();
                    }
                    else {
                        $("div#search_hotel_child_age_div").html("").hide();
                    }

                };

                app.calculateSearchNights = function () {
                    var end_date = $('#search_hotel_checkout').datepicker('getDate'); // assuming the format is correct
                    var start_date = $('#search_hotel_checkin').datepicker('getDate'); // assuming the format is correct

                    if (start_date != null && end_date != null) {
                        var date_diff = dateDiffInDays(start_date, end_date);
                        $("#number-of-nights-home").html(date_diff);
                    }
                };

                $(document).ready(function (e) {
                    app.hotelSearchInit();
                    $("#modHotelSearchForm").validate();

                    $("#search_hotel_checkin, #search_hotel_checkout").on('focus', function (e) {
                        $(this).attr("readonly", "readonly");
                    });

                    $("#search_hotel_checkin, #search_hotel_checkout").on('blur', function (e) {
                        $(this).removeAttr("readonly");
                    });
                });

                var _MS_PER_DAY = 1000 * 60 * 60 * 24;

                // a and b are javascript Date objects
                function dateDiffInDays(a, b) {
                    // Discard the time and time-zone information.
                    var utc1 = Date.UTC(a.getFullYear(), a.getMonth(), a.getDate());
                    var utc2 = Date.UTC(b.getFullYear(), b.getMonth(), b.getDate());

                    return Math.floor((utc2 - utc1) / _MS_PER_DAY);
                }
                ;

            </script>
        </div>
        
        
        
        
    </body>
</html>
