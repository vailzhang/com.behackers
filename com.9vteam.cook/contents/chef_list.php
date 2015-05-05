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

header("VTDOMDocumentVersion: 1.0.0");

?>

<vscroll width="100%" height="100%" background-color="#eae9e0" padding-left="10" padding-right="10" clips="true">
	
	
	<status width="100%" height="33" margin-top="-33" target="top" reuse="toploading">
		<view viewClass="CKAnimationView" id="imageView" src="@dd.png" width="22" height="33" top="0" left="auto" right="auto"></view>
	</status>
	
	<?php 
		
		$baseUri = "chef-list/";
		
		foreach($chefs as $chef) {
		
			require "chef_item.php";
		}	
	?>
	
	<div width="100%" height="44"></div>
	
</vscroll>

