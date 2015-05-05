<?php
if($chef && $baseUri){
	$package = isset( $chef["package"] ) ? $chef["package"] : array();
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
	$foods = isset($package["foods"]) ? $package["foods"] : array();
	$food = "";
	foreach ($foods as $f){
		if(strlen($food)){
			$food .= "、".$f->title;
		}
		else {
			$food .= $f->title;
		}
	}

?>

<action action-name="url" uri="<?php echo $baseUri;?>chef?title=<?php echo urlencode("预约厨师");?>&chefId=<?php echo $chef["pid"];?>&packageId=<?php echo $package["pid"]; ?>" width="100%" height="auto" layout="flow" background-color="#f7f8f0" margin-top="10">
	<div width="100%" height="80" padding="10">
		<img background-color="#ffffff" src="<?php echo isset($chef["logo"]) ? CKURL($chef["logo"],160) : '';?>" width="60" height="60"></img>
		<label width="100%-80" height="18" left="70" font="16 bold" color="#333333"><?php echo $chef["name"];?></label>
		<view id='star' viewClass='CKStarView' score='<?php echo $chef["score"];?>' left='70' top='45' height='20' width='80'></view>
		<label left='156' top='46' height='auto' width='auto' font='14 bold' color='#82736a'>(<?php echo COObjectIntValueForKey($chef,"scoreCount") ;?>)</label>
		<img width="22" height="22" left="70" top="20" src="<?php echo COObjectBooleanValueForKey($chef,"hasSecurityIDCode") ? '@sec_id.png' :'@sec_id_dis.png';?>"></img>
	</div>
	<div width="100%" height="1" background-color="#eae9e0"></div>
	<div width="100%" height="auto" padding="10" layout="flow">
		<label width="100%" height="24" font="14" color="#333333" align="center">¥<?php echo COObjectStringValueForKey($package,"chefPrice");?>&nbsp;/&nbsp;<?php echo COObjectValueForKey($package, "format");?></label>
		<label width="100%" margin-top="4" height="auto" font="16" align="center" color="#e24500"><?php echo $title;?></label>
		<label width="100%" margin-top="4" height="auto" font="12" align="center" color="#69584f"><?php echo $area;?></label>
		<label width="100%" margin-top="4" height="auto" font="14" align="center" color="#333333"><?php echo $food;?></label>
	</div>
</action>

<?php 
}
?>