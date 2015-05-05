<?php

global $xunsearch;

$xunsearch = "/usr/local/xunsearch";

if(file_exists("$xunsearch/sdk/php/lib/XS.php")){

	require_once "$xunsearch/sdk/php/lib/XS.php";
	
	new XS("demo");
	
}

?>