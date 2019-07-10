<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: token,Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: POST,GET');

 $data = $_POST;
 print_r($data);
