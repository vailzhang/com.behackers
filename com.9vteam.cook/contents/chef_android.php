<?php

$library = "../../";

require_once "$library/com.9vteam.cook/COContext.php";

$context = new COContext();

$chef = null;
$packages = null;

$commentUrl ="chef/chef-comment?";

$task = new COChefGetTask();
$task->pid = $context->getInputDataValue("chefId");
$task->code = $context->getInputDataValue("code");

if($task->pid !== null){
	$commentUrl .= "chefId=".$task->pid;
}
else if($task->code !== null){
	$commentUrl .= "code=".$task->code;
}

$context->handle("COChefGetTask", $task);

$chef = $task->results;
$packages = array();
$title ="";
$area = "";

$cityId = $context->getInputDataValue("cityId");
$packageId = $context->getInputDataValue("packageId");

if($chef){
	
	$task = new COChefGetPackagesTask();
	$task->chefId = $chef["pid"];
	
	$context->handle("COChefGetPackagesTask", $task);
	
	if($task->results){
		$packages = $task->results;
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

header("Content-Type: text/xml; charset=utf-8");
header("VTDOMDocumentVersion: 1.0.0");

echo '<?xml version="1.0" encoding="UTF-8"?>';

?>

<list width="100%" height="100%" background-color="#eae9e0" id="packages">
	
	<?php if($chef) {?>
	
	
	<div width="100%" height="auto" id="chef" chefId="<?php echo $chef["pid"]; ?>" background-color="#f7f8f0" padding-bottom="10" reuse="user">
	
		<img left="10" top="10" background-color="#ffffff" src="<?php echo CKURL(COObjectStringValueForKey($chef,"logo"),160)?>" width="100" height="100"></img>
		
		<div left="120" top="10" width="100%-130" height="auto" layout="flow">
		
			<label width="100%" height="20" font="16" color="#333333"><?php echo $chef["name"]?></label>
			<div width="100%" height="22">
				<img width="22" height="22" src="<?php echo COObjectBooleanValueForKey($chef,"hasSecurityIDCode") ? '@com.vteam.cook.R.drawable.sec_id' :'@com.vteam.cook.R.drawable.sec_id_dis';?>"></img>
			</div>
			<label width="100%" height="auto" margin-top="2" font="16 bold" color="#333333"><?php echo $title;?></label>
			<label width="100%" height="auto" margin-top="2" font="12" color="#69584f"><?php echo $area;?></label>
			<?php if( COObjectIntValueForKey($chef,"scoreCount") == 0) {?>
			<div width="100%" height="22" margin-top="2">
			<?php } else {?>
			<action width="100%" height="22" action-name="url" uri="<?php echo $commentUrl;?>">
			<?php }?>
			
				<view id='star' viewClass='com.vteam.cook.views.StarView' score='<?php echo $chef["score"];?>' left='0' top='0' height='20' width='80'></view>
				<label left='86' top='2' height='auto' width='auto' font='14 bold' color='#82736a'>(<?php echo COObjectIntValueForKey($chef,"scoreCount") ;?>)</label>
			
			<?php if( COObjectIntValueForKey($chef,"scoreCount") == 0) {?>
			</div>
			<?php } else {?>
			</action>
			<?php }?>
		</div>
		
	</div>
	
	<?php 
		foreach($packages as $package) {
			
			$task = new COPackageGetFoodsTask();

			$task->pid = $package["pid"];
			
			$context->handle("COPackageGetFoodsTask", $task);
			
			$foods = $task->results ? $task->results : array();
			
			$food = "";
			
			foreach ($foods as $f){
				if(strlen($food)){
					$food .= "、".$f->title;
				}
				else {
					$food .= $f->title;
				}
			}
			
			if($packageId === null){
				$packageId = $package["pid"];
			}
	?>
	
	<div width="100%" height="auto" padding-top="10" padding-left="10" padding-right="10" packageId="<?php echo $package["pid"];?>">
	<status action-name="package" status="<?php echo $package["pid"] == $packageId ? "selected":"";?>" reuse="package"
		 packageId="<?php echo $package["pid"];?>" background-color-selected="#ffffff"
		 width="100%" height="auto" background-color="#f7f8f0" >
		<div width="100%" height="auto" padding="10" layout="flow">
			<label width="100%" height="24" font="14" color="#333333" align="center"><![CDATA[¥<?php echo $package["chefPrice"];?> / <?php echo $package["format"];?>]]></label>
			<label width="100%" height="auto" min-height="24" font="16" align="center" color="#e24500" break-mode="wrap"><?php echo $package["summary"];?></label>
			<label width="100%" height="auto" font="14" align="center" color="#333333"><?php echo $food;?></label>
		</div>
		<img status="selected" src="@com.vteam.cook.R.drawable.sel" width="16" height="16" left="auto" right="6"></img>
	</status>
	</div>
	
	<?php }?>
	
	<div width="100%" height="auto" padding-top="10" padding-left="10" padding-right="10">
	<div width="100%" height="auto" background-color="#f7f8f0" layout="flow"  padding="10" reuse="order">
		
		<div width="100%" height="20">
			<label width="100%" height="20" align="center" font="14" color="#333333">预约厨师</label>
		</div>
		
		<hscroll id="orderDay" width="100%" height="44" clips="true" margin="4">
			<?php 
			
				$t = strtotime(date("Y-m-d",time()));
				$days = 31;
				$weeks = array("周日","周一","周二","周三","周四","周五","周六");
				for($i = 1;$i<$days;$i ++){
					$time = $t + $i * 24 * 3600;
					$w = date("w",$time);
	
			?>
					<status action-name="orderDay" status="<?php echo $i ==1 ?"selected":"";?>" meal="noon|dinner" day="<?php echo date("Y-m-d",$time);?>" width="64" height="100%">
						<div width="100%" height="100%" status="selected" background-color="#ffffff"></div>
						<label width="100%" height="20" top="4" font="14 bold" color="#e24500" align="center"><?php echo $weeks[$w];?></label>
						<label width="100%" height="20" top="24" font="12" color="#333333" align="center"><?php echo date("m月d日",$time);?></label>
					</status>		
			<?php
				}
				
					
			?>
		</hscroll>
		<div id="orderMeal" width="100%" height="32" padding-left="40" padding-right="40" layout="flow" margin-top="10">
			<status action-name="orderMeal" meal="noon" status="selected" width="50%" height="100%">
				<div width="100%" height="100%" status="selected" background-color="#ffffff"></div>
				<label width="100%" height="20" top="auto" bottom="auto" font="14 bold" color="#e24500" align="center">午餐</label>
			</status>
			<status action-name="orderMeal" meal="dinner" status="" width="50%" height="100%">
				<div width="100%" height="100%" status="selected" background-color="#ffffff"></div>
				<label width="100%" height="20" top="auto" bottom="auto" font="14 bold" color="#e24500" align="center">晚餐</label>
			</status>
		</div>
		
		<div id="orderFood" width="100%" height="32" padding-left="40" padding-right="40" layout="flow" margin-top="10">
			<status action-name="orderFood" food="none" status="selected" width="50%" height="100%">
				<div width="100%" height="100%" status="selected" background-color="#ffffff"></div>
				<label width="100%" height="20" top="auto" bottom="auto" font="14 bold" color="#e24500" align="center">自备食材</label>
			</status>
			<status action-name="orderFood" food="normal" status="" width="50%" height="100%">
				<div width="100%" height="100%" status="selected" background-color="#ffffff"></div>
				<label width="100%" height="20" top="auto" bottom="auto" font="14 bold" color="#e24500" align="center">代买食材</label>
			</status>
		</div>
		
		<action action-name="orderCreate" width="100%" height="44" margin-left="20" margin-right="20" margin-top="20" background-color="#e43a00">
			<label width="auto" height="auto" left="auto" right="auto" top="auto" bottom="auto" color="#ffffff" font="18 bold"><![CDATA[预  约]]></label>
		</action>
		
	</div>
	</div>
	
	<div width="100%" height="44"></div>
	
	<?php }?>
	
</list>
