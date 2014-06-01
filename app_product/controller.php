<?php
ini_set('display_errors',1); 
error_reporting(E_ALL);

/* no direct access */
defined('EXEC') or die('Restricted access');

/* include model file */
include (APPLICATIONS . DS . 'app_product' . DS . 'models' . DS . 'model.product.php');

//make Model Object
$objProduct = new product();

//Get job
$job = "";
if(isset($_REQUEST['job']) && !empty($_REQUEST['job'])) {
	$job = $_REQUEST['job'];
}

//Perform Job
$objProduct->performjob($job);

?>