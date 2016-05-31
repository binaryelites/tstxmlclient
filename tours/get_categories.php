<?php
include("../config.php");
$payload = file_get_contents("buyer.xml");

$category_id = isset($_GET['category_id']) ? $_GET['category_id'] : "";

$apiurl = hostname."api/xml/tours/get_tour_categories?category_id=".$category_id;
$request = Requests::post($apiurl, array(), array('__payload__' => $payload));
$result = simplexml_load_string($request->body);

echo "<pre>";
var_dump($result);