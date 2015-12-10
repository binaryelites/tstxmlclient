<?php
define("hostname", "http://localhost/tstnew/");

function get_xml_file($filename, $params = array()){
    extract($params);
    //var_dump($params);
    //echo "asdas - " + $user_id;
    ob_start();
    include($filename);
    $buffer = ob_get_contents();
    @ob_end_clean();
    @ob_end_flush();
    $buffer = '<?xml version="1.0" encoding="UTF-8" ?>'.$buffer;
    return $buffer;
}