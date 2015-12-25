<?php
include("config.php");

$action = isset($_GET['action']) ? $_GET['action'] : "";


switch ($action) {
    case "get_cities_subcategories_by_country":
            $apiurl = hostname."api/xml/tours/get_cities_subcategories_by_country";
            $payload = file_get_contents("buyer.xml");

            $params['country_id'] = $_GET['country_id'];

            $request = Requests::post($apiurl."?".http_build_query($params), array(), array('__payload__' => $payload));
            $result = simplexml_load_string($request->body);
            if($result->success == 0):
                $json = array(
                    "success" => false,
                    "msg" => $result->msg
                );
                echo json_encode($json);
                die();
            endif;
            
            $json = array(
                "success" => true,
                "cities" => $result->cities->item,
                "sub_categories" => $result->sub_categories->item,
                "result" => $result
            );
            
            echo json_encode($json);
            die();
        break;

    default:
        break;
}