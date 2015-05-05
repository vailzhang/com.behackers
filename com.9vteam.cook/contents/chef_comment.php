<?php

$library = "../../";

require_once "$library/com.9vteam.cook/COContext.php";

$context = new COContext();

$pageIndex = intval( $context->getInputDataValue("pageIndex") );
$pageSize = intval( $context->getInputDataValue("pageSize") );

if($pageIndex < 1){
	$pageIndex = 1;
}

if($pageSize < 1){
	$pageSize = 10;
}

$reloadURL = "?pageSize=".$pageSize."&pageIndex=".($pageIndex + 1);

$task = new COChefGetTask();
$task->pid = $context->getInputDataValue("chefId");
$task->code = $context->getInputDataValue("code");

if($task->pid){
	$reloadURL .="&chefId=".intval($task->pid);
}

if($task->code){
	$reloadURL .="&code=".urlencode($task->code);
}

$context->handle("COChefGetTask", $task);

$chef = $task->results;
$title ="";
$area = "";

$comments = array();

$cityId = $context->getInputDataValue("cityId");

if($cityId){
	$reloadURL .="&cityId=".intval($cityId);
}

if($chef){

	
	$t = new CommentQueryTask();
	$t->ttype = CommentEntityChefType;
	$t->tid = $chef["pid"];
	$t->pageIndex = $pageIndex;
	$t->pageSize = $pageSize;
	
	$context->handle("CommentQueryTask", $t);
	
	if($t->results){
		$comments = $t->results;
	}
	
	$title = isset($chef["title"]) ? $chef["title"] :"";
	$cuisines = isset($chef["cuisines"]) ? $chef["cuisines"] : array();
	foreach ($cuisines as $cuisine){
		if(strlen($title)){
			$title .= "、".$cuisine->title;
		}
		else {
			$title .= $cuisine->title;
		}
	}
	$areas = isset($chef["areas"]) ? $chef["areas"] : array();
	$area ="";
	foreach ($areas as $a){
	
		if($cityId && $a->pcid != $cityId){
			continue;
		}
			
		if(strlen($area)){
			$area .= "、".$a->title;
		}
		else {
			$area .= $a->title;
		}
	}
}

header("VTDOMDocumentVersion: 1.0.0");

?>

<?php if($pageIndex == 1) {?>
<vscroll width="100%" height="100%" background-color="#eae9e0" clips="true">
	
	
	<status width="100%" height="33" margin-top="-33" target="top" reuse="toploading">
		<view viewClass="CKAnimationView" id="imageView" src="@dd.png" width="22" height="33" top="0" left="auto" right="auto"></view>
	</status>
<?php }?>
	
	<?php if($chef) {?>
	
	<?php if($pageIndex == 1) {?>
	<div width="100%" height="auto" layout="flow">
		<div width="100%" height="auto" background-color="#f7f8f0" padding-bottom="10" reuse="user">
		
			<img left="10" top="10" background-color="#ffffff" src="<?php echo CKURL(COObjectStringValueForKey($chef,"logo"),160)?>" width="100" height="100"></img>
			
			<div left="120" top="10" width="100%-130" height="auto" layout="flow">
			
				<label width="100%" height="20" font="16" color="#333333"><?php echo $chef["name"]?></label>
				<div width="100%" height="22">
					<img width="22" height="22" src="<?php echo COObjectBooleanValueForKey($chef,"hasSecurityIDCode") ? '@sec_id.png' :'@sec_id_dis.png';?>"></img>
				</div>
				<label width="100%" height="20" font="16 bold" color="#333333"><?php echo $title;?></label>
				<label width="100%" height="20" font="12" color="#69584f"><?php echo $area;?></label>
				<div width="100%" height="22">
					<view id='star' viewClass='CKStarView' score='<?php echo $chef["score"];?>' left='0' top='0' height='20' width='80'></view>
					<label left='86' top='2' height='auto' width='auto' font='14 bold' color='#82736a'>(<?php echo COObjectIntValueForKey($chef,"scoreCount") ;?>)</label>
				</div>
			</div>
			
		</div>
		<img width="100%" height="1" src="@l.png" gravity="resize"></img>
	</div>
	<?php }?>
	
	<?php if($pageIndex == 1 && count($comments) ==0) {?>
	<div width="100%" height="24">
		<label width="auto" height="auto" top="auto" bottom="auto" left="auto" right="auto" font="16" color="#999999">暂无评论</label>
	</div>
	<?php }?>
	
	<?php 
		
		foreach($comments as $comment) {
			require "chef_comment_item.php";
		}	
	?>
	
	<?php if(count($comments) == $pageSize) {?>
	<status target="bottom" width="100%" height="44" url="<?php echo $reloadURL;?>" method="replace" group="comment" name="comment">
		<div status="bottomover" width="100%" height="100%">
			<label width="auto" height="auto" left="auto" right="auto" top="auto" bottom="auto" color="#999999" font="14">加载更多</label>
		</div>
		<div status="bottom" width="100%" height="100%">
			<label width="auto" height="auto" left="auto" right="auto" top="auto" bottom="auto" color="#999999" font="14">加载更多</label>
		</div>
		<div status="loading" width="100%" height="100%">
			<label width="auto" height="auto" left="auto" right="auto" top="auto" bottom="auto" color="#999999" font="14">加载中...</label>
		</div>
	</status>
	<?php }?>
	
	<?php }?>
	
<?php if($pageIndex == 1) {?>
</vscroll>
<?php }?>
