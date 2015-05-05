<?php

$library = "../../";

require_once "$library/com.9vteam.cook/COContext.php";

$context = new COContext();

$pageIndex = intval( $context->getInputDataValue("pageIndex"));
$pageSize = intval( $context->getInputDataValue("pageSize"));

if($pageIndex < 1){
	$pageIndex = 1;
}

if($pageSize <1){
	$pageSize = 10;
}

$cityId = $context->getInternalDataValue("cityId");

$task = new COChefSearchTask();

$task->classifyId = $context->getInputDataValue("classifyId");
$task->areaId = $context->getInputDataValue("areaId");
$task->cuisineId = $context->getInputDataValue("cuisineId");
$task->rank = $context->getInputDataValue("rank");
$task->sort = $context->getInputDataValue("sort");
$task->minPrice = $context->getInputDataValue("minPrice");
$task->maxPrice = $context->getInputDataValue("maxPrice");
$task->pageIndex = $pageIndex;
$task->pageSize = 10;

$context->handle("COChefSearchTask", $task);

$chefs = $context->getOutputDataValue("chef-search-results");

if(!$chefs){
	$chefs = array();
}

header("Content-Type: text/xml; charset=utf-8");
header("VTDOMDocumentVersion: 1.0.0");

echo '<?xml version="1.0" encoding="UTF-8"?>';

?>

<list width="100%" height="100%" background-color="#eae9e0">
	
	<?php 
		
		$baseUri = "root:///root/tab/";
		
		foreach($chefs as $chef) {
		
			require "chef_item_android.php";
		}	
	?>
	
	<div width="100%" height="44"></div>
	
</list>

